<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionExpiryGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);
    }

    /**
     * A logged-in user whose school owns an installed app with granted access.
     */
    private function userWithApp(): User
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);
        $school->grantAllAppAccess();

        return $user;
    }

    private function subscribe(School $school, string $planCode, int $daysFromNow): Subscription
    {
        $endsAt = now()->addDays($daysFromNow);

        return Subscription::create([
            'school_id' => $school->id,
            'plan_id' => Plan::where('code', $planCode)->firstOrFail()->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => $endsAt->copy()->subMonthsNoOverflow(6),
            'ends_at' => $endsAt,
        ]);
    }

    public function test_expired_subscription_redirects_school_to_checkout(): void
    {
        $user = $this->userWithApp();
        $this->subscribe($user->school, 'semester', -1);

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertRedirect(route('checkout.show', 'semester'))
            ->assertSessionHas('status');
    }

    public function test_active_subscription_still_opens_app(): void
    {
        $user = $this->userWithApp();
        $this->subscribe($user->school, 'semester', 30);

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertOk();
    }

    public function test_school_without_subscription_history_is_not_redirected(): void
    {
        $user = $this->userWithApp();

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertOk();
    }

    public function test_expired_subscription_keeps_dashboard_accessible(): void
    {
        $user = $this->userWithApp();
        $this->subscribe($user->school, 'semester', -1);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }
}
