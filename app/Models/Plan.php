<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'description',
        'price_idr',
        'duration_months',
        'duration_label',
        'badge',
        'features',
        'is_featured',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price_idr' => 'integer',
            'duration_months' => 'integer',
            'is_featured' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function webApps(): BelongsToMany
    {
        return $this->belongsToMany(WebApp::class, 'plan_web_apps')
            ->withTimestamps();
    }

    public function hasWebApp(WebApp|int|string $app): bool
    {
        if ($app instanceof WebApp) {
            return $this->webApps()->where('web_apps.id', $app->id)->exists();
        }

        if (is_numeric($app)) {
            return $this->webApps()->where('web_apps.id', (int) $app)->exists();
        }

        return $this->webApps()->where('web_apps.slug', $app)->exists();
    }

    public function formatPrice(): string
    {
        return 'Rp '.number_format($this->price_idr, 0, ',', '.');
    }

    public static function findByCode(string $code): ?self
    {
        return static::query()->where('code', $code)->where('is_active', true)->first();
    }

    public static function getDefault(): ?self
    {
        return static::query()->where('is_default', true)->where('is_active', true)->first();
    }
}
