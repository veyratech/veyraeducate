<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_plans_are_seeded_with_official_prices(): void
    {
        $this->seed(PlanSeeder::class);

        $semester = Plan::findByCode('semester');
        $tahunan = Plan::findByCode('tahunan');
        $super = Plan::findByCode('super');

        $this->assertNotNull($semester);
        $this->assertNotNull($tahunan);
        $this->assertNotNull($super);

        $this->assertSame(600000, $semester->price_idr);
        $this->assertSame(1100000, $tahunan->price_idr);
        $this->assertSame(1500000, $super->price_idr);
        $this->assertTrue($super->is_featured);
    }

    public function test_harga_page_requires_login_for_checkout_but_renders_prices(): void
    {
        $this->seed(PlanSeeder::class);

        $response = $this->get('/harga');

        $response->assertOk();
        $response->assertSee('Rp 600.000');
        $response->assertSee('Rp 1.100.000');
        $response->assertSee('Rp 1.500.000');
    }

    public function test_checkout_page_requires_authentication(): void
    {
        $this->seed(PlanSeeder::class);

        $this->get('/checkout/semester')->assertRedirect('/login');
    }

    public function test_checkout_page_renders_for_authenticated_user(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/checkout/semester');

        $response->assertOk();
        $response->assertSee('Paket Semesteran');
        $response->assertSee('Rp 600.000');
        $response->assertSee('Lanjut ke pembayaran');
    }

    public function test_unknown_plan_returns_404(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/checkout/tidak-ada')->assertNotFound();
    }

    public function test_checkout_store_creates_school_and_order_without_midtrans_when_server_key_missing(): void
    {
        config(['midtrans.server_key' => null]);
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout', [
            'plan_code' => 'semester',
            'school_name' => 'SMA Contoh',
            'school_email' => 'operator@sekolah.sch.id',
            'school_phone' => '081234567890',
            'city' => 'Depok',
            'npsn' => '20123456',
        ]);

        $response->assertSessionHasErrors('snap');
        $this->assertDatabaseHas('schools', [
            'owner_user_id' => $user->id,
            'name' => 'SMA Contoh',
        ]);
        $this->assertDatabaseHas('subscriptions', [
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('payment_orders', [
            'status' => 'failed',
        ]);
    }

    public function test_school_tracks_active_subscription(): void
    {
        $this->seed(PlanSeeder::class);

        $school = School::factory()->create();
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonthsNoOverflow(6),
            'activated_at' => now(),
        ]);

        $this->assertTrue($school->fresh()->hasActiveSubscription());
        $this->assertTrue($subscription->isActive());
    }

    public function test_admin_can_delete_unused_plan(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $plan = Plan::create([
            'code' => 'custom-plan',
            'label' => 'Paket Khusus',
            'price_idr' => 200000,
            'duration_months' => 2,
            'duration_label' => '2 Bulan',
            'sort_order' => 10,
            'features' => ['Fitur 1'],
            'is_default' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.plans.destroy', $plan));

        $response->assertRedirect(route('admin.plans.index'));
        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_admin_cannot_delete_default_plan(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $plan = Plan::create([
            'code' => 'default-plan',
            'label' => 'Paket Default',
            'price_idr' => 500000,
            'duration_months' => 6,
            'duration_label' => '6 Bulan',
            'sort_order' => 1,
            'features' => ['Fitur 1'],
            'is_default' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.plans.destroy', $plan));

        $response->assertRedirect(route('admin.plans.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('plans', ['id' => $plan->id]);
    }
}
