<?php

namespace Database\Factories;

use App\Models\WebApp;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebAppFactory extends Factory
{
    protected $model = WebApp::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(2),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category' => 'umum',
            'icon' => 'app',
            'version' => '1.0.0',
            'enabled' => true,
            'sort_order' => 0,
            'file_count' => 0,
        ];
    }
}
