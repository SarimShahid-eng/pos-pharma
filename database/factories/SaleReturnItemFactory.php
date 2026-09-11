<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleReturnItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sale_return_id' => SaleReturn::factory(),
            'product_id' => Product::factory(),
            'qty' => fake()->randomFloat(2, 0, 9999999999.99),
            'rate' => fake()->randomFloat(2, 0, 9999999999.99),
            'amount' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
