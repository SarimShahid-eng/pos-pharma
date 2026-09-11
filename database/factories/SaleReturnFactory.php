<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleReturnFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'total_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'refunded_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'date' => fake()->date(),
            'notes' => fake()->text(),
        ];
    }
}
