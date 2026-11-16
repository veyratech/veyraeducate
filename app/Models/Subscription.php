<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use App\SubscriptionStatus;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new SchoolScope);
    }

    protected $fillable = [
        'school_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'activated_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'activated_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function paymentOrders(): HasMany
    {
        return $this->hasMany(PaymentOrder::class);
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active
            && $this->ends_at !== null
            && $this->ends_at->isFuture();
    }

    public function activate(?int $durationMonths = null): void
    {
        $months = $durationMonths ?? $this->plan?->duration_months ?? 6;
        $start = now();
        $this->forceFill([
            'status' => SubscriptionStatus::Active,
            'starts_at' => $start,
            'ends_at' => $start->copy()->addMonthsNoOverflow($months),
            'activated_at' => $start,
        ])->save();
    }
}
