<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AppRequest;
use App\Models\PaymentOrder;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Scopes\SchoolScope;
use App\SubscriptionStatus;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $schoolsCount = School::query()->count();
        $activeSubs = Subscription::query()
            ->withoutGlobalScope(SchoolScope::class)
            ->where('status', SubscriptionStatus::Active)
            ->where('ends_at', '>', now())
            ->count();
        $pendingOrders = PaymentOrder::query()
            ->withoutGlobalScope(SchoolScope::class)
            ->where('status', PaymentStatus::Pending)
            ->count();
        $pendingProofOrders = PaymentOrder::query()
            ->withoutGlobalScope(SchoolScope::class)
            ->where('status', PaymentStatus::Pending)
            ->whereNotNull('proof_path')
            ->count();
        $paidThisMonth = PaymentOrder::query()
            ->withoutGlobalScope(SchoolScope::class)
            ->where('status', PaymentStatus::Paid)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount_idr');

        $activeUsers7d = User::query()
            ->where('role', '!=', User::ROLE_ADMIN)
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        $totalSchoolUsers = User::query()->where('role', '!=', User::ROLE_ADMIN)->count();
        $totalOrders = PaymentOrder::query()
            ->withoutGlobalScope(SchoolScope::class)
            ->count();
        $pendingAppRequests = AppRequest::query()->where('status', 'pending')->count();
        $incompleteProfiles = School::query()
            ->where(function ($query) {
                $query->whereNull('npsn')->orWhereNull('city');
            })
            ->count();

        $pendingReferralClaims = School::query()
            ->whereNotNull('meta')
            ->get(['id', 'meta'])
            ->sum(function (School $s): int {
                $claims = is_array($s->meta['referral_claims'] ?? null) ? $s->meta['referral_claims'] : [];

                return collect($claims)
                    ->filter(fn ($c) => strtolower((string) ($c['status'] ?? 'sedang diproses')) === 'sedang diproses')
                    ->count();
            });

        $schoolsWithAppActivity = School::query()
            ->whereNotNull('last_app_access_at')
            ->where('last_app_access_at', '>=', now()->subDays(7))
            ->count();

        $recentSchools = School::query()->with('owner')->latest('id')->limit(8)->get();
        $recentOrders = PaymentOrder::query()->with(['school', 'plan'])->latest('id')->limit(8)->get();
        $activeAppSchools = School::query()
            ->whereNotNull('last_app_access_at')
            ->where('last_app_access_at', '>=', now()->subDays(7))
            ->with('owner')
            ->orderByDesc('last_app_access_at')
            ->limit(8)
            ->get();

        $registrationTraffic = $this->registrationTraffic();
        $revenueByMonth = $this->revenueByMonth();
        $registrationsThisMonth = (int) $registrationTraffic->sum('count');
        $revenueThisMonth = (int) $revenueByMonth->last()['amount'];

        return view('admin.dashboard', [
            'schoolsCount' => $schoolsCount,
            'activeSubs' => $activeSubs,
            'pendingOrders' => $pendingOrders,
            'pendingProofOrders' => $pendingProofOrders,
            'pendingReferralClaims' => $pendingReferralClaims,
            'paidThisMonth' => (int) $paidThisMonth,
            'activeUsers7d' => $activeUsers7d,
            'totalSchoolUsers' => $totalSchoolUsers,
            'totalOrders' => $totalOrders,
            'pendingAppRequests' => $pendingAppRequests,
            'incompleteProfiles' => $incompleteProfiles,
            'schoolsWithAppActivity' => $schoolsWithAppActivity,
            'recentSchools' => $recentSchools,
            'recentOrders' => $recentOrders,
            'activeAppSchools' => $activeAppSchools,
            'registrationTraffic' => $registrationTraffic,
            'revenueByMonth' => $revenueByMonth,
            'registrationsThisMonth' => $registrationsThisMonth,
            'revenueThisMonth' => $revenueThisMonth,
        ]);
    }

    /**
     * Pendaftar (sekolah baru) per hari, 30 hari terakhir.
     *
     * @return Collection<int, array{date: string, label: string, count: int}>
     */
    private function registrationTraffic(): Collection
    {
        $days = 30;
        $today = now()->startOfDay();
        $start = $today->copy()->subDays($days - 1);

        $counts = School::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        return Collection::make(range(0, $days - 1))
            ->map(function (int $offset) use ($counts, $days) {
                $date = now()->startOfDay()->subDays($days - 1 - $offset);
                $key = $date->toDateString();

                return [
                    'date' => $key,
                    'label' => $date->format('d M'),
                    'count' => (int) ($counts[$key] ?? 0),
                ];
            });
    }

    /**
     * Pendapatan (order lunas) per bulan, 6 bulan terakhir.
     *
     * @return Collection<int, array{key: string, label: string, amount: int, orders: int}>
     */
    private function revenueByMonth(): Collection
    {
        $months = 6;
        $start = now()->startOfMonth()->subMonths($months - 1);

        $rows = PaymentOrder::query()
            ->withoutGlobalScope(SchoolScope::class)
            ->where('status', PaymentStatus::Paid)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $start)
            ->get()
            ->groupBy(fn (PaymentOrder $o) => $o->paid_at->format('Y-m'))
            ->map(fn ($group) => (object) [
                'month' => $group->first()->paid_at->format('Y-m'),
                'amount' => $group->sum('amount_idr'),
                'orders' => $group->count(),
            ]);

        return Collection::make(range(0, $months - 1))
            ->map(function (int $offset) use ($rows, $months) {
                $date = now()->startOfMonth()->subMonths($months - 1 - $offset);
                $key = $date->format('Y-m');

                return [
                    'key' => $key,
                    'label' => $date->translatedFormat('M Y'),
                    'amount' => (int) ($rows[$key]->amount ?? 0),
                    'orders' => (int) ($rows[$key]->orders ?? 0),
                ];
            });
    }
}
