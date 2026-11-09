<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'nama_divisi' => fake()->unique()->randomElement(['Acara', 'Konsumsi', 'Publikasi', 'Perlengkapan', 'Dokumentasi', 'Keamanan', 'Humas']),
        ];
    }
}
