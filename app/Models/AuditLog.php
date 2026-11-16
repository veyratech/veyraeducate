<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'user_id' => 'integer',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, mixed $auditable = null, ?array $newValues = null, ?array $oldValues = null, ?int $userId = null): self
    {
        $type = is_object($auditable) ? get_class($auditable) : (is_string($auditable) ? $auditable : 'System');
        $id = is_object($auditable) && method_exists($auditable, 'getKey') ? (int) $auditable->getKey() : 0;

        return self::create([
            'auditable_type' => $type,
            'auditable_id' => $id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public static function logDelete(Model $model, ?int $userId = null): void
    {
        self::record('deleted', $model, null, $model->getAttributes(), $userId);
    }

    public function shortAuditableType(): string
    {
        return class_basename($this->auditable_type);
    }
}
