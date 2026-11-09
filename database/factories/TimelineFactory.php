<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Timeline;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimelineFactory extends Factory
{
    protected $model = Timeline::class;

    public function definition(): array
    {
        $offset = fake()->numberBetween(0, 60);

        return [
            'event_id' => Event::factory(),
            'label' => $offset === 0 ? 'Hari H' : 'H-'.$offset,
            'offset_hari' => $offset,
            'tanggal' => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'kegiatan' => fake()->sentence(4),
            'urutan' => fake()->numberBetween(1, 10),
        ];
    }
}
