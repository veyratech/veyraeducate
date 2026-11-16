<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class ImageProcessor
{
    /**
     * Resize and save an uploaded image to the given public path.
     *
     * @param  array{width?: int, height?: int, mode?: 'fit'|'cover'|'crop'}  $options
     *                                                                                  mode fit  = scale to fit inside box, keep ratio (letterbox)
     *                                                                                  mode cover = scale to cover box, crop center excess
     *                                                                                  mode crop = crop center to target ratio, no upscale
     */
    public static function save(UploadedFile $file, string $publicPath, array $options = []): array
    {
        $targetWidth = (int) ($options['width'] ?? 0);
        $targetHeight = (int) ($options['height'] ?? 0);
        $mode = $options['mode'] ?? 'fit';

        $src = imagecreatefromstring($file->getContent());
        if ($src === false) {
            throw new \RuntimeException('Tidak dapat membaca gambar yang diunggah.');
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($targetWidth <= 0 && $targetHeight <= 0) {
            $dst = $src;
            $finalW = $origW;
            $finalH = $origH;
        } elseif ($mode === 'cover' && $targetWidth > 0 && $targetHeight > 0) {
            $dst = self::cover($src, $origW, $origH, $targetWidth, $targetHeight);
            $finalW = $targetWidth;
            $finalH = $targetHeight;
        } elseif ($mode === 'crop' && $targetWidth > 0 && $targetHeight > 0) {
            $dst = self::cropToRatio($src, $origW, $origH, $targetWidth, $targetHeight);
            $finalW = $targetWidth;
            $finalH = $targetHeight;
        } else {
            $dst = self::fit($src, $origW, $origH, $targetWidth, $targetHeight);
            $finalW = $targetWidth > 0 ? $targetWidth : (int) round($origW * min($targetWidth / $origW, $targetHeight / $origH));
            $finalH = $targetHeight > 0 ? $targetHeight : (int) round($origH * min($targetWidth / $origW, $targetHeight / $origH));
        }

        imagedestroy($src);

        $dir = dirname($publicPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = self::extension($file);
        $quality = self::quality($file);
        $fullPath = $publicPath.'.'.$ext;

        match ($ext) {
            'png' => imagepng($dst, $fullPath),
            'webp' => imagewebp($dst, $fullPath, 90),
            'gif' => imagegif($dst, $fullPath),
            default => imagejpeg($dst, $fullPath, $quality),
        };

        imagedestroy($dst);

        return ['path' => $fullPath, 'width' => $finalW, 'height' => $finalH];
    }

    /**
     * Return original dimensions of uploaded image without modifying it.
     *
     * @return array{width: int, height: int}
     */
    public static function getDimensions(UploadedFile $file): array
    {
        $src = imagecreatefromstring($file->getContent());
        if ($src === false) {
            return ['width' => 0, 'height' => 0];
        }
        $w = imagesx($src);
        $h = imagesy($src);
        imagedestroy($src);

        return ['width' => $w, 'height' => $h];
    }

    private static function fit(\GdImage $src, int $origW, int $origH, int $targetW, int $targetH): \GdImage
    {
        if ($targetW <= 0 && $targetH <= 0) {
            return $src;
        }

        $tw = $targetW > 0 ? $targetW : $origW;
        $th = $targetH > 0 ? $targetH : $origH;

        $scaleW = $tw / $origW;
        $scaleH = $th / $origH;
        $scale = min($scaleW, $scaleH);

        $newW = (int) round($origW * $scale);
        $newH = (int) round($origH * $scale);

        $dst = imagecreatetruecolor($tw, $th);
        $bg = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $tw, $th, $bg);
        imagecopyresampled(
            $dst, $src,
            (int) (($tw - $newW) / 2), (int) (($th - $newH) / 2),
            0, 0, $newW, $newH, $origW, $origH
        );

        return $dst;
    }

    private static function cover(\GdImage $src, int $origW, int $origH, int $targetW, int $targetH): \GdImage
    {
        $scale = max($targetW / $origW, $targetH / $origH);
        $newW = (int) round($origW * $scale);
        $newH = (int) round($origH * $scale);

        $dst = imagecreatetruecolor($targetW, $targetH);
        $bg = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $bg);

        $srcX = (int) round(($newW - $targetW) / 2);
        $srcY = (int) round(($newH - $targetH) / 2);

        imagecopyresampled(
            $dst, $src,
            0, 0, $srcX, $srcY,
            $targetW, $targetH, $newW, $newH
        );

        return $dst;
    }

    private static function cropToRatio(\GdImage $src, int $origW, int $origH, int $targetW, int $targetH): \GdImage
    {
        $targetRatio = $targetW / $targetH;
        $origRatio = $origW / $origH;

        if ($origRatio > $targetRatio) {
            $cropW = (int) round($origH * $targetRatio);
            $cropH = $origH;
        } else {
            $cropW = $origW;
            $cropH = (int) round($origW / $targetRatio);
        }

        $sx = (int) round(($origW - $cropW) / 2);
        $sy = (int) round(($origH - $cropH) / 2);

        $cropped = imagecreatetruecolor($cropW, $cropH);
        imagecopy($cropped, $src, 0, 0, $sx, $sy, $cropW, $cropH);

        $dst = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($dst, $cropped, 0, 0, 0, 0, $targetW, $targetH, $cropW, $cropH);
        imagedestroy($cropped);

        return $dst;
    }

    private static function extension(UploadedFile $file): string
    {
        return match (strtolower($file->getClientOriginalExtension())) {
            'jpeg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            'gif' => 'gif',
            default => 'jpg',
        };
    }

    private static function quality(UploadedFile $file): int
    {
        $ext = strtolower($file->getClientOriginalExtension());
        return match ($ext) {
            'png' => -1,
            'webp' => 85,
            default => 90,
        };
    }
}
