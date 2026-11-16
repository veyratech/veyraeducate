<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'key' => 'hero',
                'title' => 'Hero Background (Latar Belakang)',
                'section_type' => 'hero',
                'order_position' => 1,
                'is_active' => true,
                'content' => [
                    'show_banner' => true,
                    'lead' => 'Veyra Educate Wadah Super Digital Sekolah Anda',
                    'highlight' => '',
                    'tail' => '',
                    'description' => 'Veyra Educate menampung aplikasi sekolah Anda dalam satu platform: akun terpusat, langganan transparan, dan dashboard yang siap diisi aplikasi.',
                    'primary_btn_text' => 'Daftar sekolah',
                    'primary_btn_url' => '/register',
                    'secondary_btn_text' => 'Lihat demo',
                    'secondary_btn_url' => '/demo',
                    'stats' => [
                        ['number' => '3', 'label' => 'Paket'],
                        ['number' => 'Cloud', 'label' => 'Server'],
                        ['number' => 'Otomatis', 'label' => 'Aktivasi'],
                    ],
                ],
            ],
            [
                'key' => 'trust_strip',
                'title' => 'Pilar Kepercayaan (Trust Strip)',
                'section_type' => 'trust_strip',
                'order_position' => 2,
                'is_active' => true,
                'content' => [
                    'items' => [
                        [
                            'title' => 'Tanpa server lokal',
                            'desc' => 'Infrastruktur cloud yang kami kelola sepenuhnya.',
                            'color' => 'brand',
                        ],
                        [
                            'title' => 'Akun aman',
                            'desc' => 'Login terpusat untuk seluruh tim sekolah.',
                            'color' => 'gold',
                        ],
                        [
                            'title' => 'Langganan jelas',
                            'desc' => 'Harga transparan, aktivasi otomatis.',
                            'color' => 'emerald',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'features',
                'title' => 'Fitur Utama Wadah',
                'section_type' => 'features',
                'order_position' => 3,
                'is_active' => true,
                'content' => [
                    'badge' => 'Fitur',
                    'title' => 'Satu wadah untuk semua aplikasi sekolah',
                    'subtitle' => 'Platform siap pakai: akun, langganan, dan slot aplikasi yang menunggu diisi.',
                    'items' => [
                        [
                            'title' => 'Dashboard sekolah',
                            'body' => 'Satu dashboard untuk operator: status langganan, ringkasan, dan akses aplikasi.',
                            'accent' => 'brand',
                        ],
                        [
                            'title' => 'Slot aplikasi siap tanam',
                            'body' => 'Aplikasi sekolah tinggal diunggah ke wadah — tampil di dashboard tanpa daftar ulang.',
                            'accent' => 'gold',
                        ],
                        [
                            'title' => 'Multi-sekolah (tenant)',
                            'body' => 'Setiap sekolah punya data terisolasi — aman untuk banyak mitra.',
                            'accent' => 'sky',
                        ],
                        [
                            'title' => 'Akun terpusat',
                            'body' => 'Satu kredensial untuk masuk panel dan seluruh aplikasi aktif dalam langganan.',
                            'accent' => 'teal',
                        ],
                        [
                            'title' => 'Pembayaran resmi',
                            'body' => 'QRIS, virtual account, dan e-wallet via Midtrans — aktivasi otomatis setelah lunas.',
                            'accent' => 'amber',
                        ],
                        [
                            'title' => 'Panel admin lengkap',
                            'body' => 'Kelola sekolah, pengguna, paket, dan pembayaran dari satu tempat.',
                            'accent' => 'emerald',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'app_showcase',
                'title' => 'Katalog Aplikasi Sekolah',
                'section_type' => 'app_showcase',
                'order_position' => 4,
                'is_active' => true,
                'content' => [
                    'badge' => 'Katalog Aplikasi',
                    'title' => 'Ragam Aplikasi Siap Tanam untuk Sekolah Anda',
                    'subtitle' => 'Satu wadah terintegrasi untuk berbagai kebutuhan digitalisasi sekolah — dari ujian daring, presensi, rapor, hingga keuangan.',
                    'source_mode' => 'custom',
                    'items' => [
                        [
                            'name' => 'CBT & Ujian Online',
                            'category' => 'Akademik',
                            'description' => 'Sistem ujian berbasis komputer anti-curang, acak butir soal & opsi, timer presisi, dan penilaian otomatis.',
                            'status_badge' => 'Siap Pakai',
                            'accent' => 'brand',
                            'tags' => 'Anti-Curang, Bank Soal, Koreksi Instan',
                            'icon' => 'academic-cap',
                        ],
                        [
                            'name' => 'Presensi & Absensi Siswa',
                            'category' => 'Kesiswaan',
                            'description' => 'Pencatatan kehadiran digital via barcode/QR code & kartu RFID, rekapitulasi kehadiran instan, dan integrasi wali murid.',
                            'status_badge' => 'Siap Pakai',
                            'accent' => 'emerald',
                            'tags' => 'QR Code, Kartu RFID, Rekap Bulanan',
                            'icon' => 'clipboard',
                        ],
                        [
                            'name' => 'E-Rapor Kurikulum Merdeka',
                            'category' => 'Akademik',
                            'description' => 'Pengisian nilai capaian pembelajaran, ledger nilai kelas otomatis, dan cetak rapor format PDF resmi tanpa ribet.',
                            'status_badge' => 'Siap Pakai',
                            'accent' => 'gold',
                            'tags' => 'Kurikulum Merdeka, Cetak PDF, Ledger Nilai',
                            'icon' => 'document-text',
                        ],
                        [
                            'name' => 'SPP & Keuangan Sekolah',
                            'category' => 'Keuangan',
                            'description' => 'Manajemen pos tagihan siswa, kuitansi digital otomatis, pelacakan tunggakan, dan rekap arus kas sekolah.',
                            'status_badge' => 'Siap Pakai',
                            'accent' => 'amber',
                            'tags' => 'Tagihan SPP, Kuitansi Digital, Rekap Kas',
                            'icon' => 'wallet',
                        ],
                        [
                            'name' => 'Perpustakaan Digital (E-Perpus)',
                            'category' => 'Perpustakaan',
                            'description' => 'Manajemen katalog buku, sirkulasi peminjaman barcode, bebas pustaka kelulusan, dan statistik pembaca.',
                            'status_badge' => 'Bisa Ditanam',
                            'accent' => 'sky',
                            'tags' => 'Barcode Buku, Bebas Pustaka, E-Katalog',
                            'icon' => 'book-open',
                        ],
                        [
                            'name' => 'SIM Kepegawaian & Jurnal Guru',
                            'category' => 'Kepegawaian',
                            'description' => 'Pencatatan agenda mengajar harian, jam tatap muka pendidik, dan arsip dokumen kepegawaian sekolah.',
                            'status_badge' => 'Segera Hadir',
                            'accent' => 'teal',
                            'tags' => 'Agenda Mengajar, Jam Pendidik, Arsip',
                            'icon' => 'user-group',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'workflow',
                'title' => 'Alur Langkah (Cara Kerja)',
                'section_type' => 'workflow',
                'order_position' => 5,
                'is_active' => true,
                'content' => [
                    'badge' => 'Cara kerja',
                    'title' => 'Dari daftar hingga panel aktif dalam empat langkah',
                    'subtitle' => 'Alur pembayaran resmi Midtrans — QRIS, virtual account, dan e-wallet — lalu aktivasi otomatis.',
                    'steps' => [
                        [
                            'title' => 'Pilih paket',
                            'body' => 'Tentukan durasi langganan — Semesteran, Tahunan, atau Super — sesuai kebutuhan sekolah.',
                        ],
                        [
                            'title' => 'Bayar resmi',
                            'body' => 'Selesaikan pembayaran melalui QRIS, virtual account, atau e-wallet pada halaman checkout.',
                        ],
                        [
                            'title' => 'Otomatis aktif',
                            'body' => 'Sistem memverifikasi pembayaran dan mengaktifkan panel sekolah beserta masa berlakunya.',
                        ],
                        [
                            'title' => 'Buka aplikasi',
                            'body' => 'Masuk ke dashboard, buka aplikasi sekolah Anda — siap dipakai tim di lapangan.',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'ecosystem',
                'title' => 'Tiga Pilar Ekosistem',
                'section_type' => 'ecosystem',
                'order_position' => 6,
                'is_active' => true,
                'content' => [
                    'badge' => 'Ekosistem',
                    'title' => 'Berbayar resmi, aplikasi bertambah, akses terpusat',
                    'subtitle' => 'Tiga pilar layanan yang menemani sekolah sepanjang langganan.',
                    'items' => [
                        [
                            'title' => 'Pembayaran & aktivasi otomatis',
                            'body' => 'QRIS, virtual account bank, dan e-wallet. Sistem memverifikasi pembayaran dan mengaktifkan lisensi tanpa campur tangan admin.',
                            'accent' => 'brand',
                        ],
                        [
                            'title' => 'Aplikasi yang terus bertambah',
                            'body' => 'Aplikasi sekolah baru diunggah ke wadah dan langsung tersedia di panel pelanggan tanpa daftar ulang.',
                            'accent' => 'gold',
                        ],
                        [
                            'title' => 'Akses terpusat (SSO)',
                            'body' => 'Satu kredensial operator untuk panel sekolah dan seluruh aplikasi yang aktif dalam langganan.',
                            'accent' => 'sky',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'pricing',
                'title' => 'Daftar Paket Harga',
                'section_type' => 'pricing',
                'order_position' => 7,
                'is_active' => true,
                'content' => [
                    'badge' => 'Harga',
                    'title' => 'Paket langganan transparan',
                    'subtitle' => 'Harga yang tercantum di sini sama dengan yang ditagihkan saat checkout. Tanpa biaya tersembunyi.',
                ],
            ],
            [
                'key' => 'testimonials',
                'title' => 'Testimoni & Ulasan Sekolah',
                'section_type' => 'testimonials',
                'order_position' => 8,
                'is_active' => true,
                'content' => [
                    'badge' => 'Testimoni',
                    'title' => 'Dipercaya oleh puluhan sekolah & yayasan di Indonesia',
                    'subtitle' => 'Pengalaman nyata kepala sekolah dan tim operator yang merasakan kemudahan sistem terpusat Veyra Educate.',
                    'items' => [
                        [
                            'name' => 'Drs. H. Mulyadi, M.Pd.',
                            'role' => 'Kepala Sekolah',
                            'school' => 'SMA Bina Bangsa',
                            'quote' => 'Dulu kami pusing mengelola server ujian sendiri setiap kali semesteran. Sejak memakai Veyra Educate, semuanya berjalan di cloud tanpa takut down, dan pembayarannya sangat transparan.',
                            'rating' => 5,
                            'avatar_url' => null,
                        ],
                        [
                            'name' => 'Siti Rahmawati, S.Kom.',
                            'role' => 'Koordinator IT & Kurikulum',
                            'school' => 'SMK Prestasi Nusantara',
                            'quote' => 'Integrasi akun terpusatnya sangat menghemat waktu. Guru tidak perlu menghafal banyak password untuk tiap modul, dan laporan langsung tersinkron ke dashboard pimpinan.',
                            'rating' => 5,
                            'avatar_url' => null,
                        ],
                        [
                            'name' => 'Ahmad Fauzi, S.T.',
                            'role' => 'Operator Sekolah & Dapodik',
                            'school' => 'SMP Islam Al-Hikmah',
                            'quote' => 'Aktivasi otomatis Midtrans membuat kami bisa langsung mengoperasikan sistem dalam hitungan menit setelah transfer. Layanan pendampingannya juga sangat ramah dan sigap.',
                            'rating' => 5,
                            'avatar_url' => null,
                        ],
                    ],
                ],
            ],
            [
                'key' => 'faq',
                'title' => 'Tanya Jawab (FAQ)',
                'section_type' => 'faq',
                'order_position' => 9,
                'is_active' => true,
                'content' => [
                    'badge' => 'FAQ',
                    'title' => 'Pertanyaan yang sering diajukan',
                    'subtitle' => 'Ringkasan jawaban — versi lengkap ada di halaman FAQ.',
                    'items' => [
                        [
                            'q' => 'Bagaimana cara kerja sistem setelah pembayaran?',
                            'a' => 'Setelah pembayaran terverifikasi otomatis, status akun berubah menjadi Aktif dan masa berlaku dihitung seketika.',
                        ],
                        [
                            'q' => 'Apakah sekolah perlu menyiapkan server sendiri?',
                            'a' => 'Tidak. Seluruh infrastruktur, hosting, dan pemeliharaan server ditangani oleh tim Veyra Educate.',
                        ],
                        [
                            'q' => 'Bagaimana jika pembayaran pending atau gagal?',
                            'a' => 'Akun tetap dapat masuk. Panel menampilkan tombol bayar ulang dengan nomor order baru.',
                        ],
                        [
                            'q' => 'Bisakah berganti paket setelah mendaftar?',
                            'a' => 'Bisa, selama lisensi belum aktif. Total tagihan menyesuaikan otomatis sebelum pembayaran.',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'cta',
                'title' => 'Call To Action (Penutup)',
                'section_type' => 'cta',
                'order_position' => 10,
                'is_active' => true,
                'content' => [
                    'badge' => 'Siap mulai',
                    'title' => 'Siap menyiapkan wadah aplikasi sekolah Anda?',
                    'subtitle' => 'Pendaftaran memakan waktu kurang dari lima menit. Tim kami siap mendampingi onboarding melalui WhatsApp.',
                    'btn_text' => 'Daftar sekolah',
                    'btn_url' => '/register',
                    'wa_text' => 'Konsultasi WhatsApp',
                    'wa_url' => 'https://wa.me/6285707074778',
                ],
            ],
        ];

        foreach ($sections as $data) {
            HomepageSection::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }
}
