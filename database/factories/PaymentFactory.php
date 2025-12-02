<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->randomFloat(2, 1000, 100000),
            'method' => fake()->randomElement(['mobile_money', 'card', 'wallet']),
            'status' => fake()->randomElement(['pending', 'success', 'failed', 'refunded']),
            'transaction_ref' => 'TXN-' . fake()->unique()->numerify('##########'),
            'meta' => [],
        ];
    }
}

