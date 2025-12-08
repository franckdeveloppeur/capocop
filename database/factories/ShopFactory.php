<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'name' => fake()->company() . ' Shop',
            'address_id' => null,
            'currency' => 'XOF',
            'settings' => [],
        ];
    }
}






