<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'invoice_number' => fake()->word(),
            'reference_number' => fake()->word(),
            'supplier_id' => Supplier::factory(),
            'total_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'discount_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'paid_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'remaining_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'date' => fake()->date(),
            'notes' => fake()->text(),
        ];
    }
}
