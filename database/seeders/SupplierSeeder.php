<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
    [
        'name' => 'GSK Pakistan Distribution',
        'phone_number' => '+923001112233',
        'opening_balance' => 0,
        'current_balance' => 0,
    ],
    [
        'name' => 'Getz Pharma Trading Co.',
        'phone_number' => '+923012223344',
        'opening_balance' => 45000,
        'current_balance' => 45000,
    ],
    [
        'name' => 'Abbott Healthcare Suppliers',
        'phone_number' => '+923023334455',
        'opening_balance' => 120000,
        'current_balance' => 120000,
    ],
    [
        'name' => 'Sami Pharmaceuticals Wholesale',
        'phone_number' => '+923034445566',
        'opening_balance' => 15000,
        'current_balance' => 15000,
    ],
    [
        'name' => 'Al-Shifa Surgical & Medical Supplies',
        'phone_number' => '+923045556677',
        'opening_balance' => 0,
        'current_balance' => 0,
    ],
    [
        'name' => 'Searle Pakistan Distributors',
        'phone_number' => '+923056667788',
        'opening_balance' => 250000,
        'current_balance' => 250000,
    ],
    [
        'name' => 'Medisave Pharma Traders',
        'phone_number' => '+923067778899',
        'opening_balance' => 85000,
        'current_balance' => 85000,
    ],
    [
        'name' => 'Highnoon Laboratories Agency',
        'phone_number' => '+923078889900',
        'opening_balance' => 30000,
        'current_balance' => 30000,
    ],
    [
        'name' => 'City Surgical & Diagnostic Equipment',
        'phone_number' => '+923089990011',
        'opening_balance' => 62000,
        'current_balance' => 62000,
    ],
    [
        'name' => 'Wellcare Vitamins & Nutraceuticals',
        'phone_number' => '+923045556677',
        'opening_balance' => 0,
        'current_balance' => 0,
    ],
];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }
    }
// }
}
