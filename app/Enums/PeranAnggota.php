<?php

namespace App\Enums;

enum PeranAnggota: string
{
    case Koordinator = 'koordinator';
    case Anggota = 'anggota';

    public function label(): string
    {
        return match ($this) {
            self::Koordinator => 'Koordinator',
            self::Anggota => 'Anggota',
        };
    }
}

