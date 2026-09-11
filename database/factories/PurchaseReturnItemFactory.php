<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseReturnItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_return_id' => PurchaseReturn::factory(),
            'product_id' => Product::factory(),
            'qty' => fake()->randomFloat(2, 0, 9999999999.99),
            'rate' => fake()->randomFloat(2, 0, 9999999999.99),
            'amount' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
