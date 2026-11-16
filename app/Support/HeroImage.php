<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class HeroImage
{
    public const RELATIVE_DIR = 'images/marketing';

    public const FILENAME = 'hero-homepage';

    public const DEFAULT_DIR = 'images/marketing';

    public const DEFAULT_FILENAME = 'hero-default';

    public const DEFAULT_WIDTH = 500;

    public const DEFAULT_HEIGHT = 600;

    public static function absolutePath(): string
    {
        return public_path(self::RELATIVE_DIR.'/'.self::FILENAME);
    }

    public static function exists(): bool
    {
        return is_file(self::absolutePath().self::extension());
    }

    public static function url(): ?string
    {
        if (self::exists()) {
            $path = self::absolutePath().self::extension();
            $mtime = filemtime($path);

            return asset(self::RELATIVE_DIR.'/'.self::FILENAME.self::extension().'?v='.basename((string) $mtime));
        }

        return self::getDefaultUrl();
    }

    /**
     * Display box size in pixels [width, height].
     *
     * @return array{width: int, height: int}
     */
    public static function displaySize(): array
    {
        $path = self::metaPath();

        if (is_file($path)) {
            $meta = json_decode((string) File::get($path), true);

            if (is_array($meta)) {
                $width = (int) ($meta['width'] ?? 0);
                $height = (int) ($meta['height'] ?? 0);

                if ($width > 0 && $height > 0) {
                    return ['width' => $width, 'height' => $height];
                }
            }
        }

        return [
            'width' => self::DEFAULT_WIDTH,
            'height' => self::DEFAULT_HEIGHT,
        ];
    }

    public static function setDisplaySize(int $width, int $height): void
    {
        File::ensureDirectoryExists(public_path(self::RELATIVE_DIR));

        File::put(self::metaPath(), json_encode([
            'width' => $width,
            'height' => $height,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public static function store(UploadedFile $file, array $options = []): void
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

        self::clearImageOnly();

        $targetWidth = (int) ($options['width'] ?? 0);
        $targetHeight = (int) ($options['height'] ?? 0);

        if ($targetWidth > 0 && $targetHeight > 0) {
            $processorPath = __DIR__.'/ImageProcessor.php';
            if (! class_exists(ImageProcessor::class)) {
                require_once $processorPath;
            }
            $result = ImageProcessor::save($file, $dir.'/'.self::FILENAME, [
                'width' => $targetWidth,
                'height' => $targetHeight,
                'mode' => $options['mode'] ?? 'cover',
            ]);
            self::setDisplaySize($result['width'], $result['height']);
        } else {
            $file->move($dir, self::FILENAME.self::extensionFor($file));
        }
    }

    public static function clear(): void
    {
        self::clearImageOnly();

        if (is_file(self::metaPath())) {
            @unlink(self::metaPath());
        }
    }

    public static function setAsDefault(): bool
    {
        $ext = self::extension();
        if ($ext === '') {
            return false;
        }

        File::ensureDirectoryExists(public_path(self::DEFAULT_DIR));

        self::clearDefaultOnly();
        copy(
            self::absolutePath().$ext,
            self::defaultPath().$ext,
        );

        return true;
    }

    public static function getDefaultUrl(): ?string
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            $path = public_path(self::DEFAULT_DIR.'/'.self::DEFAULT_FILENAME.$ext);
            if (is_file($path)) {
                $mtime = filemtime($path);

                return asset(self::DEFAULT_DIR.'/'.self::DEFAULT_FILENAME.$ext.'?v='.basename((string) $mtime));
            }
        }

        return null;
    }

    public static function hasDefault(): bool
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            if (is_file(public_path(self::DEFAULT_DIR.'/'.self::DEFAULT_FILENAME.$ext))) {
                return true;
            }
        }

        return false;
    }

    public static function clearDefault(): void
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            $path = public_path(self::DEFAULT_DIR.'/'.self::DEFAULT_FILENAME.$ext);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    public static function resetDisplaySize(): void
    {
        if (is_file(self::metaPath())) {
            @unlink(self::metaPath());
        }
    }

    private static function clearImageOnly(): void
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            $path = public_path(self::RELATIVE_DIR.'/'.self::FILENAME.$ext);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    private static function metaPath(): string
    {
        return public_path(self::RELATIVE_DIR.'/'.self::FILENAME.'.json');
    }

    private static function defaultPath(): string
    {
        return public_path(self::DEFAULT_DIR.'/'.self::DEFAULT_FILENAME);
    }

    private static function extension(): string
    {
        foreach (['.jpg', '.jpeg', '.png', '.webp', '.gif'] as $ext) {
            if (is_file(public_path(self::RELATIVE_DIR.'/'.self::FILENAME.$ext))) {
                return $ext;
            }
        }

        return '';
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
