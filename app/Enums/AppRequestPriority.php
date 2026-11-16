<?php

namespace App\Enums;

enum AppRequestPriority: string
{
    case Normal = 'normal';
    case Tinggi = 'tinggi';
    case Mendesak = 'mendesak';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Tinggi => 'Tinggi',
            self::Mendesak => 'Mendesak',
        };
    }
}
