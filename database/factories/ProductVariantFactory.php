<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => 'SKU-' . fake()->unique()->numerify('##########'),
            'attributes' => [
                'size' => fake()->randomElement(['S', 'M', 'L', 'XL']),
                'color' => fake()->colorName(),
            ],
            'price' => fake()->randomFloat(2, 500, 50000),
            'stock' => fake()->numberBetween(0, 1000),
            'weight' => fake()->randomFloat(2, 0.1, 10),
        ];
    }
}





