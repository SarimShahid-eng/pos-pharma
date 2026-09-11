<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
    ['name' => 'Panadol Extra Tablets (Strip of 10)', 'unit' => 'strip', 'cost' => 45.00, 'sale' => 60.00, 'discount' => 0.00],
    ['name' => 'Brufen 400mg Tablets (Strip of 10)', 'unit' => 'strip', 'cost' => 60.00, 'sale' => 80.00, 'discount' => 0.00],
    ['name' => 'Augmentin 625mg Tablets (Strip of 6)', 'unit' => 'strip', 'cost' => 320.00, 'sale' => 380.00, 'discount' => 10.00],
    ['name' => 'Disprin Tablets (Strip of 10)', 'unit' => 'strip', 'cost' => 15.00, 'sale' => 20.00, 'discount' => 0.00],
    ['name' => 'Risek 20mg Capsules (Strip of 14)', 'unit' => 'strip', 'cost' => 180.00, 'sale' => 220.00, 'discount' => 0.00],
    ['name' => 'Calpol Syrup 60ml', 'unit' => 'bottle', 'cost' => 120.00, 'sale' => 150.00, 'discount' => 0.00],
    ['name' => 'ORS Sachet (Electrolyte Powder)', 'unit' => 'pcs', 'cost' => 12.00, 'sale' => 18.00, 'discount' => 0.00],
    ['name' => 'Surbex-Z Capsules (Bottle of 30)', 'unit' => 'bottle', 'cost' => 240.00, 'sale' => 290.00, 'discount' => 10.00],
    ['name' => 'Flagyl 400mg Tablets (Strip of 10)', 'unit' => 'strip', 'cost' => 55.00, 'sale' => 70.00, 'discount' => 0.00],
    ['name' => 'Centrum Multivitamin Tablets (Bottle of 30)', 'unit' => 'bottle', 'cost' => 850.00, 'sale' => 980.00, 'discount' => 20.00],
    ['name' => 'Digital Thermometer', 'unit' => 'pcs', 'cost' => 280.00, 'sale' => 350.00, 'discount' => 0.00],
    ['name' => 'Surgical Face Mask (Box of 50)', 'unit' => 'box', 'cost' => 350.00, 'sale' => 420.00, 'discount' => 15.00],
    ['name' => 'Disposable Syringe 5ml', 'unit' => 'pcs', 'cost' => 8.00, 'sale' => 12.00, 'discount' => 0.00],
    ['name' => 'Betadine Antiseptic Solution 120ml', 'unit' => 'bottle', 'cost' => 210.00, 'sale' => 260.00, 'discount' => 0.00],
    ['name' => 'Elastic Crepe Bandage 4 inch', 'unit' => 'pcs', 'cost' => 90.00, 'sale' => 120.00, 'discount' => 0.00],
];

        // Unique initial barcode base for mock EAN-13 scanning
        $baseBarcode = 8901234567000;

        foreach ($products as $index => $item) {
            $costPrice = $item['cost'];
            $salePrice = $item['sale'];
            $discount = $item['discount'];

            // Calculate net profit per unit
            $effectiveSalePrice = max(0, $salePrice - $discount);
            $profit = max(0, $effectiveSalePrice - $costPrice);

            Product::create([
                'barcode' => (string) ($baseBarcode + $index),
                'label_title' => $item['name'],
                'name' => $item['name'],
                'unit' => $item['unit'],
                'cost_price' => $costPrice,
                'sale_price' => $salePrice,
                'discount' => $discount,
                'profit' => $profit,
                'stock_qty' => rand(25, 150),
            ]);
        }
    }
}
