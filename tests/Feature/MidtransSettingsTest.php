<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\Services\MidtransService;
use App\Support\MidtransConfig;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class MidtransSettingsTest extends TestCase
{
    use RefreshDatabase;

    private string $settingsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);

        $this->settingsPath = storage_path('framework/testing/midtrans-'.uniqid().'.json');
        config(['midtrans.settings_path' => $this->settingsPath]);
    }

    protected function tearDown(): void
    {
        if (is_file($this->settingsPath)) {
            unlink($this->settingsPath);
        }

        parent::tearDown();
    }

    public function test_saved_settings_are_applied_to_runtime_config(): void
    {
        MidtransConfig::save('Mid-server-sandbox-key', 'Mid-client-sandbox-key', false);
        MidtransConfig::apply();

        $this->assertSame('Mid-server-sandbox-key', config('midtrans.server_key'));
        $this->assertSame('Mid-client-sandbox-key', config('midtrans.client_key'));
        $this->assertFalse(config('midtrans.is_production'));
        $this->assertSame(MidtransConfig::SANDBOX_SNAP_URL, config('midtrans.snap_url'));
        $this->assertSame(MidtransConfig::SANDBOX_SNAP_JS_URL, config('midtrans.snap_js_url'));

        MidtransConfig::save('Mid-server-production-key', 'Mid-client-production-key', true);
        MidtransConfig::apply();

        $this->assertSame('Mid-server-production-key', config('midtrans.server_key'));
        $this->assertTrue(config('midtrans.is_production'));
        $this->assertSame(MidtransConfig::PRODUCTION_SNAP_URL, config('midtrans.snap_url'));
        $this->assertSame(MidtransConfig::PRODUCTION_API_URL, config('midtrans.api_url'));
        $this->assertSame(MidtransConfig::PRODUCTION_SNAP_JS_URL, config('midtrans.snap_js_url'));
    }

    public function test_runtime_config_is_used_when_settings_file_is_missing(): void
    {
        config([
            'midtrans.server_key' => 'Mid-server-from-env',
            'midtrans.is_production' => false,
        ]);

        MidtransConfig::apply();

        $this->assertFileDoesNotExist($this->settingsPath);
        $this->assertSame('Mid-server-from-env', config('midtrans.server_key'));
        $this->assertSame(MidtransConfig::SANDBOX_SNAP_URL, config('midtrans.snap_url'));
    }

    public function test_snap_order_reports_unauthorized_with_mode_hint(): void
    {
        config(['midtrans.server_key' => 'Mid-server-test-key', 'midtrans.is_production' => false]);

        Http::preventStrayRequests();
        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);

        try {
            (new MidtransService)->createSnapOrder($school, Plan::findByCode('semester'), $user);
            $this->fail('RuntimeException tidak dilempar untuk respon 401.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Unauthorized', $exception->getMessage());
            $this->assertStringContainsString('mode sandbox', $exception->getMessage());
            $this->assertStringContainsString('Koneksi Midtrans', $exception->getMessage());
        }
    }

    public function test_missing_server_key_points_to_admin_settings(): void
    {
        config(['midtrans.server_key' => null]);

        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);

        try {
            (new MidtransService)->createSnapOrder($school, Plan::findByCode('semester'), $user);
            $this->fail('RuntimeException tidak dilempar untuk kunci kosong.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Pengaturan', $exception->getMessage());
            $this->assertStringContainsString('Koneksi Midtrans', $exception->getMessage());
        }
    }
}
