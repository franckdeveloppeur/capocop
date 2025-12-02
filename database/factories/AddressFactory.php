<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Other']),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'country' => 'Burkina Faso',
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'line1' => fake()->streetAddress(),
            'line2' => fake()->optional()->secondaryAddress(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
}

