<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SchoolLookupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_npsn_success_with_fallback(): void
    {
        Http::fake([
            'api.fazriansyah.eu.org/*' => Http::response(['error' => 'unavailable'], 502),
            'api-sekolah-indonesia.vercel.app/*' => Http::response([
                'dataSekolah' => [
                    [
                        'npsn' => '20103045',
                        'sekolah' => 'SD NEGERI UJIAN',
                        'kabupaten_kota' => 'Jakarta Pusat',
                        'propinsi' => 'DKI Jakarta',
                        'bentuk' => 'SD',
                        'status' => 'N',
                    ],
                ],
            ]),
        ]);

        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        $this->actingAs($admin)
            ->getJson('/admin/sekolah/lookup-npsn/20103045')
            ->assertOk()
            ->assertJson([
                'found' => true,
                'name' => 'SD NEGERI UJIAN',
                'source' => 'dapodik',
            ]);
    }

    public function test_lookup_sekolah_unified_route_returns_data(): void
    {
        Http::fake([
            'api-sekolah-indonesia.vercel.app/*' => Http::response([
                'dataSekolah' => [
                    [
                        'npsn' => '20103045',
                        'sekolah' => 'SD CUKUP DIAWI',
                        'kabupaten_kota' => 'Kab. Bogor',
                        'propinsi' => 'Jawa Barat',
                        'bentuk' => 'SD',
                        'status' => 'S',
                    ],
                ],
            ]),
        ]);

        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        $this->actingAs($admin)
            ->getJson('/admin/sekolah/lookup-sekolah/20103045')
            ->assertOk()
            ->assertJsonFragment(['name' => 'SD CUKUP DIAWI'])
            ->assertJsonFragment(['city' => 'Kab. Bogor, Jawa Barat']);
    }

    public function test_lookup_returns_not_found_for_unknown_npsn(): void
    {
        Http::fake(['*' => Http::response(['dataSekolah' => []], 200)]);

        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        $this->actingAs($admin)
            ->getJson('/admin/sekolah/lookup-npsn/99999999')
            ->assertOk()
            ->assertJsonFragment(['found' => false]);
    }

    public function test_invalid_code_returns_404_due_to_route_constraint(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();

        $this->actingAs($admin)
            ->getJson('/admin/sekolah/lookup-npsn/abc')
            ->assertNotFound();
    }
}
