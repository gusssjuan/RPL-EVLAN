<?php

namespace Database\Factories;

use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorProductFactory extends Factory
{
    protected $model = VendorProduct::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'nama_produk' => fake()->words(3, true),
            'harga' => fake()->numberBetween(100000, 5000000),
            'deskripsi' => fake()->sentence(),
            'is_aktif' => true,
        ];
    }
}
