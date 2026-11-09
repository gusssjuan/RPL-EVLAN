<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'aksi' => 'update_status',
            'deskripsi' => fake()->sentence(),
            'meta' => null,
        ];
    }
}

