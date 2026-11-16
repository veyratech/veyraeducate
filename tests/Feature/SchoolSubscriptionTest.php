<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SchoolSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_activation_sets_period_from_plan_duration(): void
    {
        $this->seed(PlanSeeder::class);

        $school = School::factory()->create();
        $plan = Plan::findByCode('super');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
        ]);

        $subscription->activate();

        $subscription->refresh();

        $this->assertSame(SubscriptionStatus::Active, $subscription->status);
        $this->assertTrue($subscription->starts_at->isSameDay(now()));
        $this->assertTrue($subscription->ends_at->isSameDay(now()->addMonthsNoOverflow(12)));
        $this->assertTrue($school->fresh()->hasActiveSubscription());
    }

    public function test_user_links_to_school_after_checkout_attempt(): void
    {
        config(['midtrans.server_key' => null]);
        $this->seed(PlanSeeder::class);

        $user = User::factory()->create();

        $this->actingAs($user)->post('/checkout', [
            'plan_code' => 'tahunan',
            'school_name' => 'SMP Al-Azhar',
            'school_email' => 'operator@alazhar.sch.id',
            'school_phone' => '08111111111',
        ]);

        $user->refresh();
        $this->assertNotNull($user->school_id);
        $this->assertSame('SMP Al-Azhar', $user->school->name);
        $this->assertCount(1, $user->school->subscriptions);
        $this->assertSame('tahunan', $user->school->subscriptions->first()->plan->code);
    }

    public function test_expired_subscription_is_not_active(): void
    {
        $this->seed(PlanSeeder::class);
        $school = School::factory()->create();
        $plan = Plan::findByCode('semester');

        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subMonthsNoOverflow(7),
            'ends_at' => now()->subMonth(),
            'activated_at' => now()->subMonthsNoOverflow(7),
        ]);

        $this->assertFalse($subscription->isActive());
        $this->assertFalse($school->fresh()->hasActiveSubscription());
    }

    public function test_admin_can_lookup_npsn_json(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        Http::fake([
            'api-sekolah-indonesia.vercel.app/*' => Http::response([
                'status' => 'success',
                'dataSekolah' => [[
                    'npsn' => '20106343',
                    'sekolah' => 'SMP NEGERI 241',
                    'kabupaten_kota' => 'Kab. Kepulauan Seribu',
                    'propinsi' => 'Prov. D.K.I. Jakarta',
                    'bentuk' => 'SMP',
                ]],
            ], 200),
        ]);

        $this->actingAs($admin)
            ->getJson('/admin/sekolah/lookup-npsn/20106343')
            ->assertOk()
            ->assertJson([
                'found' => true,
                'name' => 'SMP NEGERI 241',
                'npsn' => '20106343',
            ]);
    }

    public function test_npsn_lookup_rejects_invalid_format(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        $this->actingAs($admin)
            ->getJson('/admin/sekolah/lookup-npsn/abc')
            ->assertNotFound();
    }

    public function test_npsn_lookup_requires_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/admin/sekolah/lookup-npsn/20106343')
            ->assertForbidden();
    }
}
