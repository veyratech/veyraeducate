<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\File;

class WhatsAppConfig
{
    public static function getPath(): string
    {
        return storage_path('app/settings/whatsapp.json');
    }

    /**
     * @return array{enabled: bool, gateway: string, api_token: string, sender_number: string}
     */
    public static function get(): array
    {
        $path = self::getPath();
        $defaults = [
            'enabled' => (bool) env('WHATSAPP_ENABLED', false),
            'gateway' => (string) env('WHATSAPP_GATEWAY', 'fonnte'),
            'api_token' => (string) env('WHATSAPP_API_TOKEN', ''),
            'sender_number' => (string) env('WHATSAPP_SENDER_NUMBER', ''),
        ];

        if (! is_file($path)) {
            return $defaults;
        }

        $saved = json_decode((string) File::get($path), true) ?: [];

        return [
            'enabled' => isset($saved['enabled']) ? (bool) $saved['enabled'] : $defaults['enabled'],
            'gateway' => ! empty($saved['gateway']) ? (string) $saved['gateway'] : $defaults['gateway'],
            'api_token' => ! empty($saved['api_token']) ? (string) $saved['api_token'] : $defaults['api_token'],
            'sender_number' => ! empty($saved['sender_number']) ? (string) $saved['sender_number'] : $defaults['sender_number'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $path = self::getPath();
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
