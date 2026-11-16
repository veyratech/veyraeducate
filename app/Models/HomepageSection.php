<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'section_type',
        'order_position',
        'is_active',
        'content',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_position' => 'integer',
        'content' => 'array',
    ];

    /**
     * Scope query to only active sections.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query ordered by order_position.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_position')->orderBy('id');
    }

    /**
     * Human-friendly label for section type.
     */
    public function getSectionTypeLabelAttribute(): string
    {
        return match ($this->section_type) {
            'hero' => 'Hero Banner',
            'trust_strip' => 'Pilar Kepercayaan',
            'features' => 'Fitur Utama',
            'workflow' => 'Cara Kerja (Langkah)',
            'ecosystem' => 'Ekosistem Layanan',
            'pricing' => 'Daftar Paket Harga',
            'faq' => 'Tanya Jawab (FAQ)',
            'cta' => 'Call To Action (Penutup)',
            'custom_banner' => 'Banner Promo / Info',
            'custom_content' => 'Container Bebas (Teks & Gambar)',
            'testimonials' => 'Testimoni & Ulasan Sekolah',
            'app_showcase' => 'Showcase Aplikasi Sekolah',
            default => ucfirst(str_replace('_', ' ', (string) $this->section_type)),
        };
    }

    /**
     * Retrieve content key safely with fallback.
     */
    public function field(string $key, mixed $default = null): mixed
    {
        return data_get($this->content, $key, $default);
    }

    /**
     * Get the configured background type.
     */
    public function getBgType(): string
    {
        return (string) $this->field('bg_type', 'default');
    }

    /**
     * Check if section has custom background configured.
     */
    public function hasCustomBackground(): bool
    {
        $type = $this->getBgType();

        return $type !== 'default' && $type !== '';
    }

    /**
     * Check if the effective background is visually dark.
     */
    public function isDarkBackground(): bool
    {
        $textMode = (string) $this->field('text_mode', 'auto');
        if ($textMode === 'light') {
            return true;
        }
        if ($textMode === 'dark') {
            return false;
        }

        $type = $this->getBgType();

        if (in_array($type, ['dark', 'brand', 'gold', 'emerald'], true)) {
            return true;
        }

        if (in_array($type, ['white', 'slate'], true)) {
            return false;
        }

        if ($type === 'custom_color') {
            $hex = ltrim((string) $this->field('bg_color', '#0f172a'), '#');
            if (strlen($hex) === 6) {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $luminance = ($r * 0.299 + $g * 0.587 + $b * 0.114);

                return $luminance < 140;
            }

            return true;
        }

        if ($type === 'custom_image') {
            $overlay = (string) $this->field('bg_overlay', 'dark-60');

            return ! str_starts_with($overlay, 'light');
        }

        // Default backgrounds
        return in_array($this->section_type, ['hero', 'cta'], true);
    }

    /**
     * Get CSS classes for the section's background.
     */
    public function getBackgroundClasses(): string
    {
        $type = $this->getBgType();

        return match ($type) {
            'dark' => 'bg-gradient-to-br from-slate-950 via-brand-950 to-slate-900',
            'white' => 'bg-white dark:bg-slate-900',
            'slate' => 'bg-slate-50 dark:bg-slate-900 border-y border-slate-200/80 dark:border-slate-800',
            'brand' => 'bg-gradient-to-br from-brand-950 via-brand-900 to-sky-950',
            'gold' => 'bg-gradient-to-br from-amber-950 via-yellow-950 to-amber-900',
            'emerald' => 'bg-gradient-to-br from-emerald-950 via-teal-950 to-emerald-900',
            'custom_image' => 'relative bg-cover bg-center bg-no-repeat',
            default => '',
        };
    }

    /**
     * Resolve full web URL for any stored image path (handling absolute, storage, and asset paths).
     */
    public static function resolveImageUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return asset(ltrim($url, '/'));
    }

    /**
     * Resolve image URL for a given field key.
     */
    public function imageUrl(string $key = 'image_url'): ?string
    {
        return self::resolveImageUrl((string) $this->field($key));
    }

    /**
     * Resolve full web URL for custom background image.
     */
    public function bgImageUrl(): ?string
    {
        return $this->imageUrl('bg_image_url');
    }

    /**
     * Get inline style attributes for custom color or custom image.
     */
    public function getBackgroundInlineStyle(): string
    {
        $type = $this->getBgType();

        if ($type === 'custom_color') {
            $color = (string) $this->field('bg_color', '#0f172a');

            return 'background-color: '.e($color).';';
        }

        if ($type === 'custom_image') {
            $url = $this->bgImageUrl();
            if ($url) {
                return "background-image: url('".e($url)."'); background-size: cover; background-position: center; background-repeat: no-repeat;";
            }
        }

        return '';
    }

    /**
     * Get CSS classes for background image overlay.
     */
    public function getOverlayClasses(): string
    {
        $overlay = (string) $this->field('bg_overlay', 'dark-60');

        return match ($overlay) {
            'dark-80' => 'bg-slate-950/80 backdrop-blur-[2px]',
            'dark-60' => 'bg-slate-950/60 backdrop-blur-[1px]',
            'dark-40' => 'bg-slate-950/40',
            'light-80' => 'bg-white/80 backdrop-blur-[2px]',
            'light-60' => 'bg-white/60 backdrop-blur-[1px]',
            'light-40' => 'bg-white/40',
            'none' => 'hidden',
            default => 'bg-slate-950/60 backdrop-blur-[1px]',
        };
    }

    /**
     * Human-friendly label for current background mode.
     */
    public function getBgLabel(): string
    {
        return match ($this->getBgType()) {
            'dark' => '🌙 Gradien Gelap',
            'white' => '⚪ Putih Bersih',
            'slate' => '🌫️ Abu-abu Slate',
            'brand' => '🔷 Royal Blue',
            'gold' => '✨ Emas Mewah',
            'emerald' => '🌿 Emerald',
            'custom_color' => '🎨 Warna Kustom',
            'custom_image' => '🖼️ Gambar Latar',
            default => '⚡ Bawaan',
        };
    }

    /**
     * Get active sections ordered, cached for 1 minute.
     * Cache is forgotten (not stored as empty) when database is unreachable,
     * ensuring the homepage refreshes automatically once MySQL recovers.
     */
    public static function getActiveOrdered(): Collection
    {
        try {
            if (! Schema::hasTable('homepage_sections')) {
                return new Collection;
            }

            return Cache::remember('homepage.sections.active', 60, function (): Collection {
                return self::active()->ordered()->get();
            });
        } catch (\Throwable) {
            Cache::forget('homepage.sections.active');

            return new Collection;
        }
    }

    /**
     * Boot method for cache invalidation.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('homepage.sections.active'));
        static::deleted(fn () => Cache::forget('homepage.sections.active'));
    }
}
