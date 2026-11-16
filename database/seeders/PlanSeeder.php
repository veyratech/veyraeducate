<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
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
                'is_featured' => false,
                'is_default' => false,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
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
                'is_featured' => false,
                'is_default' => false,
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
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
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
