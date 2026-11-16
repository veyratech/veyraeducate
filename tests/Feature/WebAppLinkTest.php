<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_create_link(): void
    {
        $school = User::factory()->create();

        $this->actingAs($school)
            ->post(route('admin.web-apps.store'), [
                'name' => 'Rapor',
                'slug' => 'rapor',
                'access_url' => 'https://rapor.veyra.sch.id',
            ])
            ->assertForbidden();
    }

    public function test_external_only_app_stays_enabled(): void
    {
        WebApp::factory()->create([
            'slug' => 'rapor',
            'name' => 'Rapor Digital',
            'access_url' => 'https://rapor.veyra.sch.id',
        ]);

        $app = collect($this->app->make(SchoolAppInstaller::class)->listApps())
            ->firstWhere('slug', 'rapor');

        $this->assertNotNull($app);
        $this->assertTrue($app['enabled']);
        $this->assertSame('https://rapor.veyra.sch.id', $app['access_url']);
        $this->assertSame(route('sso.launch', 'rapor'), $app['link']);
        $this->assertFalse($app['on_disk']);
    }

    public function test_disabled_external_app_is_not_enabled(): void
    {
        WebApp::factory()->create([
            'slug' => 'rapor',
            'access_url' => 'https://rapor.veyra.sch.id',
            'enabled' => false,
        ]);

        $app = collect($this->app->make(SchoolAppInstaller::class)->listApps())
            ->firstWhere('slug', 'rapor');

        $this->assertNotNull($app);
        $this->assertFalse($app['enabled']);
    }

    public function test_dashboard_uses_external_access_url(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        WebApp::factory()->create([
            'slug' => 'rapor',
            'name' => 'Rapor Digital',
            'access_url' => 'https://rapor.veyra.sch.id',
        ]);
        $school->grantAllAppAccess();

        $app = collect($this->app->make(SchoolAppInstaller::class)->listApps())
            ->firstWhere('slug', 'rapor');

        $this->assertNotNull($app);
        $this->assertSame(route('sso.launch', 'rapor'), $app['link']);
        $this->assertSame('https://rapor.veyra.sch.id', $app['access_url']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_dashboard_uses_local_app_path_when_no_external_url(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);
        $school->grantAllAppAccess();

        $app = collect($this->app->make(SchoolAppInstaller::class)->listApps())
            ->firstWhere('slug', 'rapor');

        $this->assertNotNull($app);
        $this->assertSame(route('sso.launch', 'rapor'), $app['link']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }
}
