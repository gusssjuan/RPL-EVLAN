<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['nama' => RoleName::Admin->value]);

        User::firstOrCreate(
            ['email' => 'admin@evlan.test'],
            [
                'role_id' => $adminRole->id,
                'nama' => 'Admin EVLAN',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin12345')),
                'no_telepon' => '081234567890',
                'status_akun' => StatusAkun::Aktif,
                'email_verified_at' => now(),
            ]
        );
    }
}

