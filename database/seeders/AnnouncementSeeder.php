<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => '[INFO] Selamat Datang di VEYRA EDUCATE',
                'body' => 'Kami mengucapkan selamat datang di platform VEYRA EDUCATE. Sistem ini dirancang untuk membantu pengelolaan sekolah menjadi lebih digital, efisien, dan terintegrasi. Silakan eksplorasi semua fitur yang tersedia.',
                'published_at' => now(),
                'is_active' => true,
            ],
            [
                'title' => '[MAINTENANCE] Maintenance Server Akhir Pekan',
                'body' => 'Diberitahukan kepada seluruh pengguna bahwa akan dilakukan maintenance server pada akhir pekan mendatang. Selama periode tersebut, beberapa layanan mungkin tidak dapat diakses. Mohon pengertian dan kerjasamanya.',
                'published_at' => now()->addDays((7 - (int) now()->format('w')) % 7),
                'is_active' => true,
            ],
            [
                'title' => '[PROMO] Paket Super Diskon 20%',
                'body' => 'Promo terbatas! Dapatkan diskon 20% untuk Paket Super (1 Tahun) dengan segala fitur premium dan prioritas support 24/7. Penawaran ini hanya berlaku dalam waktu terbatas. Segera daftarkan sekolah Anda!',
                'published_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::query()->updateOrCreate(
                ['title' => $announcement['title']],
                $announcement,
            );
        }
    }
}
