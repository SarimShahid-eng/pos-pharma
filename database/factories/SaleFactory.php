<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'invoice_number' => fake()->word(),
            'total_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'discount_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'net_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'received_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'change_given' => fake()->randomFloat(2, 0, 9999999999.99),
            'payment_method' => fake()->randomElement(["cash","card","bank"]),
            'date' => fake()->date(),
        ];
    }
}
