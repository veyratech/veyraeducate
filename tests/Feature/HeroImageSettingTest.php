<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\HeroImage;
use App\Support\HeroTitle;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HeroImageSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        HeroImage::clear();
        HeroTitle::reset();
    }

    protected function tearDown(): void
    {
        HeroImage::clear();
        HeroTitle::reset();
        parent::tearDown();
    }

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);

        return User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_guest_cannot_access_settings(): void
    {
        $this->get('/admin/pengaturan')->assertRedirect('/admin/login');
    }

    public function test_school_user_forbidden_on_settings(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/pengaturan')->assertForbidden();
    }

    public function test_admin_sees_settings_page_without_hero(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/pengaturan')
            ->assertOk()
            ->assertSee('Pengaturan Sistem')
            ->assertSee('Midtrans');
    }

    public function test_homepage_shows_default_illustration_when_no_hero(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Panel sekolah')
            ->assertDontSee('images/marketing/hero-homepage');
    }

    public function test_admin_can_save_all_settings_via_unified_form(): void
    {
        $admin = $this->admin();
        $image = UploadedFile::fake()->image('hero.jpg', 720, 1280);

        $this->actingAs($admin)
            ->put('/admin/pengaturan', [
                'lead' => 'Platform sekolah modern,',
                'highlight' => 'gratis coba',
                'tail' => 'untuk satuan pendidikan.',
                'width' => 720,
                'height' => 1280,
                'server_key' => 'VT-test-server-key',
                'client_key' => 'vt-test-client-key',
                'is_production' => false,
                'hero_image' => $image,
            ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('status');

        $this->assertTrue(HeroImage::exists());

        $title = HeroTitle::get();
        $this->assertSame('Platform sekolah modern,', $title['lead']);
        $this->assertSame('gratis coba', $title['highlight']);
        $this->assertSame('untuk satuan pendidikan.', $title['tail']);

        $size = HeroImage::displaySize();
        $this->assertSame(720, $size['width']);
        $this->assertSame(1280, $size['height']);

        $this->get('/')
            ->assertOk()
            ->assertSee('images/marketing/hero-homepage.jpg', false)
            ->assertSee('aspect-ratio: 720 / 1280', false)
            ->assertSee('Platform sekolah modern,')
            ->assertSee('gratis coba')
            ->assertSee('untuk satuan pendidikan.')
            ->assertDontSee('Panel sekolah');
    }

    public function test_admin_can_update_settings_without_uploading_image(): void
    {
        $admin = $this->admin();
        $image = UploadedFile::fake()->image('hero.jpg', 720, 1280);

        $this->actingAs($admin)
            ->put('/admin/pengaturan', [
                'lead' => 'Test lead',
                'highlight' => 'Test highlight',
                'tail' => 'Test tail',
                'width' => 640,
                'height' => 480,
                'server_key' => 'VT-new-server',
                'client_key' => 'vt-new-client',
                'is_production' => true,
            ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('status');

        $this->assertFalse(HeroImage::exists());

        $title = HeroTitle::get();
        $this->assertSame('Test lead', $title['lead']);
        $this->assertSame('Test highlight', $title['highlight']);
        $this->assertSame('Test tail', $title['tail']);

        $size = HeroImage::displaySize();
        $this->assertSame(640, $size['width']);
        $this->assertSame(480, $size['height']);
    }

    public function test_unified_form_rejects_invalid_dimensions(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put('/admin/pengaturan', [
                'lead' => 'Lead',
                'highlight' => 'Highlight',
                'tail' => 'Tail',
                'width' => 10,
                'height' => 10,
            ])
            ->assertSessionHasErrors(['width', 'height']);
    }

    public function test_unified_form_rejects_non_image_upload(): void
    {
        $admin = $this->admin();
        $file = UploadedFile::fake()->create('bukan-gambar.pdf', 10, 'application/pdf');

        $this->actingAs($admin)
            ->put('/admin/pengaturan', [
                'lead' => 'Lead',
                'highlight' => 'Highlight',
                'tail' => 'Tail',
                'width' => 720,
                'height' => 1280,
                'hero_image' => $file,
            ])
            ->assertSessionHasErrors('hero_image');

        $this->assertFalse(HeroImage::exists());
    }

    public function test_admin_can_clear_hero_image(): void
    {
        $admin = $this->admin();
        $image = UploadedFile::fake()->image('hero.png', 720, 1280);

        $this->actingAs($admin)
            ->put('/admin/pengaturan', [
                'lead' => 'Lead',
                'highlight' => 'Highlight',
                'tail' => 'Tail',
                'width' => 720,
                'height' => 1280,
                'hero_image' => $image,
            ])
            ->assertRedirect();

        $this->assertTrue(HeroImage::exists());

        $this->actingAs($admin)
            ->delete('/admin/pengaturan/hero')
            ->assertRedirect(route('admin.settings.index'));

        $this->assertFalse(HeroImage::exists());

        $size = HeroImage::displaySize();
        $this->assertSame(HeroImage::DEFAULT_WIDTH, $size['width']);
        $this->assertSame(HeroImage::DEFAULT_HEIGHT, $size['height']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Panel sekolah');
    }

    public function test_homepage_shows_default_hero_title(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('selamat datang di VEYRA EDUCATE')
            ->assertSee('SEKOLAH MAJU')
            ->assertSee('Tanpa Ragu');
    }
}
