<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Smartphone X Pro',
            'description' => 'Latest smartphone with advanced features and high-resolution camera',
            'category' => 'Electronics',
            'sku' => 'PHONE-001',
            'price' => 999.99,
            'image_url' => null,
            'stock_quantity' => 50,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 1,
            'warehouse_location' => 'A-101'
        ]);

        Product::create([
            'name' => 'Wireless Headphones',
            'description' => 'Noise-cancelling wireless headphones with long battery life',
            'category' => 'Audio',
            'sku' => 'AUDIO-002',
            'price' => 199.99,
            'image_url' => null,
            'stock_quantity' => 100,
            'can_be_returned' => true,
            'return_period_days' => 14,
            'supplier_id' => 2,
            'warehouse_location' => 'B-203'
        ]);

        Product::create([
            'name' => 'Laptop Pro 15"',
            'description' => 'High-performance laptop for professionals and gamers',
            'category' => 'Computers',
            'sku' => 'COMP-003',
            'price' => 1499.99,
            'image_url' => null,
            'stock_quantity' => 25,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 1,
            'warehouse_location' => 'A-105'
        ]);
    }
}
