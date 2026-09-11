<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseReturnFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'supplier_id' => Supplier::factory(),
            'total_amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'date' => fake()->date(),
            'notes' => fake()->text(),
        ];
    }
}
