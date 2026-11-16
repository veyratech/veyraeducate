<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'web_app_id',
        'slug',
        'version',
        'status',
        'source_file',
        'file_count',
        'size_bytes',
        'notes',
        'created_by',
        'deployed_at',
    ];

    protected function casts(): array
    {
        return [
            'file_count' => 'integer',
            'size_bytes' => 'integer',
            'deployed_at' => 'datetime',
        ];
    }

    public function webApp(): BelongsTo
    {
        return $this->belongsTo(WebApp::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'deploying', 'success']);
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu',
            'deploying' => 'Dideploy',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
        ];
    }
}
