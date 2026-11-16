<?php

namespace Tests\Feature;

use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
        config(['midtrans.server_key' => null]);
    }

    public function test_pembayaran_page_requires_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $subscription = $school->subscriptions()->create([
            'plan_id' => Plan::findByCode('semester')->id,
            'status' => 'pending',
        ]);

        $order = PaymentOrder::factory()->create([
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $owner->id,
            'plan_id' => Plan::findByCode('semester')->id,
        ]);

        $this->actingAs($other)
            ->get(route('pembayaran.show', $order->order_id))
            ->assertNotFound();

        $this->actingAs($owner)
            ->get(route('pembayaran.show', $order->order_id))
            ->assertOk()
            ->assertSee($order->order_id);
    }

    public function test_guest_cannot_access_pembayaran(): void
    {
        $this->get('/pembayaran/VEY-TEST')->assertRedirect('/login');
    }

    public function test_register_then_checkout_redirects_with_validation_errors_when_midtrans_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout', [
            'plan_code' => 'super',
            'school_name' => 'SMK Negeri 5',
            'school_email' => 'smk5@sekolah.sch.id',
            'school_phone' => '085711112222',
        ]);

        $response->assertSessionHasErrors('snap');
        $this->assertDatabaseHas('schools', ['name' => 'SMK Negeri 5']);
    }

    public function test_checkout_creates_order_and_redirects_to_pembayaran_when_snap_succeeds(): void
    {
        config(['midtrans.server_key' => 'Mid-server-test-key']);
        Http::preventStrayRequests();
        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-abc',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/abc',
            ], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout', [
            'plan_code' => 'semester',
            'school_name' => 'SMA Snap',
            'school_email' => 'snap@sekolah.sch.id',
            'school_phone' => '081200001111',
        ]);

        $order = PaymentOrder::query()->latest('id')->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('pembayaran.show', $order->order_id));
        $this->assertSame('pending', $order->status->value);
        $this->assertSame('snap-token-abc', $order->snap_token);
        $this->assertSame(600000, $order->amount_idr);
        $this->assertDatabaseHas('schools', ['name' => 'SMA Snap']);
        $this->assertDatabaseHas('subscriptions', ['status' => 'pending']);

        Http::assertSentCount(1);
    }

    public function test_checkout_accepts_midtrans_201_created_response(): void
    {
        config(['midtrans.server_key' => 'Mid-server-test-key']);
        Http::preventStrayRequests();
        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-201',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/201',
            ], 201),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout', [
            'plan_code' => 'semester',
            'school_name' => 'SMA Dua Ratus Satu',
            'school_email' => '201@sekolah.sch.id',
            'school_phone' => '081200002222',
        ]);

        $order = PaymentOrder::query()->latest('id')->first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('pembayaran.show', $order->order_id));
        $this->assertSame('pending', $order->status->value);
        $this->assertSame('snap-token-201', $order->snap_token);
    }

    public function test_bendahara_can_access_school_orders_without_creating_duplicate_school(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_SCHOOL]);
        $school = School::factory()->create([
            'owner_user_id' => $owner->id,
            'name' => 'SMA Negeri 1 Bersama',
        ]);
        $owner->forceFill(['school_id' => $school->id])->save();

        $bendahara = User::factory()->create([
            'role' => User::ROLE_BENDAHARA,
            'school_id' => $school->id,
        ]);

        // Bendahara performs manual transfer checkout for the school
        $this->actingAs($bendahara)->post('/checkout', [
            'plan_code' => 'semester',
            'school_name' => 'SMA Negeri 1 Bersama',
            'school_email' => 'sman1@sekolah.sch.id',
            'school_phone' => '081233334444',
            'payment_method' => 'manual_transfer',
        ])->assertRedirect();

        // Still only 1 school in database (no duplicate created)
        $this->assertSame(1, School::count());

        $order = PaymentOrder::withoutGlobalScopes()->latest('id')->firstOrFail();

        // Both Owner and Bendahara can view the order and invoice
        $this->actingAs($owner)
            ->get(route('pembayaran.show', $order->order_id))
            ->assertOk();

        $this->actingAs($bendahara)
            ->get(route('pembayaran.show', $order->order_id))
            ->assertOk();

        $this->actingAs($bendahara)
            ->get(route('pembayaran.invoice', $order->order_id))
            ->assertOk();
    }
}
