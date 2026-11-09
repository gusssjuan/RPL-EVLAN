<?php

namespace Database\Factories;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => Role::firstOrCreate(['nama' => RoleName::User->value])->id,
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'no_telepon' => '08'.fake()->numerify('##########'),
            'status_akun' => StatusAkun::Aktif,
            'preferensi_notifikasi' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['nama' => RoleName::Admin->value])->id,
        ]);
    }

    public function vendor(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['nama' => RoleName::Vendor->value])->id,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => [
            'status_akun' => StatusAkun::Nonaktif,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}
