<?php

namespace Database\Factories;

use App\Enums\JenisItemTemplate;
use App\Models\Template;
use App\Models\TemplateItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemplateItemFactory extends Factory
{
    protected $model = TemplateItem::class;

    public function definition(): array
    {
        return [
            'template_id' => Template::factory(),
            'jenis' => fake()->randomElement(JenisItemTemplate::cases()),
            'nama_item' => fake()->sentence(3),
            'kelompok' => fake()->word(),
            'deskripsi' => fake()->sentence(),
            'offset_hari' => fake()->numberBetween(1, 60),
            'urutan' => fake()->numberBetween(1, 20),
        ];
    }
}

