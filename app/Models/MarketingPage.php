<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'eyebrow',
        'hero_image',
        'cta_text',
        'cta_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
