<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 5000, 500000);
        $shippingAmount = fake()->randomFloat(2, 0, 10000);
        $discountAmount = fake()->randomFloat(2, 0, $totalAmount * 0.2);

        return [
            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
            'address_id' => Address::factory(),
            'total_amount' => $totalAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'status' => fake()->randomElement(['pending', 'processing', 'paid', 'shipped', 'delivered', 'cancelled', 'refunded']),
            'payment_method' => fake()->randomElement(['mobile_money', 'card', 'wallet', 'installment']),
            'is_installment' => fake()->boolean(20),
        ];
    }
}


















