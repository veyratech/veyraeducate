<?php

namespace App\Enums;

enum AppRequestCategory: string
{
    case Akademik = 'akademik';
    case Keuangan = 'keuangan';
    case Perpustakaan = 'perpustakaan';
    case Kesiswaan = 'kesiswaan';
    case Kepegawaian = 'kepegawaian';
    case Ekskul = 'ekskul';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Akademik => 'Akademik',
            self::Keuangan => 'Keuangan',
            self::Perpustakaan => 'Perpustakaan',
            self::Kesiswaan => 'Kesiswaan',
            self::Kepegawaian => 'Kepegawaian',
            self::Ekskul => 'Ekskul',
            self::Lainnya => 'Lainnya',
        };
    }
}
