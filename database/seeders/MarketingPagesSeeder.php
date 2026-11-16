<?php

namespace Database\Seeders;

use App\Models\MarketingPage;
use Illuminate\Database\Seeder;

class MarketingPagesSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'demo' => [
                'title' => 'Demo Interaktif — Coba Veyra Educate & CBT Online',
                'description' => 'Uji coba langsung antarmuka ujian CBT siswa dari browser Anda dan akses portal demo manajemen sekolah tanpa perlu instalasi rumit.',
                'eyebrow' => 'Simulasi & Demo Sistem Interaktif',
                'cta_text' => 'Coba Simulator CBT Siswa',
                'cta_url' => '#simulator',
                'is_active' => true,
            ],
            'brosur' => [
                'title' => 'Brosur Digital — Veyra Educate & CBT Online',
                'description' => 'Brosur resmi digitalisasi ujian sekolah berbasis komputer (CBT Online) dan portal aplikasi Veyra Educate. Hemat jutaan rupiah biaya cetak kertas, 100% cloud, dan ramah SPJ Dana BOS.',
                'eyebrow' => 'Brosur Resmi',
                'cta_text' => 'Unduh Brosur PDF',
                'cta_url' => '#download-brosur',
                'is_active' => true,
            ],
            'proposal' => [
                'title' => 'Surat Penawaran Resmi — Veyra Educate & CBT Online',
                'description' => 'Proposal resmi untuk keperluan presentasi kepada dinas pendidikan, kepala sekolah, dan pemangku kepentingan. Format A4 siap cetak dengan branding profesional.',
                'eyebrow' => 'Proposal Resmi',
                'cta_text' => 'Unduh Proposal PDF',
                'cta_url' => '#download-proposal',
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $slug => $data) {
            MarketingPage::firstOrCreate(
                ['slug' => $slug],
                $data
            );
        }
    }
}
