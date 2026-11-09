<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => fake()->unique()->words(2, true),
            'deskripsi' => fake()->sentence(),
            'ikon' => fake()->randomElement(['🎤', '🎂', '💍', '🤝', '🇮🇩']),
        ];
    }
}

