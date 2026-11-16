<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\WebApp;
use Illuminate\Database\Seeder;

class AppCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $apps = [
            [
                'slug' => 'presensi',
                'name' => 'Presensi Digital',
                'category' => 'kesiswaan',
                'icon' => 'clipboard',
                'description' => 'Absensi siswa & guru berbasis QR Code',
                'enabled' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'e-rapor',
                'name' => 'E-Rapor',
                'category' => 'akademik',
                'icon' => 'document-text',
                'description' => 'Pengelolaan rapor digital Kurikulum Merdeka',
                'enabled' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'spp-keuangan',
                'name' => 'Keuangan & SPP',
                'category' => 'keuangan',
                'icon' => 'wallet',
                'description' => 'Manajemen pembayaran SPP dan keuangan sekolah',
                'enabled' => true,
                'sort_order' => 4,
            ],
            [
                'slug' => 'perpustakaan',
                'name' => 'Perpustakaan Digital',
                'category' => 'perpustakaan',
                'icon' => 'book-open',
                'description' => 'Katalog dan sirkulasi buku digital',
                'enabled' => true,
                'sort_order' => 5,
            ],
            [
                'slug' => 'ekskul',
                'name' => 'Ekstrakurikuler',
                'category' => 'ekskul',
                'icon' => 'trophy',
                'description' => 'Manajemen kegiatan ekstrakurikuler siswa',
                'enabled' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($apps as $app) {
            WebApp::query()->updateOrCreate(
                ['slug' => $app['slug']],
                $app,
            );
        }
    }
}
