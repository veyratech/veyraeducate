<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\AppGuide;
use App\Models\User;
use App\Models\WebApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_user_can_view_guides_index_without_upload_button(): void
    {
        $schoolUser = User::factory()->create(['role' => User::ROLE_SCHOOL]);

        $guide = AppGuide::create([
            'user_id' => $schoolUser->id,
            'title' => 'Panduan Rapor Digital',
            'description' => 'Tata cara pengisian rapor siswa',
            'version' => 'v1.0',
            'file_path' => 'guides/rapor.pdf',
            'file_name' => 'rapor.pdf',
            'file_size' => 1024,
            'file_type' => 'pdf',
        ]);

        $response = $this->actingAs($schoolUser)->get(route('guides.index'));

        $response->assertOk()
            ->assertSee('Buku Panduan Aplikasi')
            ->assertSee('Panduan Rapor Digital')
            ->assertDontSee('Upload Buku Panduan');
    }

    public function test_admin_user_sees_admin_panel_link_instead_of_upload_button_on_school_guides_index(): void
    {
        $admin = User::factory()->admin()->create();

        $guide = AppGuide::create([
            'user_id' => $admin->id,
            'title' => 'Panduan Rapor Digital',
            'version' => 'v1.0',
            'file_path' => 'guides/rapor.pdf',
            'file_name' => 'rapor.pdf',
            'file_size' => 1024,
            'file_type' => 'pdf',
        ]);

        $response = $this->actingAs($admin)->get(route('guides.index'));

        $response->assertOk()
            ->assertSee('Buku Panduan Aplikasi')
            ->assertSee('Kelola di Panel Admin')
            ->assertDontSee('+ Upload Buku Panduan')
            ->assertDontSee('uploadModal');
    }

    public function test_school_user_cannot_upload_guide(): void
    {
        Storage::fake('public');

        $schoolUser = User::factory()->create(['role' => User::ROLE_SCHOOL]);
        $app = WebApp::factory()->create(['name' => 'E-Perpustakaan']);

        $file = UploadedFile::fake()->create('panduan_perpustakaan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($schoolUser)->post(route('guides.store'), [
            'title' => 'Buku Panduan E-Perpustakaan',
            'web_app_id' => $app->id,
            'version' => 'v2.0',
            'file' => $file,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('app_guides', [
            'title' => 'Buku Panduan E-Perpustakaan',
        ]);
    }

    public function test_admin_can_view_admin_guides_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.guides.index'));

        $response->assertOk()
            ->assertSee('Buku Panduan Aplikasi')
            ->assertSee('Upload Buku Panduan');
    }

    public function test_admin_can_upload_guide_via_admin_portal(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $app = WebApp::factory()->create(['name' => 'E-Perpustakaan']);

        $file = UploadedFile::fake()->create('panduan_perpustakaan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.guides.store'), [
            'title' => 'Buku Panduan E-Perpustakaan',
            'web_app_id' => $app->id,
            'version' => 'v2.0',
            'description' => 'Panduan lengkap modul perpustakaan',
            'category' => 'perpustakaan',
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.guides.index'));
        $this->assertDatabaseHas('app_guides', [
            'title' => 'Buku Panduan E-Perpustakaan',
            'version' => 'v2.0',
            'web_app_id' => $app->id,
            'file_name' => 'panduan_perpustakaan.pdf',
        ]);
    }

    public function test_user_can_download_guide(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $filePath = 'guides/test-manual.pdf';
        Storage::disk('public')->put($filePath, 'dummy content');

        $guide = AppGuide::create([
            'user_id' => $user->id,
            'title' => 'Panduan Ujian Online',
            'version' => 'v1.1',
            'file_path' => $filePath,
            'file_name' => 'test-manual.pdf',
            'file_size' => 100,
            'file_type' => 'pdf',
            'download_count' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('guides.download', $guide));

        $response->assertOk();
        $this->assertEquals(1, $guide->fresh()->download_count);
    }

    public function test_school_user_cannot_delete_guide(): void
    {
        Storage::fake('public');

        $schoolUser = User::factory()->create(['role' => User::ROLE_SCHOOL]);
        $filePath = 'guides/delete-me.pdf';
        Storage::disk('public')->put($filePath, 'dummy content');

        $guide = AppGuide::create([
            'user_id' => $schoolUser->id,
            'title' => 'Panduan Hapus',
            'file_path' => $filePath,
            'file_name' => 'delete-me.pdf',
            'file_size' => 100,
            'file_type' => 'pdf',
        ]);

        $response = $this->actingAs($schoolUser)->delete(route('guides.destroy', $guide));

        $response->assertForbidden();
        $this->assertDatabaseHas('app_guides', ['id' => $guide->id]);
    }

    public function test_admin_can_delete_guide_via_admin_portal(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $filePath = 'guides/delete-me.pdf';
        Storage::disk('public')->put($filePath, 'dummy content');

        $guide = AppGuide::create([
            'user_id' => $admin->id,
            'title' => 'Panduan Hapus Admin',
            'file_path' => $filePath,
            'file_name' => 'delete-me.pdf',
            'file_size' => 100,
            'file_type' => 'pdf',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.guides.destroy', $guide));

        $response->assertRedirect(route('admin.guides.index'));
        $this->assertSoftDeleted('app_guides', ['id' => $guide->id]);
    }

    public function test_admin_can_update_guide_metadata_and_user_can_preview_pdf(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $schoolUser = User::factory()->create(['role' => User::ROLE_SCHOOL]);
        $filePath = 'guides/preview-me.pdf';
        Storage::disk('public')->put($filePath, '%PDF-1.4 dummy');

        $guide = AppGuide::create([
            'user_id' => $admin->id,
            'title' => 'Panduan Lama',
            'version' => 'v1.0',
            'category' => 'umum',
            'file_path' => $filePath,
            'file_name' => 'preview-me.pdf',
            'file_size' => 100,
            'file_type' => 'pdf',
            'download_count' => 3,
        ]);

        $this->actingAs($admin)->put(route('admin.guides.update', $guide), [
            'title' => 'Panduan CBT Terbaru',
            'version' => 'v2.1',
            'category' => 'operator',
            'description' => 'Update fitur baru',
        ])->assertRedirect(route('admin.guides.index'));

        $this->assertDatabaseHas('app_guides', [
            'id' => $guide->id,
            'title' => 'Panduan CBT Terbaru',
            'version' => 'v2.1',
            'category' => 'operator',
            'download_count' => 3,
        ]);

        $previewResponse = $this->actingAs($schoolUser)->get(route('guides.preview', $guide));
        $previewResponse->assertOk();
        $this->assertEquals(4, $guide->fresh()->download_count);
    }

    public function test_admin_can_create_draft_announcement_and_toggle_publish(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Info Pemeliharaan Server',
            'body' => 'Jadwal maintenance malam ini.',
            // is_active omitted (unchecked checkbox)
        ])->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::where('title', 'Info Pemeliharaan Server')->firstOrFail();
        $this->assertFalse($announcement->is_active);

        $this->actingAs($admin)
            ->patch(route('admin.announcements.toggle', $announcement))
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertTrue($announcement->fresh()->is_active);
    }
}
