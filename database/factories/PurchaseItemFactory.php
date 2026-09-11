<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory(),
            'qty' => fake()->randomFloat(2, 0, 9999999999.99),
            'bonus_qty' => fake()->randomFloat(2, 0, 9999999999.99),
            'rate' => fake()->randomFloat(2, 0, 9999999999.99),
            'amount' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
