<?php

namespace Database\Factories;

use App\Models\Checklist;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChecklistFactory extends Factory
{
    protected $model = Checklist::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'nama_kebutuhan' => fake()->sentence(3),
            'kelompok' => fake()->randomElement(['Perlengkapan', 'Konsumsi', 'Acara', 'Publikasi', 'Dokumentasi']),
            'dari_template' => true,
            'status_selesai' => false,
            'urutan' => fake()->numberBetween(1, 15),
        ];
    }
}
