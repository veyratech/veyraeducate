<?php

namespace Tests\Feature;

use App\Models\HomepageSection;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\HomepageSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageSectionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminUserSeeder::class);
        $this->seed(HomepageSectionSeeder::class);
        $this->admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_homepage_renders_all_active_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Veyra Educate Wadah Super Digital Sekolah Anda');
        $response->assertSee('Tanpa server lokal');
        $response->assertSee('Satu wadah untuk semua aplikasi sekolah');
        $response->assertSee('Pertanyaan yang sering diajukan');
    }

    public function test_admin_can_view_homepage_sections_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/homepage');

        $response->assertStatus(200);
        $response->assertSee('Kelola Container Homepage');
        $response->assertSee('Hero Background (Latar Belakang)');
        $response->assertSee('Fitur Utama Wadah');
    }

    public function test_admin_can_create_new_custom_banner(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/homepage', [
            'title' => 'Banner Promo Spesial',
            'section_type' => 'custom_banner',
            'order_position' => 2,
            'is_active' => '1',
            'badge' => 'Promo Terbatas',
            'title_text' => 'Diskon Awal Tahun 50%',
            'subtitle' => 'Khusus pendaftaran sekolah minggu ini.',
            'theme' => 'emerald',
            'btn_text' => 'Klaim Sekarang',
            'btn_url' => '/register',
        ]);

        $response->assertRedirect('/admin/homepage');
        $this->assertDatabaseHas('homepage_sections', [
            'title' => 'Banner Promo Spesial',
            'section_type' => 'custom_banner',
        ]);

        // Homepage should now display the new promo
        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Diskon Awal Tahun 50%');
        $homeResponse->assertSee('Promo Terbatas');
    }

    public function test_admin_can_toggle_section_visibility(): void
    {
        $hero = HomepageSection::query()->where('key', 'hero')->firstOrFail();
        $this->assertTrue($hero->is_active);

        // Toggle off
        $response = $this->actingAs($this->admin)->patch("/admin/homepage/{$hero->id}/toggle");
        $response->assertRedirect('/admin/homepage');

        $hero->refresh();
        $this->assertFalse($hero->is_active);

        // Homepage should no longer render the hero
        $homeResponse = $this->get('/');
        $homeResponse->assertDontSee('Veyra Educate menampung aplikasi sekolah Anda');
    }

    public function test_admin_can_edit_section_content(): void
    {
        $hero = HomepageSection::query()->where('key', 'hero')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$hero->id}", [
            'title' => 'Hero Banner Diperbarui',
            'order_position' => 1,
            'is_active' => '1',
            'lead' => 'Teknologi Sekolah No. 1',
            'highlight' => 'Solusi Digital Terbaik,',
            'tail' => 'untuk Semua Sekolah.',
            'description' => 'Deskripsi baru yang keren dan modern.',
            'primary_btn_text' => 'Daftar Sekarang Juga',
            'primary_btn_url' => '/register',
            'secondary_btn_text' => 'Cek Demo',
            'secondary_btn_url' => '/demo',
        ]);

        $response->assertRedirect('/admin/homepage');

        $hero->refresh();
        $this->assertEquals('Hero Banner Diperbarui', $hero->title);
        $this->assertEquals('Solusi Digital Terbaik,', $hero->field('highlight'));

        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Solusi Digital Terbaik,');
        $homeResponse->assertSee('untuk Semua Sekolah.');
    }

    public function test_admin_can_reorder_sections(): void
    {
        $hero = HomepageSection::query()->where('key', 'hero')->firstOrFail();
        $trust = HomepageSection::query()->where('key', 'trust_strip')->firstOrFail();

        $this->assertEquals(1, $hero->order_position);
        $this->assertEquals(2, $trust->order_position);

        // Move hero down
        $response = $this->actingAs($this->admin)->post("/admin/homepage/{$hero->id}/move", [
            'direction' => 'down',
        ]);
        $response->assertRedirect('/admin/homepage');

        $hero->refresh();
        $trust->refresh();

        $this->assertEquals(2, $hero->order_position);
        $this->assertEquals(1, $trust->order_position);
    }

    public function test_admin_can_delete_custom_section(): void
    {
        $custom = HomepageSection::query()->create([
            'key' => 'custom_123',
            'title' => 'Test Custom Section',
            'section_type' => 'custom_content',
            'order_position' => 99,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/homepage/{$custom->id}");
        $response->assertRedirect('/admin/homepage');

        $this->assertDatabaseMissing('homepage_sections', [
            'id' => $custom->id,
        ]);
    }

    public function test_admin_can_reset_to_default_sections(): void
    {
        HomepageSection::query()->delete();
        $this->assertDatabaseCount('homepage_sections', 0);

        $response = $this->actingAs($this->admin)->post('/admin/homepage/reset-default');
        $response->assertRedirect('/admin/homepage');

        $this->assertDatabaseCount('homepage_sections', 10);
    }

    public function test_admin_can_set_custom_background_color_and_overlay(): void
    {
        $hero = HomepageSection::query()->where('key', 'hero')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$hero->id}", [
            'title' => 'Hero Banner Custom Background',
            'order_position' => 1,
            'is_active' => '1',
            'bg_type' => 'custom_color',
            'bg_color' => '#1e1b4b',
            'text_mode' => 'light',
            'lead' => 'Veyra Educate',
            'highlight' => 'Wadah Super Digital',
            'tail' => 'Sekolah Anda',
        ]);

        $response->assertRedirect('/admin/homepage');

        $hero->refresh();
        $this->assertEquals('custom_color', $hero->getBgType());
        $this->assertEquals('#1e1b4b', $hero->field('bg_color'));
        $this->assertTrue($hero->hasCustomBackground());
        $this->assertTrue($hero->isDarkBackground());

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('background-color: #1e1b4b;', false);
    }

    public function test_admin_can_set_custom_background_image_url(): void
    {
        $features = HomepageSection::query()->where('key', 'features')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$features->id}", [
            'title' => 'Fitur Utama Wadah',
            'order_position' => 3,
            'is_active' => '1',
            'bg_type' => 'custom_image',
            'bg_image_url_manual' => 'https://images.unsplash.com/photo-custom.jpg',
            'bg_overlay' => 'dark-80',
            'badge' => 'Fitur Unggulan',
        ]);

        $response->assertRedirect('/admin/homepage');

        $features->refresh();
        $this->assertEquals('custom_image', $features->getBgType());
        $this->assertEquals('https://images.unsplash.com/photo-custom.jpg', $features->field('bg_image_url'));
        $this->assertEquals('dark-80', $features->field('bg_overlay'));
        $this->assertTrue($features->hasCustomBackground());

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('background-image: url(&#039;https://images.unsplash.com/photo-custom.jpg&#039;);', false);
    }

    public function test_admin_can_set_custom_image_on_grid_cards(): void
    {
        $features = HomepageSection::query()->where('key', 'features')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$features->id}", [
            'title' => 'Fitur Unggulan dengan Foto',
            'order_position' => 3,
            'is_active' => '1',
            'badge' => 'Fitur Lengkap',
            'items' => [
                [
                    'title' => 'Portal Siswa Modern',
                    'body' => 'Akses materi dan tugas real-time.',
                    'accent' => 'brand',
                    'image_url' => 'https://images.unsplash.com/photo-student-card.jpg',
                ],
                [
                    'title' => 'E-Rapor Kurikulum Merdeka',
                    'body' => 'Otomatisasi kalkulasi nilai sekolah.',
                    'accent' => 'emerald',
                    'image_url' => 'https://images.unsplash.com/photo-report-card.jpg',
                ],
            ],
        ]);

        $response->assertRedirect('/admin/homepage');

        $features->refresh();
        $items = $features->field('items', []);
        $this->assertCount(2, $items);
        $this->assertEquals('https://images.unsplash.com/photo-student-card.jpg', $items[0]['image_url']);
        $this->assertEquals('https://images.unsplash.com/photo-report-card.jpg', $items[1]['image_url']);

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Portal Siswa Modern');
        $homeResponse->assertSee('https://images.unsplash.com/photo-student-card.jpg');
        $homeResponse->assertSee('https://images.unsplash.com/photo-report-card.jpg');
    }

    public function test_admin_can_set_cta_photo_model(): void
    {
        $cta = HomepageSection::query()->where('key', 'cta')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$cta->id}", [
            'title' => 'Bergabung Bersama Kami Sekarang',
            'order_position' => 7,
            'is_active' => '1',
            'badge' => 'Ajakan',
            'image_url_manual' => 'https://images.unsplash.com/photo-model-school.jpg',
            'image_position' => 'left',
            'btn_text' => 'Daftar Gratis',
            'btn_url' => '/register',
            'wa_text' => 'WhatsApp Kami',
            'wa_url' => 'https://wa.me/62812345678',
        ]);

        $response->assertRedirect('/admin/homepage');

        $cta->refresh();
        $this->assertEquals('https://images.unsplash.com/photo-model-school.jpg', $cta->field('image_url'));
        $this->assertEquals('left', $cta->field('image_position'));

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('https://images.unsplash.com/photo-model-school.jpg');
        $homeResponse->assertSee('Bergabung Bersama Kami Sekarang');
    }

    public function test_homepage_renders_testimonials_section(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dipercaya oleh puluhan sekolah');
        $response->assertSee('Drs. H. Mulyadi, M.Pd.');
        $response->assertSee('SMA Bina Bangsa');
        $response->assertSee('SMK Prestasi Nusantara');
    }

    public function test_admin_can_create_testimonials_section(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/homepage', [
            'title' => 'Ulasan Pimpinan Sekolah',
            'section_type' => 'testimonials',
            'order_position' => 3,
            'is_active' => '1',
            'badge' => 'Kata Mereka',
            'subtitle' => 'Kesan dan pesan dari sekolah pengguna.',
            'testimonials' => [
                [
                    'name' => 'Budi Santoso, S.Pd.',
                    'role' => 'Wakil Kepala Kurikulum',
                    'school' => 'MAN 1 Metro',
                    'quote' => 'Sistem CBT berjalan lancar tanpa kendala untuk 800 siswa serentak.',
                    'rating' => '5',
                    'avatar_url' => 'https://images.unsplash.com/photo-budi.jpg',
                ],
            ],
        ]);

        $response->assertRedirect('/admin/homepage');
        $this->assertDatabaseHas('homepage_sections', [
            'title' => 'Ulasan Pimpinan Sekolah',
            'section_type' => 'testimonials',
        ]);

        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Budi Santoso, S.Pd.');
        $homeResponse->assertSee('MAN 1 Metro');
        $homeResponse->assertSee('Sistem CBT berjalan lancar');
    }

    public function test_admin_can_update_testimonials_section(): void
    {
        $testimonial = HomepageSection::query()->where('section_type', 'testimonials')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$testimonial->id}", [
            'title' => 'Ulasan Diperbarui',
            'order_position' => $testimonial->order_position,
            'is_active' => '1',
            'badge' => 'Ulasan Baru',
            'subtitle' => 'Subjudul baru testimoni',
            'testimonials' => [
                [
                    'name' => 'Hj. Nurul Hidayah, M.Pd.',
                    'role' => 'Kepala Madrasah',
                    'school' => 'MTs Negeri 2 Malang',
                    'quote' => 'Pelayanan sangat memuaskan dan sistem sangat mudah digunakan oleh guru sepuh sekalipun.',
                    'rating' => '5',
                ],
            ],
        ]);

        $response->assertRedirect('/admin/homepage');

        $testimonial->refresh();
        $this->assertEquals('Ulasan Diperbarui', $testimonial->title);
        $items = $testimonial->field('items', []);
        $this->assertCount(1, $items);
        $this->assertEquals('Hj. Nurul Hidayah, M.Pd.', $items[0]['name']);
        $this->assertEquals('MTs Negeri 2 Malang', $items[0]['school']);

        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Hj. Nurul Hidayah, M.Pd.');
        $homeResponse->assertSee('MTs Negeri 2 Malang');
    }

    public function test_homepage_renders_app_showcase_section(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Katalog Aplikasi');
        $response->assertSee('Ragam Aplikasi Siap Tanam untuk Sekolah Anda');
        $response->assertSee('CBT & Ujian Online');
        $response->assertSee('Presensi & Absensi Siswa');
        $response->assertSee('E-Rapor Kurikulum Merdeka');
    }

    public function test_admin_can_create_app_showcase_section(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/homepage', [
            'title' => 'Koleksi Aplikasi Sekolah Baru',
            'section_type' => 'app_showcase',
            'order_position' => 4,
            'is_active' => '1',
            'badge' => 'Modul Terlengkap',
            'subtitle' => 'Pilihan aplikasi pendidikan siap pakai.',
            'source_mode' => 'custom',
            'items' => [
                [
                    'name' => 'Koperasi Siswa Digital',
                    'category' => 'Keuangan',
                    'description' => 'Aplikasi POS kasir dan saldo tabungan koperasi siswa berbasis kartu pelajar.',
                    'status_badge' => 'Siap Pakai',
                    'accent' => 'emerald',
                    'tags' => 'Kasir POS, Kartu Siswa, Saldo',
                    'icon' => 'wallet',
                ],
            ],
        ]);

        $response->assertRedirect('/admin/homepage');
        $this->assertDatabaseHas('homepage_sections', [
            'title' => 'Koleksi Aplikasi Sekolah Baru',
            'section_type' => 'app_showcase',
        ]);

        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Koperasi Siswa Digital');
        $homeResponse->assertSee('Kasir POS');
        $homeResponse->assertSee('Kartu Siswa');
    }

    public function test_admin_can_update_app_showcase_section(): void
    {
        $showcase = HomepageSection::query()->where('section_type', 'app_showcase')->firstOrFail();

        $response = $this->actingAs($this->admin)->put("/admin/homepage/{$showcase->id}", [
            'title' => 'Showcase Aplikasi Diperbarui',
            'order_position' => $showcase->order_position,
            'is_active' => '1',
            'badge' => 'Katalog Unggulan',
            'subtitle' => 'Katalog aplikasi unggulan kami.',
            'source_mode' => 'custom',
            'items' => [
                [
                    'name' => 'Lab Komputer Virtual',
                    'category' => 'Praktikum',
                    'description' => 'Simulasi praktikum sains dan coding interaktif langsung dari browser.',
                    'status_badge' => 'Bisa Ditanam',
                    'accent' => 'sky',
                    'tags' => 'Virtual Lab, Sains, Coding',
                    'icon' => 'desktop-computer',
                ],
            ],
        ]);

        $response->assertRedirect('/admin/homepage');

        $showcase->refresh();
        $this->assertEquals('Showcase Aplikasi Diperbarui', $showcase->title);
        $items = $showcase->field('items', []);
        $this->assertCount(1, $items);
        $this->assertEquals('Lab Komputer Virtual', $items[0]['name']);

        $homeResponse = $this->get('/');
        $homeResponse->assertSee('Lab Komputer Virtual');
    }
}
