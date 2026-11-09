<?php

namespace App\Enums;

enum StatusVerifikasi: string
{
    case Menunggu = 'menunggu';
    case Terverifikasi = 'terverifikasi';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Terverifikasi => 'Terverifikasi',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Terverifikasi => 'success',
            self::Ditolak => 'danger',
        };
    }
}

