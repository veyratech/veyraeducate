<?php

namespace Tests\Feature;

use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);

        return User::where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Masuk admin');
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard admin');
    }

    public function test_school_user_cannot_login_to_admin(): void
    {
        $this->seed(PlanSeeder::class);
        $schoolUser = User::factory()->create(['password' => 'password']);

        $this->from('/admin/login')
            ->post('/admin/login', [
                'email' => $schoolUser->email,
                'password' => 'password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/sekolah')->assertRedirect('/admin/login');
        $this->get('/admin/paket')->assertRedirect('/admin/login');
        $this->get('/admin/pembayaran')->assertRedirect('/admin/login');
    }

    public function test_school_user_gets_403_on_admin_area(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/sekolah')->assertForbidden();
    }

    public function test_admin_sees_stats_and_latest_records(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $sub = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonthsNoOverflow(6),
            'activated_at' => now(),
        ]);
        PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'subscription_id' => $sub->id,
            'plan_id' => $plan->id,
            'user_id' => $school->owner_user_id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Total Sekolah')
            ->assertSee('Langganan Aktif')
            ->assertSee($school->name);
    }

    public function test_admin_dashboard_shows_registration_traffic_and_monthly_revenue(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);

        $this->travelTo(Carbon::create(2026, 9, 24, 12, 0, 0));

        School::factory()->create([
            'owner_user_id' => User::factory()->create()->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);
        School::factory()->create([
            'owner_user_id' => User::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $sub = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonthsNoOverflow(6),
            'activated_at' => now(),
        ]);
        PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'subscription_id' => $sub->id,
            'plan_id' => $plan->id,
            'user_id' => $school->owner_user_id,
            'status' => 'paid',
            'amount_idr' => 600000,
            'paid_at' => now()->subMonths(1)->setDay(10)->setTime(12, 0),
        ]);
        PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'subscription_id' => $sub->id,
            'plan_id' => $plan->id,
            'user_id' => $school->owner_user_id,
            'status' => 'paid',
            'amount_idr' => 750000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk()
            ->assertSee('Pendapatan', false)
            ->assertSee('6 bulan terakhir', false)
            ->assertSee('750k', false);

        $response->assertViewHas('registrationTraffic', function ($traffic) {
            return $traffic->count() === 30 && $traffic->sum('count') >= 2;
        });
        $response->assertViewHas('revenueByMonth', function ($revenue) {
            if ($revenue->count() !== 6) {
                return false;
            }

            $current = $revenue->last();

            return $current['key'] === '2026-09' && $current['amount'] === 750000 && $current['orders'] === 1;
        });
    }

    public function test_admin_dashboard_charts_are_empty_safe(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Pendapatan', false)
            ->assertSee('6 bulan terakhir', false);
    }

    public function test_admin_can_list_and_search_schools(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);

        $school = School::factory()->create([
            'owner_user_id' => User::factory()->create()->id,
            'name' => 'SMA Khusus Admin Search',
        ]);

        $this->actingAs($admin)->get('/admin/sekolah')
            ->assertOk()
            ->assertSee('SMA Khusus Admin Search');

        $this->actingAs($admin)->get('/admin/sekolah?q=Khusus')
            ->assertOk()
            ->assertSee('SMA Khusus Admin Search');
    }

    public function test_admin_can_create_school_with_owner(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/sekolah', [
            'name' => 'SMP Baru',
            'npsn' => '12345678',
            'email' => 'info@smpbaru.test',
            'phone' => '08111111111',
            'city' => 'Jakarta',
            'owner_name' => 'Kepala SMP',
            'owner_email' => 'kepala@smpbaru.test',
            'owner_password' => 'password123',
        ])->assertRedirect();

        $this->assertDatabaseHas('schools', ['name' => 'SMP Baru', 'npsn' => '12345678']);
        $this->assertDatabaseHas('users', ['email' => 'kepala@smpbaru.test', 'role' => 'school']);
    }

    public function test_admin_can_edit_plan_price_and_features(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);
        $plan = Plan::findByCode('semester');

        $this->actingAs($admin)
            ->put("/admin/paket/{$plan->id}", [
                'label' => 'Paket Semesteran Baru',
                'description' => 'Diubah',
                'price_idr' => 650000,
                'duration_months' => 6,
                'duration_label' => '6 Bulan',
                'badge' => 'Baru',
                'sort_order' => 10,
                'features' => ['Fitur A'],
                'is_active' => '1',
                'is_featured' => '0',
            ])
            ->assertRedirect(route('admin.plans.index'));

        $plan->refresh();
        $this->assertSame('Paket Semesteran Baru', $plan->label);
        $this->assertSame(650000, $plan->price_idr);
        $this->assertFalse($plan->is_featured);
        $this->assertTrue($plan->is_active);
    }

    public function test_admin_can_create_plan(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/paket', [
                'code' => 'new-plan',
                'label' => 'Paket Baru',
                'description' => 'Deskripsi paket baru',
                'price_idr' => 200000,
                'duration_months' => 1,
                'duration_label' => '1 Bulan',
                'sort_order' => 50,
                'features' => ['Fitur 1', 'Fitur 2'],
                'is_active' => '1',
                'is_featured' => '0',
            ])
            ->assertRedirect(route('admin.plans.edit', Plan::where('code', 'new-plan')->first()));

        $this->assertDatabaseHas('plans', [
            'code' => 'new-plan',
            'label' => 'Paket Baru',
            'price_idr' => 200000,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_plan_with_invalid_code_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/paket', [
                'code' => 'Invalid Code!',
                'label' => 'Paket Baru',
                'price_idr' => 100000,
                'duration_months' => 1,
                'duration_label' => 'Bulanan',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_admin_can_edit_school(): void
    {
        $admin = $this->admin();
        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);

        $this->actingAs($admin)
            ->put("/admin/sekolah/{$school->id}", [
                'name' => 'SMA Diubah Admin',
                'npsn' => $school->npsn,
                'email' => 'ubah@sekolah.test',
                'phone' => '0812000000',
                'city' => 'Bandung',
            ])
            ->assertRedirect(route('admin.schools.show', $school));

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'SMA Diubah Admin',
            'city' => 'Bandung',
        ]);
    }

    public function test_admin_can_delete_school(): void
    {
        $admin = $this->admin();
        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);

        $this->actingAs($admin)
            ->delete("/admin/sekolah/{$school->id}")
            ->assertRedirect(route('admin.schools.index'));

        $this->assertSoftDeleted('schools', ['id' => $school->id]);
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['name' => 'Guru List Search', 'last_login_at' => now()]);

        $this->actingAs($admin)
            ->get('/admin/pengguna')
            ->assertOk()
            ->assertSee('Guru List Search')
            ->assertSee('Login Terakhir')
            ->assertSee('Tambah Pengguna');

        $this->actingAs($admin)
            ->get('/admin/pengguna?q=List')
            ->assertOk()
            ->assertSee('Guru List Search');
    }

    public function test_admin_can_create_school_user(): void
    {
        $admin = $this->admin();
        $school = School::factory()->create(['owner_user_id' => $admin->id]);

        $response = $this->actingAs($admin)
            ->post('/admin/pengguna', [
                'name' => 'Guru Baru Veyra',
                'email' => 'guru.baru@sekolah.sch.id',
                'password' => 'secret12345',
                'role' => User::ROLE_SCHOOL,
                'school_id' => $school->id,
                'phone' => '081234567890',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Guru Baru Veyra',
            'email' => 'guru.baru@sekolah.sch.id',
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
            'phone' => '081234567890',
        ]);

        $created = User::where('email', 'guru.baru@sekolah.sch.id')->first();
        $this->assertNotNull($created);
        $this->assertTrue(password_verify('secret12345', $created->password));
    }

    public function test_admin_can_create_admin_user(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->post('/admin/pengguna', [
                'name' => 'Admin Pembantu',
                'email' => 'admin.pembantu@veyraeducate.test',
                'password' => 'adminpass123',
                'role' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Admin Pembantu',
            'email' => 'admin.pembantu@veyraeducate.test',
            'role' => User::ROLE_ADMIN,
            'school_id' => null,
        ]);
    }

    public function test_admin_cannot_create_user_with_duplicate_email(): void
    {
        $admin = $this->admin();
        $existing = User::factory()->create(['email' => 'duplikat@test.com']);

        $response = $this->actingAs($admin)
            ->post('/admin/pengguna', [
                'name' => 'Pengguna Lain',
                'email' => 'duplikat@test.com',
                'password' => 'secret12345',
                'role' => User::ROLE_SCHOOL,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['name' => 'User Untuk Dihapus']);

        $response = $this->actingAs($admin)
            ->delete("/admin/pengguna/{$user->id}");

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)
            ->delete("/admin/pengguna/{$admin->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_mark_payment_paid_and_activate_subscription(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('super');
        $sub = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
            'starts_at' => now(),
            'ends_at' => now()->addMonthsNoOverflow(12),
        ]);
        $order = PaymentOrder::factory()->create([
            'school_id' => $school->id,
            'subscription_id' => $sub->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post("/admin/pembayaran/{$order->order_id}/lunas")
            ->assertRedirect();

        $this->assertSame('paid', $order->fresh()->status->value);
        $this->assertSame('active', $sub->fresh()->status->value);
        $this->assertTrue($sub->fresh()->isActive());
    }

    public function test_admin_login_for_non_admin_is_rejected_after_auth_attempt(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create(['password' => 'password']);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
