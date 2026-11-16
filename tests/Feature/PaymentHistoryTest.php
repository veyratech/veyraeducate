<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('pembayaran.history'))->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_payment_history(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->update(['school_id' => $school->id]);

        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
        ]);

        $order = PaymentOrder::factory()->create([
            'order_id' => 'ORD-HIST-01',
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_idr' => $plan->price_idr,
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('pembayaran.history'));

        $response->assertOk();
        $response->assertSee('ORD-HIST-01');
        $response->assertSee($plan->label);
        $response->assertSee(route('pembayaran.invoice', $order->order_id));
        $response->assertSee('Riwayat Tagihan &amp; Kwitansi', false);
    }

    public function test_user_only_sees_their_own_orders(): void
    {
        $user1 = User::factory()->create();
        $school1 = School::factory()->create(['owner_user_id' => $user1->id]);
        $user1->update(['school_id' => $school1->id]);

        $user2 = User::factory()->create();
        $school2 = School::factory()->create(['owner_user_id' => $user2->id]);
        $user2->update(['school_id' => $school2->id]);

        $plan = Plan::findByCode('semester');

        $sub1 = $school1->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
        ]);

        $sub2 = $school2->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
        ]);

        $order1 = PaymentOrder::factory()->create([
            'order_id' => 'ORD-SCHOOL-ONE',
            'subscription_id' => $sub1->id,
            'school_id' => $school1->id,
            'user_id' => $user1->id,
            'plan_id' => $plan->id,
            'status' => PaymentStatus::Paid,
        ]);

        $order2 = PaymentOrder::factory()->create([
            'order_id' => 'ORD-SCHOOL-TWO',
            'subscription_id' => $sub2->id,
            'school_id' => $school2->id,
            'user_id' => $user2->id,
            'plan_id' => $plan->id,
            'status' => PaymentStatus::Paid,
        ]);

        $response = $this->actingAs($user1)->get(route('pembayaran.history'));

        $response->assertOk();
        $response->assertSee('ORD-SCHOOL-ONE');
        $response->assertDontSee('ORD-SCHOOL-TWO');
    }

    public function test_filter_by_status_works(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->update(['school_id' => $school->id]);

        $plan = Plan::findByCode('semester');

        $sub = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
        ]);

        $paidOrder = PaymentOrder::factory()->create([
            'order_id' => 'ORD-PAID-01',
            'subscription_id' => $sub->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => PaymentStatus::Paid,
        ]);

        $pendingOrder = PaymentOrder::factory()->create([
            'order_id' => 'ORD-PENDING-02',
            'subscription_id' => $sub->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => PaymentStatus::Pending,
        ]);

        $responsePaid = $this->actingAs($user)->get(route('pembayaran.history', ['status' => 'paid']));
        $responsePaid->assertOk();
        $responsePaid->assertSee('ORD-PAID-01');
        $responsePaid->assertDontSee('ORD-PENDING-02');

        $responsePending = $this->actingAs($user)->get(route('pembayaran.history', ['status' => 'pending']));
        $responsePending->assertOk();
        $responsePending->assertSee('ORD-PENDING-02');
        $responsePending->assertDontSee('ORD-PAID-01');
    }
}
