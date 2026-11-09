<?php

namespace Database\Factories;

use App\Enums\StatusPendaftaran;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'no_telepon' => '08' . fake()->numerify('##########'),
            'catatan' => fake()->sentence(),
            'persetujuan_data' => true,
            'status' => StatusPendaftaran::Menunggu,
            'alasan_penolakan' => null,
            'decided_at' => null,
        ];
    }
}

