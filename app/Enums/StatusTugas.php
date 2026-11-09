<?php

namespace App\Enums;

enum StatusTugas: string
{
    case BelumDikerjakan = 'belum_dikerjakan';
    case SedangDikerjakan = 'sedang_dikerjakan';
    case SudahDiselesaikan = 'sudah_diselesaikan';

    public function label(): string
    {
        return match ($this) {
            self::BelumDikerjakan => 'Belum dikerjakan',
            self::SedangDikerjakan => 'Sedang dikerjakan',
            self::SudahDiselesaikan => 'Sudah diselesaikan',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BelumDikerjakan => '○',
            self::SedangDikerjakan => '◐',
            self::SudahDiselesaikan => '✓',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BelumDikerjakan => 'neutral',
            self::SedangDikerjakan => 'warning',
            self::SudahDiselesaikan => 'success',
        };
    }
}

