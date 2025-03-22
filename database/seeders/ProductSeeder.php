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
            "name" => "Air Max 270",
            'description' => 'Lightweight and stylish',
            'category' => 'shoes',
            'sku' => 'shoe-001',
            'price' => 120.00,
            'image_url' => '/images/airmax270.jpg',
            'stock_quantity' => 25,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 1,
            'warehouse_location' => 'A-105'
        ]);

        Product::create([
            "name" => "Yeezy Boost 350",
            'description' => 'Comfortable with sleek design',
            'category' => 'shoes',
            'sku' => 'shoe-002',
            'price' => 220.00,
            'image_url' => '/images/yeezy350.jpg',
            'stock_quantity' => 15,
            'can_be_returned' => true,
            'return_period_days' => 0,
            'supplier_id' => 1,
            'warehouse_location' => 'A-106'
        ]);

        Product::create([
            "name" => "Jordan 1 Retro",
            'description' => 'Classic high-top design',
            'category' => 'shoes',
            'sku' => 'shoe-003',
            'price' => 170.00,
            'image_url' => '/images/jordan1retro.jpg',
            'stock_quantity' => 30,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 2,
            'warehouse_location' => 'A-107'
        ]);

        Product::create([
            "name" => "Converse All-Star",
            'description' => 'Casual everyday shoe',
            'category' => 'shoes',
            'sku' => 'shoe-004',
            'price' => 60.00,
            'image_url' => '/images/converseallstar.jpg',
            'stock_quantity' => 50,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 3,
            'warehouse_location' => 'B-101'
        ]);

        Product::create([
            "name" => "Adidas Ultraboost",
            'description' => 'High-performance running shoe',
            'category' => 'shoes',
            'sku' => 'shoe-005',
            'price' => 180.00,
            'image_url' => '/images/ultraboost.jpg',
            'stock_quantity' => 20,
            'can_be_returned' => true,
            'return_period_days' => 0,
            'supplier_id' => 2,
            'warehouse_location' => 'B-102'
        ]);

        Product::create([
            "name" => "Puma RS-X",
            'description' => 'Futuristic design with bold colors',
            'category' => 'shoes',
            'sku' => 'shoe-006',
            'price' => 140.00,
            'image_url' => '/images/pumarsx.jpg',
            'stock_quantity' => 35,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 3,
            'warehouse_location' => 'B-103'
        ]);

        Product::create([
            "name" => "New Balance 574",
            'description' => 'Classic and comfortable',
            'category' => 'shoes',
            'sku' => 'shoe-007',
            'price' => 100.00,
            'image_url' => '/images/nb574.jpg',
            'stock_quantity' => 40,
            'can_be_returned' => true,
            'return_period_days' => 0,
            'supplier_id' => 1,
            'warehouse_location' => 'C-101'
        ]);

        Product::create([
            "name" => "Vans Old Skool",
            'description' => 'Timeless skateboarding shoe',
            'category' => 'shoes',
            'sku' => 'shoe-008',
            'price' => 70.00,
            'image_url' => '/images/vansoldskool.jpg',
            'stock_quantity' => 45,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 2,
            'warehouse_location' => 'C-102'
        ]);

        Product::create([
            "name" => "Reebok Classic",
            'description' => 'Retro running shoe',
            'category' => 'shoes',
            'sku' => 'shoe-009',
            'price' => 90.00,
            'image_url' => '/images/reebokclassic.jpg',
            'stock_quantity' => 20,
            'can_be_returned' => true,
            'return_period_days' => 0,
            'supplier_id' => 3,
            'warehouse_location' => 'C-103'
        ]);

        Product::create([
            "name" => "Asics Gel-Kayano",
            'description' => 'Premium running support',
            'category' => 'shoes',
            'sku' => 'shoe-010',
            'price' => 160.00,
            'image_url' => '/images/asicsgelkayano.jpg',
            'stock_quantity' => 18,
            'can_be_returned' => true,
            'return_period_days' => 30,
            'supplier_id' => 1,
            'warehouse_location' => 'C-104'
        ]);
    }
}
