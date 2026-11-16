<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\Notifications\SubscriptionExpiryNotification;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionExpiryNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_reminder_at_7_days_before_expiry(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        Notification::fake();

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDays(183),
            'ends_at' => now()->addDays(7),
            'activated_at' => now(),
        ]);

        $this->artisan('subscriptions:expiry-check')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $school->owner,
            SubscriptionExpiryNotification::class,
            function (SubscriptionExpiryNotification $notification, $channel) use ($subscription) {
                return $notification->subscription->is($subscription);
            }
        );

        $subscription->refresh();
        $this->assertArrayHasKey('reminded_7days', $subscription->meta);
    }

    public function test_command_sends_reminder_at_3_days_before_expiry(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        Notification::fake();

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDays(185),
            'ends_at' => now()->addDays(3),
            'activated_at' => now(),
        ]);

        $this->artisan('subscriptions:expiry-check')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $school->owner,
            SubscriptionExpiryNotification::class,
            function (SubscriptionExpiryNotification $notification, $channel) use ($subscription) {
                return $notification->subscription->is($subscription);
            }
        );

        $subscription->refresh();
        $this->assertArrayHasKey('reminded_3days', $subscription->meta);
    }

    public function test_command_does_not_send_duplicate_notifications(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        Notification::fake();

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDays(183),
            'ends_at' => now()->addDays(7),
            'activated_at' => now(),
            'meta' => ['reminded_7days' => now()->toISOString()],
        ]);

        $this->artisan('subscriptions:expiry-check')
            ->assertExitCode(0);

        Notification::assertNotSentTo($school->owner, SubscriptionExpiryNotification::class);
    }

    public function test_command_skips_expiring_subscriptions_without_owner(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        Notification::fake();

        $school = School::factory()->create(['owner_user_id' => null]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDays(183),
            'ends_at' => now()->addDays(7),
            'activated_at' => now(),
        ]);

        $this->artisan('subscriptions:expiry-check')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function test_command_skips_expired_subscriptions(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        Notification::fake();

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subMonths(7),
            'ends_at' => now()->subDays(1),
            'activated_at' => now()->subMonths(6),
        ]);

        $this->artisan('subscriptions:expiry-check')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function test_command_skips_non_active_subscriptions(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        Notification::fake();

        $school = School::factory()->create(['owner_user_id' => User::factory()->create()->id]);
        $plan = Plan::findByCode('semester');
        $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
            'starts_at' => now()->subDays(183),
            'ends_at' => now()->addDays(7),
            'activated_at' => null,
        ]);

        $this->artisan('subscriptions:expiry-check')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
