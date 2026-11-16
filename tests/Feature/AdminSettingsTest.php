<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\HeroTitle;
use App\Support\MidtransConfig;
use App\Support\WhatsAppConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private string $settingsPath;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_admin_can_see_settings_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $response = $this->get('/admin/pengaturan');
        $response->assertStatus(200);
        $response->assertViewIs('admin.settings.index');
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $response = $this->put('/admin/pengaturan', [
            'company_name' => 'PT Veyra Edukasi',
            'signer_name' => 'Ahmad Fauzi',
            'signer_title' => 'Finance Manager',
            'signer_city' => 'Sumenep',
            'lead' => 'New lead text',
            'highlight' => 'New highlight',
            'tail' => 'New tail text',
            'width' => 800,
            'height' => 600,
            'server_key' => 'new-server-key',
            'client_key' => 'new-client-key',
            'is_production' => false,
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('status');

        $this->assertEquals('New lead text', HeroTitle::get()['lead']);
        $this->assertEquals('new-server-key', MidtransConfig::get()['server_key']);
    }

    public function test_settings_validation_fails_on_empty_company_name(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $response = $this->put('/admin/pengaturan', [
            'company_name' => '',
            'signer_name' => 'Ahmad Fauzi',
            'signer_title' => 'Finance Manager',
            'signer_city' => 'Sumenep',
        ]);

        $response->assertSessionHasErrors('company_name');
    }

    public function test_settings_apply_midtrans_config(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $this->put('/admin/pengaturan', [
            'company_name' => 'PT Veyra Edukasi',
            'signer_name' => 'Ahmad Fauzi',
            'signer_title' => 'Finance Manager',
            'signer_city' => 'Sumenep',
            'lead' => 'Test',
            'highlight' => 'Test',
            'tail' => 'Test',
            'width' => 800,
            'height' => 600,
            'server_key' => 'admin-server-key',
            'client_key' => 'admin-client-key',
            'is_production' => true,
        ]);

        // After admin sets config, it should be applied to runtime
        MidtransConfig::apply();
        $this->assertEquals('admin-server-key', config('midtrans.server_key'));
        $this->assertTrue(config('midtrans.is_production'));
    }

    public function test_admin_can_update_whatsapp_settings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $response = $this->put('/admin/pengaturan', [
            'company_name' => 'PT Veyra Edukasi',
            'signer_name' => 'Ahmad Fauzi',
            'signer_title' => 'Finance Manager',
            'signer_city' => 'Sumenep',
            'whatsapp_enabled' => true,
            'whatsapp_gateway' => 'fonnte',
            'whatsapp_api_token' => 'test-token-12345',
            'whatsapp_sender_number' => '085707074778',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('status');

        $cfg = WhatsAppConfig::get();
        $this->assertTrue($cfg['enabled']);
        $this->assertSame('fonnte', $cfg['gateway']);
        $this->assertSame('test-token-12345', $cfg['api_token']);
    }

    public function test_admin_can_send_test_whatsapp_message(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $response = $this->post(route('admin.settings.test-whatsapp'), [
            'test_phone' => '085707074778',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('status');
    }
}
