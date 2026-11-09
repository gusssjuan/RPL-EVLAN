<?php

namespace App\Enums;

enum JenisItemTemplate: string
{
    case Kebutuhan = 'kebutuhan';
    case Timeline = 'timeline';
    case Divisi = 'divisi';
    case Jobdesk = 'jobdesk';

    public function label(): string
    {
        return match ($this) {
            self::Kebutuhan => 'Kebutuhan',
            self::Timeline => 'Timeline',
            self::Divisi => 'Struktur Kepanitiaan',
            self::Jobdesk => 'Rekomendasi Jobdesk',
        };
    }
}
