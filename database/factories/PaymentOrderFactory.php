<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentOrder>
 */
class PaymentOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => 'VEY-'.now()->format('Ymd').'-'.strtoupper(Str::random(8)),
            'subscription_id' => Subscription::factory(),
            'school_id' => School::factory(),
            'user_id' => User::factory(),
            'plan_id' => Plan::factory()->semester(),
            'amount_idr' => 600000,
            'status' => PaymentStatus::Pending,
            'payment_method' => null,
            'snap_token' => null,
            'transaction_id' => null,
            'snap_response' => null,
            'webhook_payload' => null,
            'paid_at' => null,
            'expired_at' => now()->addDay(),
        ];
    }
}
