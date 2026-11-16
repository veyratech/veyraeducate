<?php

namespace Tests\Unit;

use App\Services\SchoolLookupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SchoolLookupServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_length_returns_error(): void
    {
        $service = app(SchoolLookupService::class);
        $result = $service->lookup('abc');
        $this->assertFalse($result['found']);
        $this->assertStringContainsString('digit', $result['message'] ?? '');
    }

    public function test_npsn_resolved_from_vercel_fallback(): void
    {
        Http::fake([
            'api-sekolah-indonesia.vercel.app/*' => fn ($request) => Http::response([
                'dataSekolah' => [
                    [
                        'npsn' => '20103045',
                        'sekolah' => 'SD NEGERI TEST',
                        'kabupaten_kota' => 'Bandung',
                        'propinsi' => 'Jawa Barat',
                        'bentuk' => 'SD',
                        'status' => 'N',
                    ],
                ],
            ]),
        ]);

        $service = app(SchoolLookupService::class);
        $result = $service->lookup('20103045');
        $this->assertTrue($result['found'], json_encode($result));
        $this->assertEquals('SD NEGERI TEST', $result['name']);
        $this->assertEquals('Negeri', $result['status']);
        $this->assertEquals('Bandung, Jawa Barat', $result['city']);
    }

    public function test_not_found_returns_message(): void
    {
        Http::fake([
            'api-sekolah-indonesia.vercel.app/*' => fn () => Http::response(['dataSekolah' => []], 200),
        ]);

        $service = app(SchoolLookupService::class);
        $result = $service->lookup('99999999');

        $this->assertFalse($result['found']);
        $this->assertStringContainsString('tidak ditemukan', strtolower($result['message'] ?? ''));
    }

    public function test_emis_nsm_falls_through_to_npsn_check(): void
    {
        Http::fake([
            'appmadrasah.kemenag.go.id/*' => fn () => Http::response('<html></html>', 200),
            'api-sekolah-indonesia.vercel.app/*' => fn () => Http::response(['dataSekolah' => []], 200),
        ]);

        $service = app(SchoolLookupService::class);
        $result = $service->lookup('121235290091');

        $this->assertFalse($result['found']);
    }
}
