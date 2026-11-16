<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\Services\MidtransService;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
        config([
            'midtrans.server_key' => 'Mid-server-test-key',
            'midtrans.client_key' => 'Mid-client-test-key',
            'midtrans.is_production' => false,
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'ORDER-1',
            'status_code' => '200',
            'signature_key' => 'invalid',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }

    public function test_webhook_rejects_unknown_order(): void
    {
        $signature = hash('sha512', 'UNKNOWN-1'.'200'.'Mid-server-test-key');

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'UNKNOWN-1',
            'status_code' => '200',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(404);
    }

    public function test_valid_settlement_webhook_activates_subscription_and_is_idempotent(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $plan = Plan::findByCode('semester');

        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        $order = PaymentOrder::query()->create([
            'order_id' => 'VEY-TEST-001',
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_idr' => 600000,
            'status' => PaymentStatus::Pending,
            'expired_at' => now()->addDay(),
        ]);

        $payload = [
            'order_id' => 'VEY-TEST-001',
            'status_code' => '200',
            'transaction_status' => 'settlement',
            'transaction_id' => 'TRX-99',
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].'Mid-server-test-key');

        $first = $this->postJson('/webhooks/midtrans', $payload);
        $first->assertOk();

        $order->refresh();
        $subscription->refresh();

        $this->assertSame(PaymentStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('active', $subscription->status->value);
        $this->assertNotNull($subscription->ends_at);

        $paidAt = $order->paid_at;

        $second = $this->postJson('/webhooks/midtrans', $payload);
        $second->assertOk();
        $order->refresh();
        $this->assertTrue($paidAt->equalTo($order->paid_at));
    }

    public function test_expire_webhook_marks_order_expired(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        $order = PaymentOrder::query()->create([
            'order_id' => 'VEY-TEST-002',
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_idr' => 600000,
            'status' => PaymentStatus::Pending,
            'expired_at' => now()->addDay(),
        ]);

        $payload = [
            'order_id' => 'VEY-TEST-002',
            'status_code' => '407',
            'transaction_status' => 'expire',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].'Mid-server-test-key');

        $this->postJson('/webhooks/midtrans', $payload)->assertOk();

        $this->assertSame(PaymentStatus::Expired, $order->fresh()->status);
        $this->assertSame('pending', $subscription->fresh()->status->value);
    }

    public function test_signature_helper_matches_midtrans_algorithm(): void
    {
        $service = new MidtransService;
        $payload = [
            'order_id' => 'A1',
            'status_code' => '200',
            'signature_key' => hash('sha512', 'A1'.'200'.'Mid-server-test-key'),
        ];

        $this->assertTrue($service->verifyNotificationSignature($payload));
        $this->assertFalse($service->verifyNotificationSignature([
            'order_id' => 'A1',
            'status_code' => '200',
            'signature_key' => 'nope',
        ]));
    }
}
