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

class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
    }

    public function test_dashboard_shows_remaining_days_and_last_payment(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $subscription = $school->subscriptions()->create([
            'plan_id' => Plan::findByCode('semester')->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'activated_at' => now()->subDays(10),
        ]);

        PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $subscription->plan_id,
            'status' => PaymentStatus::Paid,
            'amount_idr' => 600000,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Langganan Aktif')
            ->assertSee('Hari Tersisa')
            ->assertSee('Tagihan Transaksi Terakhir')
            ->assertSee('Lunas')
            ->assertSee('Rp 600.000')
            ->assertSee('Kelengkapan Data Profil')
            ->assertSee($school->name);
    }

    public function test_dashboard_shows_pay_action_when_payment_is_pending(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $subscription = $school->subscriptions()->create([
            'plan_id' => Plan::findByCode('semester')->id,
            'status' => SubscriptionStatus::Pending,
        ]);

        $order = PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $subscription->plan_id,
            'status' => PaymentStatus::Pending,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Menunggu Pembayaran')
            ->assertSee('Bayar Sekarang')
            ->assertSee(route('pembayaran.show', $order->order_id), false);
    }

    public function test_dashboard_shows_checkout_call_to_action_without_subscription(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Belum Berlangganan')
            ->assertSee('Pilih Paket Langganan')
            ->assertSee('Data Sekolah Belum Lengkap');
    }

    public function test_ganti_paket_opens_floating_plan_picker(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $school->subscriptions()->create([
            'plan_id' => Plan::findByCode('semester')->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'activated_at' => now()->subDays(10),
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ganti / Perpanjang Paket')
            ->assertSee('id="planModal"', false)
            ->assertSee('Paket Aplikasi Sekolah')
            ->assertSee('Paket Semesteran')
            ->assertSee('Paket Super (1 Tahun)')
            ->assertSee('Paket Saat Ini')
            ->assertSee('Upgrade ke Paket Super (1 Tahun)')
            ->assertSee('Lihat perbandingan lengkap')
            ->assertDontSee('>Ganti paket</a>', false);
    }
}
