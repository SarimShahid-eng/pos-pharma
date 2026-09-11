<?php

namespace Database\Factories;

use App\Models\Purchases,id;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'payment_method' => fake()->randomElement(["cash","bank"]),
            'amount' => fake()->randomFloat(2, 0, 9999999999.99),
            'purchase_id' => Purchases,id::factory(),
            'reference_no' => fake()->word(),
            'date' => fake()->date(),
            'notes' => fake()->text(),
        ];
    }
}
