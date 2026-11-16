<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->slug(2),
            'label' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price_idr' => fake()->randomElement([600000, 1100000, 1500000]),
            'duration_months' => fake()->randomElement([6, 12]),
            'duration_label' => fake()->randomElement(['6 Bulan', '12 Bulan']),
            'badge' => fake()->optional()->sentence(3),
            'features' => [fake()->sentence(), fake()->sentence()],
            'is_featured' => true,
            'is_default' => true,
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function semester(): static
    {
        return $this->state(fn () => [
            'code' => 'semester',
            'label' => 'Paket Semesteran',
            'description' => 'Akses panel sekolah untuk 1 semester ajaran.',
            'price_idr' => 600000,
            'duration_months' => 6,
            'duration_label' => '6 Bulan',
            'badge' => 'Masa Aktif: 6 Bulan',
            'features' => [
                'Akses panel sekolah (aplikasi terintegrasi)',
                'Akun operator & guru tanpa batas',
                'Aplikasi sekolah terhubung ke satu dashboard',
                'Dukungan onboarding via WhatsApp',
            ],
            'is_featured' => true,
            'is_default' => true,
            'sort_order' => 10,
        ]);
    }

    public function tahunan(): static
    {
        return $this->state(fn () => [
            'code' => 'tahunan',
            'label' => 'Paket 1 Tahun Penuh',
            'description' => 'Akses penuh mencakup Semester Ganjil & Genap.',
            'price_idr' => 1100000,
            'duration_months' => 12,
            'duration_label' => '12 Bulan',
            'badge' => 'Masa Aktif: 12 Bulan',
            'features' => [
                'Semua fitur Paket Semester',
                'Masa berlaku 1 tahun penuh',
                'Pembaruan aplikasi selama masa aktif',
                'Prioritas support WhatsApp',
            ],
            'is_featured' => true,
            'is_default' => true,
            'sort_order' => 20,
        ]);
    }

    public function super(): static
    {
        return $this->state(fn () => [
            'code' => 'super',
            'label' => 'Paket Super (1 Tahun)',
            'description' => 'Solusi lengkap untuk kebutuhan digital sekolah.',
            'price_idr' => 1500000,
            'duration_months' => 12,
            'duration_label' => '12 Bulan + Ekstra',
            'badge' => 'Semua Fitur + Support Prioritas',
            'features' => [
                'Seluruh fitur Paket 1 Tahun Penuh',
                'Custom branding sekolah',
                'Onboarding & pelatihan tim',
                'Priority support 24/7',
            ],
            'is_featured' => true,
            'is_default' => true,
            'sort_order' => 30,
        ]);
    }
}
