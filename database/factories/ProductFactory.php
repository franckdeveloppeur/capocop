<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $basePrice = fake()->randomFloat(2, 1000, 100000);
        $hasPromo = fake()->boolean(30);

        return [
            'shop_id' => Shop::factory(),
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraph(),
            'base_price' => $basePrice,
            'price_promo' => $hasPromo ? fake()->randomFloat(2, $basePrice * 0.7, $basePrice * 0.9) : null,
            'status' => fake()->randomElement(['draft', 'active', 'archived']),
            'stock_manage' => fake()->boolean(80),
        ];
    }
}





