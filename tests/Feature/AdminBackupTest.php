<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_database_backup(): void
    {
        $response = $this->get(route('admin.backup.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_access_database_backup(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.backup.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_database_backup_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.backup.index'));
        $response->assertOk()
            ->assertSee('Database &amp; Cadangan Sistem', false)
            ->assertSee('Unduh Backup SQL')
            ->assertSee('Optimasi Tabel');
    }

    public function test_admin_can_download_database_sql_backup(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.backup.download'));

        $response->assertOk();
        $this->assertStringContainsString('backup-veyraeducate-', (string) $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.sql', (string) $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Database Backup', $content);
        $this->assertStringContainsString('users', $content);
    }

    public function test_admin_can_optimize_database(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.backup.optimize'));
        $response->assertRedirect();
        $response->assertSessionHas('status');
    }
}
