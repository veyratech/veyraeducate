<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppRequest extends Model
{
    use Auditable, HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new SchoolScope);
    }

    protected $fillable = [
        'school_id',
        'user_id',
        'name',
        'description',
        'category',
        'priority',
        'target_date',
        'additional_notes',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'category' => 'string',
            'priority' => 'string',
            'target_date' => 'date',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
