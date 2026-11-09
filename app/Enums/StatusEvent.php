<?php

namespace App\Enums;

enum StatusEvent: string
{
    case Perencanaan = 'perencanaan';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Perencanaan => 'Perencanaan',
            self::Selesai => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Perencanaan => 'blue',
            self::Selesai => 'green',
        };
    }
}

