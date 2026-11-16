<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolReferralController extends Controller
{
    /**
     * Display the school operator referral portal.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $school = $user->school()->first() ?? School::where('owner_user_id', $user->id)->first();

        if (! $school) {
            $school = School::create([
                'owner_user_id' => $user->id,
                'name' => 'Sekolah '.$user->name,
                'email' => $user->email,
                'meta' => [
                    'level' => 'SMA/SMK',
                    'status' => 'Swasta',
                    'academic_year' => '2026/2027',
                    'semester' => 'Ganjil',
                ],
            ]);
            $user->forceFill(['school_id' => $school->id])->save();
        }

        $referralCode = $school->referral_code;
        $referralLink = $school->referral_link;

        $referredSchools = $school->getReferredSchools();
        $totalInvited = $referredSchools->count();
        $totalSubscribed = $referredSchools->filter(fn (School $s) => $s->hasActiveSubscription())->count();
        $totalEarnings = $school->getReferralEarnings();
        $claimedAmount = $school->getClaimedReferralAmount();
        $availableBalance = $school->getAvailableReferralBalance();

        // Sample promo message for one-click WhatsApp sharing
        $shareText = "Halo Rekan Operator/Guru! 🎓\n\n"
            ."Gunakan ekosistem *Veyra Educate & CBT Online* untuk digitalisasi asesmen STS/SAS/Ujian Sekolah tanpa ribet server lokal dan bebas kertas fotokopi.\n\n"
            ."Daftarkan sekolah Anda melalui tautan resmi ini untuk mendapatkan prioritas aktivasi dan panduan migrasi gratis:\n"
            ."👉 {$referralLink}\n\n"
            ."Kode Referral: *{$referralCode}*";

        $waShareUrl = 'https://wa.me/?text='.urlencode($shareText);

        $claimHistory = array_reverse($school->meta['referral_claims'] ?? []);

        return view('school.referral.index', compact(
            'school',
            'referralCode',
            'referralLink',
            'referredSchools',
            'totalInvited',
            'totalSubscribed',
            'totalEarnings',
            'claimedAmount',
            'availableBalance',
            'shareText',
            'waShareUrl',
            'claimHistory'
        ));
    }

    /**
     * Submit payout claim or conversion to subscription extension.
     */
    public function claim(Request $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school()->first() ?? School::where('owner_user_id', $user->id)->firstOrFail();

        if (! $user->isAdmin() && (int) $user->id !== (int) $school->owner_user_id && ! in_array($user->role, ['school', 'operator', null, ''], true)) {
            abort(403, 'Hanya pemilik sekolah atau operator utama yang dapat mengajukan klaim komisi referral.');
        }

        $validated = $request->validate([
            'claim_type' => ['required', 'string', 'in:cash,discount_voucher'],
            'bank_name' => ['nullable', 'required_if:claim_type,cash', 'string', 'max:100'],
            'account_number' => ['nullable', 'required_if:claim_type,cash', 'string', 'max:50'],
            'account_holder' => ['nullable', 'required_if:claim_type,cash', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'claim_type.required' => 'Pilih jenis klaim reward referral.',
            'bank_name.required_if' => 'Nama Bank / E-Wallet wajib diisi untuk pencairan tunai.',
            'account_number.required_if' => 'Nomor rekening / E-Wallet wajib diisi untuk pencairan tunai.',
            'account_holder.required_if' => 'Nama pemilik rekening wajib diisi untuk pencairan tunai.',
        ]);

        $availableBalance = $school->getAvailableReferralBalance();
        if ($availableBalance < 50000) {
            return back()->with('error', 'Saldo komisi siap klaim belum mencukupi batas minimum penarikan (Min. Rp 50.000).');
        }

        $claims = $school->meta['referral_claims'] ?? [];
        $claims[] = [
            'id' => 'CLM-'.strtoupper(uniqid()),
            'date' => now()->translatedFormat('d M Y H:i'),
            'type' => $validated['claim_type'],
            'amount' => $availableBalance,
            'bank_name' => $validated['claim_type'] === 'cash' ? $validated['bank_name'] : 'Voucher Potongan Tagihan',
            'account_number' => $validated['claim_type'] === 'cash' ? $validated['account_number'] : '-',
            'account_holder' => $validated['claim_type'] === 'cash' ? $validated['account_holder'] : $school->name,
            'status' => 'Sedang Diproses',
        ];

        $meta = $school->meta ?? [];
        $meta['referral_claims'] = $claims;
        $school->update(['meta' => $meta]);

        return back()->with('success', 'Permintaan klaim reward referral sebesar Rp '.number_format($availableBalance, 0, ',', '.').' berhasil diajukan dan sedang diverifikasi oleh Tim Veyra.');
    }
}
