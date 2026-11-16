<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'plan_id' => Plan::factory()->semester(),
            'status' => SubscriptionStatus::Pending,
            'starts_at' => null,
            'ends_at' => null,
            'activated_at' => null,
            'meta' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonthsNoOverflow(6),
            'activated_at' => now(),
        ]);
    }
}
