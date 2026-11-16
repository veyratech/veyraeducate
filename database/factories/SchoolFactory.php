<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_user_id' => User::factory(),
            'name' => fake()->company().' '.fake()->randomElement(['SMA', 'SMP', 'SMK']),
            'npsn' => fake()->unique()->numerify('########'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('08##########'),
            'city' => fake()->city(),
            'logo_path' => null,
            'meta' => null,
        ];
    }
}
