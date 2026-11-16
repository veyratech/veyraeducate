<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $fillable = [
        'connection',
        'queue',
        'payload',
        'exception',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'failed_at' => 'datetime',
        ];
    }
}
