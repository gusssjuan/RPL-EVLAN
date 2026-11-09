<?php

namespace App\Enums;

enum StatusPendaftaran: string
{
    case Menunggu = 'menunggu';
    case Diterima = 'diterima';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Diterima => 'Diterima',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Diterima => 'success',
            self::Ditolak => 'danger',
        };
    }
}

