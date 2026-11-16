<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph(3),
            'published_at' => $this->faker->dateTimeBetween('-30 days', '+1 day'),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
