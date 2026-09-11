<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'qty' => fake()->randomFloat(2, 0, 9999999999.99),
            'type' => fake()->randomElement(["purchase","sale","purchase_return","sale_return","adjustment"]),
            'reference_id' => fake()->numberBetween(-10000, 10000),
            'reference_type' => fake()->word(),
        ];
    }
}
