<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percent', 'fixed']);
        $value = $type === 'percent' 
            ? fake()->numberBetween(5, 50) 
            : fake()->randomFloat(2, 1000, 10000);

        return [
            'code' => strtoupper(fake()->unique()->bothify('???####')),
            'type' => $type,
            'value' => $value,
            'min_order_amount' => fake()->randomFloat(2, 5000, 50000),
            'usage_limit' => fake()->numberBetween(10, 1000),
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
        ];
    }
}
















