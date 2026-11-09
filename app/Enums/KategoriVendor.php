<?php

namespace App\Enums;

enum KategoriVendor: string
{
    case Catering = 'catering';
    case Dekorasi = 'dekorasi';
    case Dokumentasi = 'dokumentasi';
    case SoundSystem = 'sound_system';
    case PerlengkapanAcara = 'perlengkapan_acara';

    public function label(): string
    {
        return match ($this) {
            self::Catering => 'Catering',
            self::Dekorasi => 'Dekorasi',
            self::Dokumentasi => 'Dokumentasi',
            self::SoundSystem => 'Sound System',
            self::PerlengkapanAcara => 'Perlengkapan Acara',
        };
    }
}
