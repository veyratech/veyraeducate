<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class HeroBackground
{
    public const RELATIVE_DIR = 'images/marketing';

    public const FILENAME = 'hero-bg';

    /**
     * Get the full public URL of the uploaded background image, or null if none.
     */
    public static function url(): ?string
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            $path = public_path(self::RELATIVE_DIR.'/'.self::FILENAME.$ext);
            if (is_file($path)) {
                $mtime = filemtime($path);

                return asset(self::RELATIVE_DIR.'/'.self::FILENAME.$ext.'?v='.basename((string) $mtime));
            }
        }

        return null;
    }

    /**
     * Store an uploaded file as the hero background image.
     */
    public static function store(UploadedFile $file): void
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return;
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $allowedExts = ['jpeg', 'jpg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowedExts, true)) {
            return;
        }

        $dir = public_path(self::RELATIVE_DIR);
        File::ensureDirectoryExists($dir);

        self::clear();
        $ext = self::extensionFor($file);
        $file->move($dir, self::FILENAME.$ext);
    }

    /**
     * Delete the current background image.
     */
    public static function clear(): void
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            $path = public_path(self::RELATIVE_DIR.'/'.self::FILENAME.$ext);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Check if a background image exists.
     */
    public static function exists(): bool
    {
        return self::url() !== null;
    }

    private static function extensionFor(UploadedFile $file): string
    {
        return match (strtolower($file->getClientOriginalExtension())) {
            'jpeg' => '.jpg',
            'png' => '.png',
            'webp' => '.webp',
            'gif' => '.gif',
            default => '.jpg',
        };
    }
}
