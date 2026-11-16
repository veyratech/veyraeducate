<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolAppAssetRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_url_is_served_for_school_with_access(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [
            'index.blade.php' => '<div>Rapor Digital</div>',
            'app.css' => 'body { color: red; }',
        ], ['name' => 'Rapor Digital']);

        $school->grantAllAppAccess();

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor/asset/app.css')
            ->assertOk()
            ->assertSee('color: red', false);
    }

    public function test_asset_url_requires_authentication(): void
    {
        $this->installSchoolApp('rapor', [
            'index.blade.php' => '<div>Rapor Digital</div>',
            'app.css' => 'body { color: red; }',
        ], ['name' => 'Rapor Digital']);

        $this->get('/apps/sekolah/rapor/asset/app.css')
            ->assertRedirect('/login');
    }

    public function test_app_route_still_serves_the_installed_view(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [
            'index.blade.php' => '<div>Rapor Digital</div>',
        ], ['name' => 'Rapor Digital']);

        $school->grantAllAppAccess();

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertOk()
            ->assertSee('Rapor Digital', false);
    }
}
