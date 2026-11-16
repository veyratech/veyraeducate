<?php

namespace Tests\Feature;

use App\Services\SchoolLookupService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SchoolLookupServiceTest extends TestCase
{
    private SchoolLookupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SchoolLookupService::class);
    }

    /** @test */
    public function it_returns_error_for_invalid_code_length()
    {
        $result = $this->service->lookup('abc');
        $this->assertFalse($result['found']);
        $this->assertStringContainsString('NPSN', $result['message'] ?? '');
        $this->assertStringContainsString('NSM', $result['message'] ?? '');
    }

    /** @test */
    public function it_returns_error_for_too_short_code()
    {
        $result = $this->service->lookup('12345');
        $this->assertFalse($result['found']);
        $this->assertStringContainsString('8 digit', $result['message']);
    }

    /** @test */
    public function it_returns_error_for_too_long_code()
    {
        $result = $this->service->lookup('12345678901234');
        $this->assertFalse($result['found']);
        $this->assertStringContainsString('13 digit', $result['message']);
    }

    /** @test */
    public function it_looks_up_npsn_via_fallback_when_primary_fails()
    {
        // Fazrian API will fail in test environment (DNS), so it should fall back to Vercel
        Http::fake([
            'api.fazriansyah.eu.org/*' => Http::response(['error' => 'unavailable'], 502),
            'api-sekolah-indonesia.vercel.app/*' => Http::response([
                'dataSekolah' => [
                    [
                        'npsn'           => '20103045',
                        'sekolah'        => 'SDIT AL BIRUNI',
                        'kabupaten_kota' => 'Kota Jakarta Selatan',
                        'propinsi'       => 'Prov. D.K.I. Jakarta',
                        'bentuk'         => 'SD',
                        'status'         => 'S',
                    ],
                ],
            ]),
        ]);

        $result = $this->service->lookup('20103045');

        $this->assertTrue($result['found']);
        $this->assertEquals('SDIT AL BIRUNI', $result['name']);
        $this->assertEquals('dapodik', $result['source']);
    }

    /** @test */
    public function it_resolves_npsn_from_vercel_fallback()
    {
        Http::fake([
            'api-sekolah-indonesia.vercel.app/*' => Http::response([
                'dataSekolah' => [
                    [
                        'npsn'           => '20103045',
                        'sekolah'        => 'SD NEGERI CENDANA III',
                        'kabupaten_kota' => 'Jakarta Pusat',
                        'propinsi'       => 'DKI Jakarta',
                        'bentuk'         => 'SD',
                        'status'         => 'N',
                        'alamat_jalan'   => 'Jl. Cendrana No. 5',
                    ],
                ],
            ]),
        ]);

        $result = $this->service->lookup('20103045');

        $this->assertTrue($result['found']);
        $this->assertEquals('SD NEGERI CENDANA III', $result['name']);
        $this->assertEquals('Negeri', $result['status']);
        $this->assertEquals('Jakarta Pusat, DKI Jakarta', $result['city']);
        $this->assertEquals('Jl. Cendrana No. 5', $result['address']);
    }

    /** @test */
    public function it_marks_madrasah_school_correctly()
    {
        Http::fake([
            'api-sekolah-indonesia.vercel.app/*' => Http::response([
                'dataSekolah' => [
                    [
                        'npsn'           => '20103045',
                        'sekolah'        => 'MTs NEGERI SUMENEP',
                        'kabupaten_kota' => 'Kab. Sumenep',
                        'propinsi'       => 'Jawa Timur',
                        'bentuk'         => 'MTs',
                        'status'         => 'N',
                    ],
                ],
            ]),
        ]);

        $result = $this->service->lookup('20103045');

        $this->assertTrue($result['found']);
        $this->assertEquals('Kemenag', $result['lembaga']);
        $this->assertEquals('Madrasah', true === str_contains(strtoupper($result['name']), 'MTS'));
    }

    /** @test */
    public function it_returns_not_found_when_npsn_does_not_exist()
    {
        Http::fake([
            '*' => Http::response(['dataSekolah' => []], 200),
        ]);

        $result = $this->service->lookup('99999999');

        $this->assertFalse($result['found']);
        $this->assertStringContainsString('tidak ditemukan', strtolower($result['message']));
    }

    /** @test */
    public function it_handles_emis_nsm_lookup_failure_gracefully()
    {
        // EMIS Kemenag is a SPA; simulate failure
        Http::fake([
            'appmadrasah.kemenag.go.id/*' => Http::response('<html></html>', 200),
            'api-sekolah-indonesia.vercel.app/*' => Http::response(['dataSekolah' => []], 200),
        ]);

        $result = $this->service->lookup('121235290091');

        // Falls through both NPSN and NSM sources → not found
        $this->assertFalse($result['found']);
    }
}
