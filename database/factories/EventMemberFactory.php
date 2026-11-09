<?php

namespace Database\Factories;

use App\Enums\PeranAnggota;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventMemberFactory extends Factory
{
    protected $model = EventMember::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'division_id' => Division::factory(),
            'peran' => PeranAnggota::Anggota,
        ];
    }

    public function koordinator(): static
    {
        return $this->state(fn () => [
            'peran' => PeranAnggota::Koordinator,
        ]);
    }
}
