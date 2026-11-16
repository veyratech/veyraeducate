<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Scopes\SchoolScope;
use App\SubscriptionStatus;
use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class School extends Model
{
    /** @use HasFactory<SchoolFactory> */
    use Auditable, HasFactory;

    protected $withTrashed = true;

    protected $fillable = [
        'owner_user_id',
        'name',
        'npsn',
        'email',
        'phone',
        'city',
        'district',
        'postal_code',
        'logo_path',
        'meta',
        'last_app_access_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'last_app_access_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function paymentOrders(): HasMany
    {
        return $this->hasMany(PaymentOrder::class);
    }

    public function webApps(): BelongsToMany
    {
        return $this->belongsToMany(WebApp::class, 'school_web_apps')
            ->withTimestamps();
    }

    public function hasAccessToApp(string $slug): bool
    {
        $app = WebApp::where('slug', $slug)->first();
        if (! $app || ! $app->enabled) {
            return false;
        }

        // 1. Direct school grant (via school_web_apps or manual approval)
        if ($this->webApps()->where('web_apps.id', $app->id)->exists()) {
            return true;
        }

        // 2. Plan-based access for active subscribers
        if ($this->hasActiveSubscription()) {
            $plan = $this->activeSubscription?->plan;
            if ($plan) {
                if ($plan->hasWebApp($app)) {
                    return true;
                }

                // If the app is not restricted to any specific plans and the plan doesn't have custom restrictions
                if (! $app->plans()->exists() && ! $plan->webApps()->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function grantAppAccess(string $slug): void
    {
        $app = WebApp::where('slug', $slug)->first();
        if ($app && ! $this->webApps()->where('web_app_id', $app->id)->exists()) {
            $this->webApps()->attach($app->id);
        }
    }

    public function grantPlanAppAccess(Plan $plan): void
    {
        if ($plan->webApps()->exists()) {
            $planAppIds = $plan->webApps()->where('enabled', true)->pluck('web_apps.id')->all();
            $this->webApps()->syncWithoutDetaching($planAppIds);
        } else {
            $this->grantAllAppAccess();
        }
    }

    public function grantAllAppAccess(): void
    {
        WebApp::enabled()->get()->each(function (WebApp $app) {
            $this->grantAppAccess($app->slug);
        });
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', SubscriptionStatus::Active->value)
            ->where('ends_at', '>', now())
            ->latestOfMany('ends_at');
    }

    public function latestSubscription(): ?Subscription
    {
        return $this->subscriptions()->latest('id')->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->withoutGlobalScope(SchoolScope::class)
            ->where('status', SubscriptionStatus::Active->value)
            ->where('ends_at', '>', now())
            ->exists();
    }

    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('id', $schoolId);
    }

    public static function current(): ?self
    {
        return auth()->check() ? auth()->user()->school : null;
    }

    /**
     * Get or generate unique school referral code.
     */
    public function getReferralCodeAttribute(): string
    {
        return $this->meta['referral_code'] ?? ('SCH-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT));
    }

    public function getReferralCode(): string
    {
        return $this->referral_code;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->meta['address'] ?? null;
    }

    public function getHeadmasterAttribute(): ?string
    {
        return $this->meta['headmaster'] ?? ($this->meta['headmaster_name'] ?? null);
    }

    /**
     * Get full sharable referral link.
     */
    public function getReferralLinkAttribute(): string
    {
        return url('/register?ref='.$this->referral_code);
    }

    /**
     * Get schools referred by this school.
     *
     * @return Collection<int, School>
     */
    public function getReferredSchools()
    {
        return self::where('meta->referred_by_school_id', $this->id)->get();
    }

    /**
     * Calculate total gross referral rewards earned (10% commission on paid orders from referred schools).
     */
    public function getReferralEarnings(): int
    {
        $referredIds = $this->getReferredSchools()->pluck('id');
        if ($referredIds->isEmpty()) {
            return 0;
        }

        $totalPaid = PaymentOrder::withoutGlobalScope(SchoolScope::class)
            ->whereIn('school_id', $referredIds)
            ->where('status', 'paid')
            ->sum('amount_idr');

        return (int) round($totalPaid * 0.10);
    }

    /**
     * Calculate total referral commission already claimed or currently being processed.
     */
    public function getClaimedReferralAmount(): int
    {
        $claims = is_array($this->meta['referral_claims'] ?? null) ? $this->meta['referral_claims'] : [];
        $totalClaimed = 0;

        foreach ($claims as $claim) {
            $status = strtolower((string) ($claim['status'] ?? ''));
            if (in_array($status, ['ditolak', 'batal'], true)) {
                continue;
            }
            $totalClaimed += (int) ($claim['amount'] ?? 0);
        }

        return max(0, $totalClaimed);
    }

    /**
     * Calculate available (unclaimed) referral commission balance.
     */
    public function getAvailableReferralBalance(): int
    {
        return max(0, $this->getReferralEarnings() - $this->getClaimedReferralAmount());
    }
}
