<?php

namespace Database\Factories;

use App\Models\SavedVendor;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedVendorFactory extends Factory
{
    protected $model = SavedVendor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
        ];
    }
}

