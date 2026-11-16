<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Services\MidtransService;
use App\SubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function show(Request $request, string $planCode): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->canAccessBilling()) {
            return redirect()->route('dashboard')->with('error', 'Menu Checkout & Pembayaran khusus untuk Kepala Sekolah dan Bendahara BOS.');
        }

        $plan = Plan::findByCode($planCode);

        if (! $plan) {
            abort(404);
        }

        $school = $user->school()->first() ?? School::where('owner_user_id', $user->id)->first();
        $availableReferralBalance = $school ? $school->getAvailableReferralBalance() : 0;

        return view('checkout.show', [
            'plan' => $plan,
            'school' => $school,
            'availableReferralBalance' => $availableReferralBalance,
            'plans' => Plan::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'currentPlan' => $school?->activeSubscription()->first()?->plan,
        ]);
    }

    public function store(Request $request, MidtransService $midtrans): RedirectResponse
    {
        $user = $request->user();

        if (! $user->canAccessBilling()) {
            abort(403, 'Hanya Kepala Sekolah atau Bendahara BOS yang dapat melakukan pemesanan paket.');
        }

        $validated = $request->validate([
            'plan_code' => ['required', 'string', 'exists:plans,code'],
            'school_name' => ['required', 'string', 'max:255'],
            'school_email' => ['required', 'string', 'email', 'max:255'],
            'school_phone' => ['required', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:120'],
            'npsn' => ['nullable', 'string', 'max:32'],
            'payment_method' => ['nullable', 'string', 'in:midtrans,manual_transfer'],
            'use_referral_balance' => ['nullable', 'boolean'],
        ]);

        $plan = Plan::findByCode($validated['plan_code']);

        if (! $plan) {
            throw ValidationException::withMessages([
                'plan_code' => 'Paket tidak ditemukan atau tidak aktif.',
            ]);
        }

        $school = $user->school()->first() ?? School::where('owner_user_id', $user->id)->first();

        if ($school) {
            $school->update([
                'name' => $validated['school_name'],
                'email' => $validated['school_email'],
                'phone' => $validated['school_phone'],
                'city' => $validated['city'] ?? $school->city,
                'npsn' => $validated['npsn'] ?? $school->npsn,
            ]);
        } else {
            $school = School::query()->create([
                'owner_user_id' => $user->id,
                'name' => $validated['school_name'],
                'email' => $validated['school_email'],
                'phone' => $validated['school_phone'],
                'city' => $validated['city'] ?? null,
                'npsn' => $validated['npsn'] ?? null,
            ]);
        }

        if ((int) $user->school_id !== (int) $school->id) {
            $user->forceFill(['school_id' => $school->id])->save();
        }

        $discountAmount = 0;
        if (! empty($validated['use_referral_balance'])) {
            $availableBalance = $school->getAvailableReferralBalance();
            if ($availableBalance > 0) {
                $discountAmount = min($availableBalance, max(0, $plan->price_idr - 10000));
            }
        }

        $finalAmountIdr = max(10000, $plan->price_idr - $discountAmount);
        $paymentMethod = $validated['payment_method'] ?? 'midtrans';

        if ($paymentMethod === 'manual_transfer') {
            $subscription = Subscription::query()->create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Pending,
            ]);

            $orderId = 'VEY-'.now()->format('YmdHis').'-'.strtoupper(Str::random(6));

            $order = PaymentOrder::query()->create([
                'order_id' => $orderId,
                'subscription_id' => $subscription->id,
                'school_id' => $school->id,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount_idr' => $finalAmountIdr,
                'status' => PaymentStatus::Pending,
                'payment_method' => 'manual_transfer',
                'notes' => $discountAmount > 0 ? 'Potongan Komisi Referral: Rp '.number_format($discountAmount, 0, ',', '.') : null,
                'expired_at' => now()->addDays(7),
            ]);

            if ($discountAmount > 0) {
                $this->recordReferralDiscountClaim($school, $discountAmount, $orderId);
            }

            return redirect()->route('pembayaran.show', $order);
        }

        $discountNotes = $discountAmount > 0
            ? 'Potongan Komisi Referral: Rp '.number_format($discountAmount, 0, ',', '.')
            : null;

        try {
            $order = $midtrans->createSnapOrder($school, $plan, $user, $finalAmountIdr, $discountNotes);
            if ($discountAmount > 0) {
                $this->recordReferralDiscountClaim($school, $discountAmount, $order->order_id);
            }
        } catch (RuntimeException $e) {
            return back()->withErrors([
                'snap' => $e->getMessage(),
            ])->withInput();
        }

        return redirect()->route('pembayaran.show', $order);
    }

    private function recordReferralDiscountClaim(School $school, int $discountAmount, string $orderId): void
    {
        $meta = $school->meta ?? [];
        $claims = is_array($meta['referral_claims'] ?? null) ? $meta['referral_claims'] : [];
        $claims[] = [
            'id' => 'CLM-'.strtoupper(uniqid()),
            'date' => now()->translatedFormat('d M Y H:i'),
            'type' => 'discount_voucher',
            'amount' => $discountAmount,
            'bank_name' => 'Potongan Langsung Checkout ('.$orderId.')',
            'account_number' => $orderId,
            'account_holder' => $school->name,
            'status' => 'Dicairkan',
            'processed_at' => now()->translatedFormat('d M Y H:i'),
            'admin_notes' => 'Digunakan sebagai potongan harga langsung pada order '.$orderId,
        ];
        $meta['referral_claims'] = $claims;
        $school->update(['meta' => $meta]);
    }
}
