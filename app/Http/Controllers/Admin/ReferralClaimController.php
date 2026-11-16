<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Scopes\SchoolScope;
use App\SubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralClaimController extends Controller
{
    /**
     * Display all school referral claims and commission pool summary for Admin.
     */
    public function index(Request $request): View
    {
        $statusFilter = (string) $request->query('status', 'all');

        $schools = School::query()
            ->with('owner')
            ->orderBy('name')
            ->get();

        // Collect all referred school IDs across the platform
        $referredSchools = $schools->filter(fn (School $s) => ! empty($s->meta['referred_by_school_id']));
        $referredSchoolIds = $referredSchools->pluck('id');

        $totalReferralRevenue = $referredSchoolIds->isEmpty()
            ? 0
            : (int) PaymentOrder::withoutGlobalScope(SchoolScope::class)
                ->whereIn('school_id', $referredSchoolIds)
                ->where('status', 'paid')
                ->sum('amount_idr');

        $totalCommissionPool = (int) round($totalReferralRevenue * 0.10);

        $allClaims = collect();
        $pendingAmount = 0;
        $paidOutAmount = 0;
        $pendingCount = 0;

        foreach ($schools as $school) {
            $claims = is_array($school->meta['referral_claims'] ?? null) ? $school->meta['referral_claims'] : [];
            foreach ($claims as $claim) {
                $status = (string) ($claim['status'] ?? 'Sedang Diproses');
                $amount = (int) ($claim['amount'] ?? 0);
                $statusLower = strtolower($status);

                if (in_array($statusLower, ['selesai', 'dicairkan', 'disetujui'], true)) {
                    $paidOutAmount += $amount;
                } elseif (! in_array($statusLower, ['ditolak', 'batal'], true)) {
                    $pendingAmount += $amount;
                    $pendingCount++;
                }

                $allClaims->push([
                    'school' => $school,
                    'id' => (string) ($claim['id'] ?? '-'),
                    'date' => (string) ($claim['date'] ?? '-'),
                    'type' => (string) ($claim['type'] ?? 'cash'),
                    'amount' => $amount,
                    'bank_name' => (string) ($claim['bank_name'] ?? '-'),
                    'account_number' => (string) ($claim['account_number'] ?? '-'),
                    'account_holder' => (string) ($claim['account_holder'] ?? '-'),
                    'status' => $status,
                    'admin_notes' => (string) ($claim['admin_notes'] ?? ''),
                    'processed_at' => (string) ($claim['processed_at'] ?? ''),
                ]);
            }
        }

        if ($statusFilter === 'pending') {
            $allClaims = $allClaims->filter(fn (array $c) => strtolower($c['status']) === 'sedang diproses');
        } elseif ($statusFilter === 'approved') {
            $allClaims = $allClaims->filter(fn (array $c) => in_array(strtolower($c['status']), ['selesai', 'dicairkan', 'disetujui'], true));
        } elseif ($statusFilter === 'rejected') {
            $allClaims = $allClaims->filter(fn (array $c) => strtolower($c['status']) === 'ditolak');
        }

        // Top referrer schools summary
        $topReferrers = $schools
            ->map(function (School $school) {
                $invited = $school->getReferredSchools();

                return [
                    'school' => $school,
                    'invited_count' => $invited->count(),
                    'active_count' => $invited->filter(fn (School $s) => $s->hasActiveSubscription())->count(),
                    'total_earnings' => $school->getReferralEarnings(),
                    'available_balance' => $school->getAvailableReferralBalance(),
                ];
            })
            ->filter(fn (array $row) => $row['invited_count'] > 0 || $row['total_earnings'] > 0)
            ->sortByDesc('total_earnings')
            ->values();

        return view('admin.referrals.index', [
            'claims' => $allClaims->reverse()->values(),
            'statusFilter' => $statusFilter,
            'totalReferralRevenue' => $totalReferralRevenue,
            'totalCommissionPool' => $totalCommissionPool,
            'pendingAmount' => $pendingAmount,
            'paidOutAmount' => $paidOutAmount,
            'pendingCount' => $pendingCount,
            'topReferrers' => $topReferrers,
        ]);
    }

    /**
     * Approve (mark paid / apply subscription extension) or reject a school's referral claim.
     */
    public function updateStatus(Request $request, School $school, string $claimId): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:approve,reject'],
            'admin_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $meta = $school->meta ?? [];
        $claims = is_array($meta['referral_claims'] ?? null) ? $meta['referral_claims'] : [];
        $foundIndex = null;

        foreach ($claims as $index => $claim) {
            if (($claim['id'] ?? '') === $claimId) {
                $foundIndex = $index;
                break;
            }
        }

        if ($foundIndex === null) {
            return back()->with('error', 'Data pengajuan klaim tidak ditemukan.');
        }

        $targetClaim = $claims[$foundIndex];
        $amount = (int) ($targetClaim['amount'] ?? 0);
        $type = (string) ($targetClaim['type'] ?? 'cash');

        if ($validated['action'] === 'approve') {
            $claims[$foundIndex]['status'] = 'Dicairkan';
            $claims[$foundIndex]['processed_at'] = now()->translatedFormat('d M Y H:i');
            $claims[$foundIndex]['admin_notes'] = $validated['admin_notes'] ?? ($type === 'discount_voucher'
                ? 'Konversi perpanjangan masa aktif langganan otomatis.'
                : 'Dana komisi telah ditransfer ke rekening tujuan.');

            // If voucher claim, automatically extend or activate school subscription proportionally (Rp 100.000 = 30 days)
            if ($type === 'discount_voucher' && $amount > 0) {
                $bonusDays = max(15, (int) round(($amount / 100000) * 30));
                $activeSub = $school->subscriptions()
                    ->withoutGlobalScope(SchoolScope::class)
                    ->where('status', SubscriptionStatus::Active->value)
                    ->where('ends_at', '>', now())
                    ->latest('ends_at')
                    ->first();

                if ($activeSub && $activeSub->ends_at) {
                    $activeSub->update([
                        'ends_at' => $activeSub->ends_at->copy()->addDays($bonusDays),
                    ]);
                } else {
                    $defaultPlan = Plan::query()->where('is_active', true)->orderBy('price_idr')->first();
                    if ($defaultPlan) {
                        $school->subscriptions()->create([
                            'plan_id' => $defaultPlan->id,
                            'status' => SubscriptionStatus::Active,
                            'starts_at' => now(),
                            'ends_at' => now()->addDays($bonusDays),
                            'activated_at' => now(),
                            'meta' => ['source' => 'referral_voucher_claim', 'claim_id' => $claimId],
                        ]);
                        $school->grantPlanAppAccess($defaultPlan);
                    }
                }
            }

            $meta['referral_claims'] = $claims;
            $school->update(['meta' => $meta]);

            return redirect()
                ->route('admin.referrals.index')
                ->with('status', "Klaim {$claimId} milik {$school->name} berhasil disetujui (Rp ".number_format($amount, 0, ',', '.').').');
        }

        // Action === 'reject': mark as Ditolak so getClaimedReferralAmount() automatically restores balance to the school
        $claims[$foundIndex]['status'] = 'Ditolak';
        $claims[$foundIndex]['processed_at'] = now()->translatedFormat('d M Y H:i');
        $claims[$foundIndex]['admin_notes'] = $validated['admin_notes'] ?: 'Ditolak oleh Admin — silakan periksa kembali data rekening tujuan.';

        $meta['referral_claims'] = $claims;
        $school->update(['meta' => $meta]);

        return redirect()
            ->route('admin.referrals.index')
            ->with('status', "Klaim {$claimId} milik {$school->name} ditolak. Saldo sebesar Rp ".number_format($amount, 0, ',', '.').' otomatis dikembalikan ke Saldo Siap Klaim sekolah.');
    }
}
