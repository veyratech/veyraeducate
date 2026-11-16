<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    use SoftDeletes;

    protected static function bootAuditable(): void
    {
        static::deleting(function (Model $model) {
            AuditLog::logDelete($model, Auth::id());
        });

        static::created(function (Model $model) {
            AuditLog::create([
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'action' => 'created',
                'new_values' => json_encode($model->getAttributes()),
                'user_id' => Auth::id(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        });

        static::updated(function (Model $model) {
            $changes = array_diff_assoc($model->getChanges(), array_flip(['updated_at']));
            if (empty($changes)) {
                return;
            }

            AuditLog::create([
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'action' => 'updated',
                'old_values' => json_encode(array_intersect_key($model->getOriginal(), $changes)),
                'new_values' => json_encode($changes),
                'user_id' => Auth::id(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        });
    }

    public function scopeHasAuditLogs($query)
    {
        return $query;
    }
}
