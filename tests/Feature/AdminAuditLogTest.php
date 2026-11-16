<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminUserSeeder::class);
    }

    public function test_admin_can_view_audit_logs_page(): void
    {
        $admin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

        AuditLog::record('test_action', null, ['detail' => 'testing audit log']);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

        $response->assertOk()
            ->assertSee('Audit Log &amp; Jejak Aktivitas', false)
            ->assertSee('test_action');
    }

    public function test_non_admin_cannot_view_audit_logs(): void
    {
        $user = User::factory()->create(['role' => 'school']);

        $this->actingAs($user)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }
}
