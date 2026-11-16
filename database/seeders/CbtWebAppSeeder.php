<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\WebApp;
use Illuminate\Database\Seeder;

class CbtWebAppSeeder extends Seeder
{
    public function run(): void
    {
        WebApp::query()->updateOrCreate(
            ['slug' => 'cbtonline'],
            [
                'name' => 'CBT Online',
                'description' => 'Sistem Ujian Online Terpadu, bank soal, dan evaluasi hasil belajar berbasis CBT.',
                'category' => 'akademik',
                'icon' => 'academic-cap',
                'access_url' => null,
                'version' => '1.0.0',
                'enabled' => true,
                'sort_order' => 1,
            ]
        );
    }
}
