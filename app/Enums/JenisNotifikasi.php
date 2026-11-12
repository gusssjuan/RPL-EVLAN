<?php

namespace App\Enums;

enum JenisNotifikasi: string
{
    case Penugasan = 'penugasan';
    case Deadline = 'deadline';
    case StatusTugas = 'status_tugas';
    case Undangan = 'undangan';
    case Pendaftaran = 'pendaftaran';
    case VerifikasiVendor = 'verifikasi_vendor';

    public function label(): string
    {
        return match ($this) {
            self::Penugasan => 'Penugasan',
            self::Deadline => 'Batas Waktu',
            self::StatusTugas => 'Status Tugas',
            self::Undangan => 'Undangan',
            self::Pendaftaran => 'Pendaftaran',
            self::VerifikasiVendor => 'Verifikasi Vendor',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Penugasan => 'clipboard-list',
            self::Deadline => 'clock',
            self::StatusTugas => 'circle-dashed',
            self::Undangan => 'mail',
            self::Pendaftaran => 'calendar',
            self::VerifikasiVendor => 'badge-check',
        };
    }
}
