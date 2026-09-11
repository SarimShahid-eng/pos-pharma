<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'qty' => fake()->randomFloat(2, 0, 9999999999.99),
            'rate' => fake()->randomFloat(2, 0, 9999999999.99),
            'discount' => fake()->randomFloat(2, 0, 9999999999.99),
            'after_discount_price' => fake()->randomFloat(2, 0, 9999999999.99),
            'amount' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
