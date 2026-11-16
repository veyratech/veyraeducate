<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_render(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonthsNoOverflow(6),
            'activated_at' => now(),
        ]);
        $order = PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'user_id' => $school->owner_user_id,
        ]);
        $announcement = Announcement::factory()->create();

        $pages = [
            '/admin',
            '/admin/sekolah',
            "/admin/sekolah/{$school->id}",
            "/admin/sekolah/{$school->id}/edit",
            '/admin/pengguna',
            '/admin/aplikasi',
            '/admin/link-akses',
            '/admin/paket',
            '/admin/paket/baru',
            "/admin/paket/{$plan->id}/edit",
            '/admin/pembayaran',
            "/admin/pembayaran/{$order->order_id}",
            '/admin/pengaturan',
            '/admin/homepage',
            '/admin/homepage/baru',
            '/admin/pengumuman',
            '/admin/pengumuman/baru',
            "/admin/pengumuman/{$announcement->id}/edit",
            '/admin/permintaan-aplikasi',
        ];

        foreach ($pages as $page) {
            $response = $this->actingAs($admin)->get($page);

            $response->assertStatus(200, "GET {$page}");
            $response->assertSee($admin->name);
            $response->assertSee($admin->email);
            $response->assertSee('Keluar', false);
        }
    }
}
