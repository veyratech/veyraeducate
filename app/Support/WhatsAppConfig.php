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
            'enabled' => (bool) self::readEnv('WHATSAPP_ENABLED'),
            'gateway' => (string) self::readEnv('WHATSAPP_GATEWAY') ?: 'fonnte',
            'api_token' => (string) self::readEnv('WHATSAPP_API_TOKEN'),
            'sender_number' => (string) self::readEnv('WHATSAPP_SENDER_NUMBER'),
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

    protected static function readEnv(string $key): ?string
    {
        $envPath = base_path('.env');
        if (! is_file($envPath)) {
            return null;
        }

        foreach (file($envPath) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (preg_match('/^'.preg_quote($key, '/').'\s*=\s*(.+)/', $line, $matches)) {
                $value = trim($matches[1], '"\'');

                return $value !== '' ? $value : null;
            }
        }

        return null;
    }
}
