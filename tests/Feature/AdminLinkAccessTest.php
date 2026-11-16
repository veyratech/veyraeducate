<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminLinkAccessTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);

        return User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_guest_cannot_access_links_page(): void
    {
        $this->get('/admin/aplikasi')->assertRedirect('/admin/login');
        $this->get('/admin/link-akses')->assertRedirect('/admin/login');
    }

    public function test_school_user_forbidden_on_links_page(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/aplikasi')->assertForbidden();
        $this->actingAs($user)->get('/admin/link-akses')->assertForbidden();
    }

    public function test_admin_sees_app_manager_page(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/aplikasi')
            ->assertOk()
            ->assertSee('Semua Aplikasi')
            ->assertSee('Kelola aplikasi')
            ->assertSee('Belum ada aplikasi');
    }

    public function test_sidebar_keeps_management_and_access_separate(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/admin/aplikasi')->assertOk();

        $response->assertSee('Semua Aplikasi')
            ->assertSee('Tambah Aplikasi')
            ->assertSee('Katalog Aplikasi')
            ->assertSee(route('admin.link-access.index'), false);
    }

    public function test_link_access_page_shows_empty_state(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.link-access.index'))
            ->assertOk()
            ->assertSee('Link Akses')
            ->assertSee('Belum ada link akses');
    }

    public function test_admin_can_register_app_without_url_as_draft(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/aplikasi', [
                'name' => 'Aplikasi Draft Baru',
                'slug' => 'draft-baru',
                'description' => 'Belum di-deploy',
            ])
            ->assertRedirect('/admin/aplikasi');

        $this->assertDatabaseHas('web_apps', ['slug' => 'draft-baru', 'access_url' => null]);

        $this->actingAs($admin)
            ->get('/admin/aplikasi')
            ->assertOk()
            ->assertSee('Aplikasi Draft Baru')
            ->assertSee('Draft / Not Deployed')
            ->assertSee('Belum di-deploy — aplikasi belum muncul di App Hub dan belum bisa dibuka.');
    }

    public function test_admin_sees_default_link_for_installed_app(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->get(route('admin.link-access.index'))
            ->assertOk()
            ->assertSee('Rapor Digital')
            ->assertSee(route('sso.launch', 'rapor'), false);
    }

    public function test_admin_can_create_external_link(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.store'), [
                'name' => 'Rapor Digital',
                'slug' => 'rapor',
                'access_url' => 'https://rapor.veyra.sch.id',
                'description' => 'Aplikasi rapor',
                'category' => 'akademik',
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('web_apps', [
            'slug' => 'rapor',
            'name' => 'Rapor Digital',
            'access_url' => 'https://rapor.veyra.sch.id',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.web-apps.index'))
            ->assertOk()
            ->assertSee('Link eksternal');

        $this->actingAs($admin)
            ->get(route('admin.link-access.index'))
            ->assertOk()
            ->assertSee('https://rapor.veyra.sch.id', false)
            ->assertSee('Ubah link');
    }

    public function test_create_link_rejects_invalid_url(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.store'), [
                'name' => 'Rapor',
                'slug' => 'rapor',
                'access_url' => 'bukan-url-valid',
            ])
            ->assertSessionHasErrors('access_url');

        $this->assertDatabaseMissing('web_apps', ['slug' => 'rapor']);
    }

    public function test_create_link_rejects_duplicate_slug(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->post(route('admin.web-apps.store'), [
                'name' => 'Rapor Lain',
                'slug' => 'rapor',
                'access_url' => 'https://lain.sch.id',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_admin_can_update_link_target(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->patch(route('admin.link-access.update', 'rapor'), [
                'access_url' => 'https://rapor-baru.veyra.sch.id',
            ])
            ->assertRedirect(route('admin.link-access.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('web_apps', [
            'slug' => 'rapor',
            'access_url' => 'https://rapor-baru.veyra.sch.id',
        ]);
    }

    public function test_update_link_requires_target_when_app_is_not_on_server(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.store'), [
                'name' => 'Eksternal',
                'slug' => 'eksternal',
                'access_url' => 'https://eksternal.sch.id',
            ])
            ->assertRedirect(route('admin.web-apps.index'));

        $this->actingAs($admin)
            ->from(route('admin.link-access.index'))
            ->patch(route('admin.link-access.update', 'eksternal'), ['access_url' => ''])
            ->assertSessionHasErrors('access_url');

        $this->assertDatabaseHas('web_apps', [
            'slug' => 'eksternal',
            'access_url' => 'https://eksternal.sch.id',
        ]);
    }

    public function test_admin_can_clear_link_target_for_app_on_server(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->patch(route('admin.link-access.update', 'rapor'), [
                'access_url' => 'https://rapor-baru.veyra.sch.id',
            ])
            ->assertRedirect(route('admin.link-access.index'));

        $this->actingAs($admin)
            ->patch(route('admin.link-access.update', 'rapor'), ['access_url' => ''])
            ->assertRedirect(route('admin.link-access.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('web_apps', ['slug' => 'rapor', 'access_url' => null]);
    }

    public function test_delete_confirmation_keeps_app_name_out_of_inline_javascript(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor', [], ['name' => "Sekolah 'Budi' & \"Canggih\""]);

        $this->actingAs($admin)
            ->get('/admin/aplikasi')
            ->assertOk()
            ->assertSee('data-confirm="Hapus aplikasi Sekolah &#039;Budi&#039; &amp; &quot;Canggih&quot;?"', false)
            ->assertSee('onsubmit="return confirm(this.dataset.confirm);"', false)
            ->assertDontSee("confirm('Hapus link", false);
    }

    public function test_admin_can_remove_link(): void
    {
        $admin = $this->admin();

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->delete(route('admin.web-apps.uninstall', 'rapor'))
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertSoftDeleted('web_apps', ['slug' => 'rapor']);
        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/rapor');
    }

    public function test_upload_endpoint_is_gone(): void
    {
        $admin = $this->admin();

        $this->assertFalse(Route::has('admin.web-apps.install'));
        $this->assertFalse(Route::has('admin.web-apps.update-link'));

        $this->actingAs($admin)
            ->post('/admin/aplikasi/install', ['slug' => 'rapor'])
            ->assertStatus(405);

        $this->assertDatabaseMissing('web_apps', ['slug' => 'rapor']);
    }
}
