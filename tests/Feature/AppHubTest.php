<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\WebApp;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppHubTest extends TestCase
{
    use RefreshDatabase;

    private function schoolUser(): User
    {
        $school = School::factory()->create();

        return User::factory()->create(['school_id' => $school->id]);
    }

    public function test_guest_cannot_open_app_hub(): void
    {
        $this->get('/apps')->assertRedirect();
    }

    public function test_app_hub_shows_empty_state_without_dummy_apps(): void
    {
        $this->seed(DatabaseSeeder::class);
        WebApp::query()->forceDelete();

        $user = $this->schoolUser();

        $this->actingAs($user)
            ->get('/apps')
            ->assertOk()
            ->assertSee('Belum ada aplikasi')
            ->assertSee('Aplikasi yang Anda tambahkan akan muncul di App Hub.');

        $this->assertDatabaseCount('web_apps', 0);
    }

    public function test_draft_app_is_hidden_from_app_hub(): void
    {
        $user = $this->schoolUser();

        WebApp::factory()->create([
            'slug' => 'draft-rahasia',
            'name' => 'Aplikasi Draft Tersembunyi',
            'access_url' => null,
        ]);

        $this->actingAs($user)
            ->get('/apps')
            ->assertOk()
            ->assertDontSee('Aplikasi Draft Tersembunyi');
    }

    public function test_disabled_app_is_hidden_from_app_hub(): void
    {
        $user = $this->schoolUser();

        WebApp::factory()->create([
            'slug' => 'nonaktif',
            'name' => 'Aplikasi Nonaktif',
            'access_url' => 'https://nonaktif.veyra.id',
            'enabled' => false,
        ]);

        $this->actingAs($user)
            ->get('/apps')
            ->assertOk()
            ->assertDontSee('Aplikasi Nonaktif');
    }

    public function test_deployed_app_appears_as_card_with_open_button(): void
    {
        $user = $this->schoolUser();

        WebApp::factory()->create([
            'slug' => 'kasir',
            'name' => 'Kasir Sekolah',
            'access_url' => 'https://kasir.veyra.id',
        ]);
        $user->school->grantAllAppAccess();

        $this->actingAs($user)
            ->get('/apps')
            ->assertOk()
            ->assertSee('Kasir Sekolah')
            ->assertSee('Buka Aplikasi')
            ->assertSee(route('sso.launch', 'kasir'), false);
    }

    public function test_deployed_app_installed_on_server_appears_in_app_hub(): void
    {
        $user = $this->schoolUser();

        $this->installSchoolApp('absensi', [], ['name' => 'Absensi Digital']);
        $user->school->grantAllAppAccess();

        $this->actingAs($user)
            ->get('/apps')
            ->assertOk()
            ->assertSee('Absensi Digital')
            ->assertSee('Buka Aplikasi');
    }

    public function test_draft_app_cannot_be_opened(): void
    {
        $user = $this->schoolUser();

        WebApp::factory()->create([
            'slug' => 'draft-rahasia',
            'access_url' => null,
        ]);
        $user->school->grantAllAppAccess();

        $this->actingAs($user)
            ->get('/apps/sekolah/draft-rahasia')
            ->assertNotFound();
    }

    public function test_disabled_app_cannot_be_opened(): void
    {
        $user = $this->schoolUser();

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);
        $user->school->grantAllAppAccess();

        WebApp::query()->where('slug', 'rapor')->update(['enabled' => false]);

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertNotFound();
    }
}
