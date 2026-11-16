<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class HeroTitle
{
    public const DEFAULT_LEAD = 'selamat datang di VEYRA EDUCATE';

    public const DEFAULT_HIGHLIGHT = 'SEKOLAH MAJU';

    public const DEFAULT_TAIL = 'Tanpa Ragu';

    /**
     * @return array{lead: string, highlight: string, tail: string}
     */
    public static function get(): array
    {
        $path = self::path();

        if (is_file($path)) {
            $data = json_decode((string) File::get($path), true);

            if (is_array($data)) {
                return [
                    'lead' => (string) ($data['lead'] ?? self::DEFAULT_LEAD),
                    'highlight' => (string) ($data['highlight'] ?? self::DEFAULT_HIGHLIGHT),
                    'tail' => (string) ($data['tail'] ?? self::DEFAULT_TAIL),
                ];
            }
        }

        return [
            'lead' => self::DEFAULT_LEAD,
            'highlight' => self::DEFAULT_HIGHLIGHT,
            'tail' => self::DEFAULT_TAIL,
        ];
    }

    public static function save(string $lead, string $highlight, string $tail): void
    {
        File::ensureDirectoryExists(dirname(self::path()));

        File::put(self::path(), json_encode([
            'lead' => $lead,
            'highlight' => $highlight,
            'tail' => $tail,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public static function reset(): void
    {
        if (is_file(self::path())) {
            @unlink(self::path());
        }
    }

    private static function path(): string
    {
        return storage_path('app/marketing/hero-title.json');
    }
}
