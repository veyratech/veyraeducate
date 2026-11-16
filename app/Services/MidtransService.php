<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\SubscriptionStatus;
use App\Support\MidtransConfig;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransService
{
    public function createSnapOrder(
        School $school,
        Plan $plan,
        Authenticatable $user,
        ?int $customAmountIdr = null,
        ?string $notes = null,
    ): PaymentOrder {
        $grossAmount = $customAmountIdr !== null ? max(10000, $customAmountIdr) : $plan->price_idr;

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
            'amount_idr' => $grossAmount,
            'status' => PaymentStatus::Pending,
            'notes' => $notes,
            'expired_at' => now()->addHours(24),
        ]);

        if (! config('midtrans.server_key')) {
            $order->update(['status' => PaymentStatus::Failed]);

            throw new RuntimeException('Kunci API Midtrans belum diatur. Buka Pengaturan - Koneksi Midtrans lalu simpan server key.');
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $school->name,
                'email' => $school->email ?? $user->email,
                'phone' => $school->phone ?? '-',
            ],
            'expiry' => [
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        $response = Http::withBasicAuth(config('midtrans.server_key'), '')
            ->withHeaders(['Accept' => 'application/json'])
            ->post(MidtransConfig::snapUrl(), $payload);

        if (! $response->successful()) {
            $order->update([
                'status' => PaymentStatus::Failed,
                'snap_response' => $response->json(),
            ]);

            if ($response->status() === 401) {
                $mode = MidtransConfig::isProduction() ? 'produksi' : 'sandbox';

                throw new RuntimeException(
                    "Midtrans menolak kunci API (Unauthorized) pada mode {$mode}. "
                    .'Periksa server key dan mode sandbox/produksi di Pengaturan - Koneksi Midtrans.'
                );
            }

            throw new RuntimeException('Gagal membuat Snap token Midtrans: '.$response->body());
        }

        $order->update([
            'snap_token' => $response->json('token'),
            'snap_response' => $response->json(),
        ]);

        return $order->fresh();
    }

    public function verifyNotificationSignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $serverKey = config('midtrans.server_key');

        if ($orderId === '' || $statusCode === '' || ! $serverKey) {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$serverKey);

        return hash_equals($expected, (string) ($payload['signature_key'] ?? ''));
    }
}
