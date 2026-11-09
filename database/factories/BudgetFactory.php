<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        $jumlah = fake()->numberBetween(1, 10);
        $hargaSatuan = fake()->numberBetween(50000, 1000000);

        return [
            'event_id' => Event::factory(),
            'checklist_id' => null,
            'vendor_product_id' => null,
            'nama_item' => fake()->words(2, true),
            'jumlah' => $jumlah,
            'harga_satuan' => $hargaSatuan,
            'total' => $jumlah * $hargaSatuan,
        ];
    }
}

