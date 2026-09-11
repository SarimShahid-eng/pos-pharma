<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'barcode' => fake()->word(),
            'label_title' => fake()->word(),
            'name' => fake()->name(),
            'unit' => fake()->randomElement(['pcs','pack','kg','ltr']),
            'cost_price' => fake()->randomFloat(2, 0, 9999999999.99),
            'sale_price' => fake()->randomFloat(2, 0, 9999999999.99),
            'discount' => fake()->randomFloat(2, 0, 9999999999.99),
            'profit' => fake()->randomFloat(2, 0, 9999999999.99),
            'stock_qty' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
