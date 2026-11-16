<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedersAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_checkout_cta_when_no_subscription(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Belum Berlangganan');
        $response->assertSee('Pilih Paket Langganan');
        $response->assertSee('Server Online');
        $response->assertSee('Agenda Digital Sekolah');
        $response->assertSee('Panduan Terpopuler');
    }

    public function test_dashboard_shows_smart_renewal_alert_when_subscription_expires_soon(): void
    {
        $this->seed(PlanSeeder::class);
        $school = School::factory()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
        ]);

        $plan = Plan::first();
        Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDays(25),
            'ends_at' => now()->addDays(5),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Pengingat Perpanjangan');
        $response->assertSee('5 Hari Tersisa');
        $response->assertSee('Perpanjang Sekarang');
    }

    public function test_dashboard_shows_quick_invoice_button_when_payment_order_exists(): void
    {
        $this->seed(PlanSeeder::class);
        $school = School::factory()->create();
        $user = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
        ]);
        $plan = Plan::first();

        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
        ]);

        PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_id' => $subscription->id,
            'order_id' => 'ORD-TEST-12345',
            'status' => PaymentStatus::Paid,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Unduh Bukti Bayar &amp; SPJ BOS', false);
        $response->assertSee(route('pembayaran.show', 'ORD-TEST-12345'));
    }
}
