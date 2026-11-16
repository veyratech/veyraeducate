<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Scopes\SchoolScope;
use Database\Factories\PaymentOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOrder extends Model
{
    /** @use HasFactory<PaymentOrderFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new SchoolScope);
    }

    protected $fillable = [
        'order_id',
        'subscription_id',
        'school_id',
        'user_id',
        'plan_id',
        'amount_idr',
        'status',
        'payment_method',
        'snap_token',
        'transaction_id',
        'snap_response',
        'webhook_payload',
        'proof_path',
        'bank_sender',
        'notes',
        'paid_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount_idr' => 'integer',
            'snap_response' => 'array',
            'webhook_payload' => 'array',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_id';
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function isManualTransfer(): bool
    {
        return $this->payment_method === 'manual_transfer';
    }

    public function hasProof(): bool
    {
        return ! empty($this->proof_path);
    }

    public function proofUrl(): ?string
    {
        return $this->proof_path ? asset('storage/'.$this->proof_path) : null;
    }

    public function formatAmount(): string
    {
        return 'Rp '.number_format($this->amount_idr, 0, ',', '.');
    }
}
