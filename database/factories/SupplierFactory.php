<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'opening_balance' => fake()->randomFloat(2, 0, 9999999999.99),
            'current_balance' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
