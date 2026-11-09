<?php

namespace App\Enums;

enum RoleName: string
{
    case Admin = 'admin';
    case User = 'user';
    case Vendor = 'vendor';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::User => 'Pengguna',
            self::Vendor => 'Vendor',
        };
    }
}
