<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\File;

class ContactConfig
{
    public static function getPath(): string
    {
        return storage_path('app/settings/contact.json');
    }

    /**
     * @return array<string, string>
     */
    public static function get(): array
    {
        $path = self::getPath();
        $defaults = [
            'whatsapp_number' => '085707074778',
            'email' => 'veyracreativetech@gmail.com',
            'address' => 'Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep KodePos: 69461',
        ];

        if (! is_file($path)) {
            return $defaults;
        }

        $saved = json_decode((string) File::get($path), true) ?: [];

        return array_merge($defaults, array_filter($saved, fn ($v) => $v !== null && $v !== ''));
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
