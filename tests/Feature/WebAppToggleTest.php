<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebApp;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppToggleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);

        return User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_admin_can_toggle_app_status_by_slug(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor');

        $app = WebApp::query()->where('slug', 'rapor')->firstOrFail();
        $this->assertTrue($app->enabled);

        $this->actingAs($admin)
            ->patch(route('admin.web-apps.toggle', 'rapor'))
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertFalse($app->fresh()->enabled);

        $this->actingAs($admin)
            ->patch(route('admin.web-apps.toggle', 'rapor'))
            ->assertRedirect(route('admin.web-apps.index'));

        $this->assertTrue($app->fresh()->enabled);
    }

    public function test_toggling_unknown_app_returns_404(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/aplikasi/tidak-ada/toggle')
            ->assertNotFound();

        $this->assertDatabaseMissing('web_apps', ['slug' => 'tidak-ada']);
    }

    public function test_school_user_cannot_toggle_app(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/admin/aplikasi/rapor/toggle')
            ->assertForbidden();
    }
}
