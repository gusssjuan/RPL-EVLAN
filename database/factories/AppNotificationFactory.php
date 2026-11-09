<?php

namespace Database\Factories;

use App\Enums\JenisNotifikasi;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppNotificationFactory extends Factory
{
    protected $model = AppNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => null,
            'task_id' => null,
            'jenis' => fake()->randomElement(JenisNotifikasi::cases()),
            'pesan' => fake()->sentence(),
            'tautan' => null,
            'dedupe_key' => fake()->unique()->uuid(),
            'dibaca_at' => null,
            'waktu_kirim' => now(),
        ];
    }
}
