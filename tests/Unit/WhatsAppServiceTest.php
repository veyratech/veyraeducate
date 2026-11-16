<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\WhatsAppService;
use App\Support\WhatsAppConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_normalizes_indonesian_phone_numbers(): void
    {
        $service = new WhatsAppService;

        $this->assertEquals('6281234567890', $service->normalizePhone('081234567890'));
        $this->assertEquals('6281234567890', $service->normalizePhone('81234567890'));
        $this->assertEquals('6281234567890', $service->normalizePhone('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', $service->normalizePhone('6281234567890'));
    }

    public function test_it_falls_back_to_log_when_gateway_is_disabled_or_token_is_empty(): void
    {
        WhatsAppConfig::save([
            'enabled' => false,
            'gateway' => 'fonnte',
            'api_token' => '',
        ]);

        $service = new WhatsAppService;
        $result = $service->sendMessage('081234567890', 'Pesan pengujian');

        $this->assertTrue($result);
    }

    public function test_it_sends_http_request_when_configured(): void
    {
        WhatsAppConfig::save([
            'enabled' => true,
            'gateway' => 'fonnte',
            'api_token' => 'secret-test-token',
        ]);

        Http::fake([
            'https://api.fonnte.com/send' => Http::response(['status' => true], 200),
        ]);

        $service = new WhatsAppService;
        $result = $service->sendMessage('081234567890', 'Halo Veyra');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '6281234567890'
                && $request->hasHeader('Authorization', 'secret-test-token');
        });
    }

    public function test_it_sends_test_message_successfully(): void
    {
        WhatsAppConfig::save([
            'enabled' => false,
            'gateway' => 'log',
            'api_token' => '',
        ]);

        $service = new WhatsAppService;
        $this->assertTrue($service->sendTestMessage('081299998888'));
    }
}
