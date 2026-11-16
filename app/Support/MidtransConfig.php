<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class MidtransConfig
{
    public const SANDBOX_SNAP_URL = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    public const PRODUCTION_SNAP_URL = 'https://app.midtrans.com/snap/v1/transactions';

    public const SANDBOX_API_URL = 'https://api.sandbox.midtrans.com/v2';

    public const PRODUCTION_API_URL = 'https://api.midtrans.com/v2';

    public const SANDBOX_SNAP_JS_URL = 'https://app.sandbox.midtrans.com/snap/snap.js';

    public const PRODUCTION_SNAP_JS_URL = 'https://app.midtrans.com/snap/snap.js';

    public static function getPath(): string
    {
        return config('midtrans.settings_path') ?: storage_path('app/settings/midtrans.json');
    }

    public static function get(): array
    {
        $path = self::getPath();

        if (! is_file($path)) {
            return [
                'server_key' => config('midtrans.server_key', ''),
                'client_key' => config('midtrans.client_key', ''),
                'is_production' => config('midtrans.is_production', false),
            ];
        }

        $data = json_decode(File::get($path), true) ?: [];

        return [
            'server_key' => $data['server_key'] ?? config('midtrans.server_key', ''),
            'client_key' => $data['client_key'] ?? config('midtrans.client_key', ''),
            'is_production' => (bool) ($data['is_production'] ?? config('midtrans.is_production', false)),
        ];
    }

    /**
     * Terapkan kunci & mode Midtrans yang tersimpan di panel admin ke config runtime,
     * supaya service, webhook, dan halaman pembayaran memakai endpoint yang sama.
     */
    public static function apply(): void
    {
        $data = self::get();
        $isProduction = (bool) $data['is_production'];

        config([
            'midtrans.server_key' => $data['server_key'],
            'midtrans.client_key' => $data['client_key'],
            'midtrans.is_production' => $isProduction,
            'midtrans.snap_url' => $isProduction ? self::PRODUCTION_SNAP_URL : self::SANDBOX_SNAP_URL,
            'midtrans.api_url' => $isProduction ? self::PRODUCTION_API_URL : self::SANDBOX_API_URL,
            'midtrans.snap_js_url' => $isProduction ? self::PRODUCTION_SNAP_JS_URL : self::SANDBOX_SNAP_JS_URL,
        ]);
    }

    public static function snapUrl(): string
    {
        return (bool) config('midtrans.is_production', false)
            ? self::PRODUCTION_SNAP_URL
            : self::SANDBOX_SNAP_URL;
    }

    public static function isProduction(): bool
    {
        return (bool) config('midtrans.is_production', false);
    }

    public static function save(string $serverKey, string $clientKey, bool $isProduction): void
    {
        File::ensureDirectoryExists(dirname(self::getPath()));

        File::put(self::getPath(), json_encode([
            'server_key' => $serverKey,
            'client_key' => $clientKey,
            'is_production' => $isProduction,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
