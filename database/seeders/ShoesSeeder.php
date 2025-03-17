<?php

namespace Database\Seeders;

use App\Models\Shoe;
use Illuminate\Database\Seeder;

class ShoesSeeder extends Seeder
{
    public function run(): void
    {
        $shoes = [
            ["name" => "Air Max 270", "description" => "Lightweight and stylish", "color" => "Black/White", "size" => "10", "price" => 120.00, "image_url" => "/images/airmax270.jpg", "can_be_returned" => true],
            ["name" => "Yeezy Boost 350", "description" => "Comfortable with sleek design", "color" => "Gray", "size" => "9.5", "price" => 220.00, "image_url" => "/images/yeezy350.jpg", "can_be_returned" => false],
            ["name" => "Jordan 1 Retro", "description" => "Classic high-top design", "color" => "Red/Black", "size" => "11", "price" => 170.00, "image_url" => "/images/jordan1retro.jpg", "can_be_returned" => true],
            ["name" => "Converse All-Star", "description" => "Casual everyday shoe", "color" => "White", "size" => "8", "price" => 60.00, "image_url" => "/images/converseallstar.jpg", "can_be_returned" => true],
            ["name" => "Adidas Ultraboost", "description" => "High-performance running shoe", "color" => "Black", "size" => "10.5", "price" => 180.00, "image_url" => "/images/ultraboost.jpg", "can_be_returned" => false],
            ["name" => "Puma RS-X", "description" => "Futuristic design with bold colors", "color" => "Blue/Red", "size" => "9", "price" => 140.00, "image_url" => "/images/pumarsx.jpg", "can_be_returned" => true],
            ["name" => "New Balance 574", "description" => "Classic and comfortable", "color" => "Gray/White", "size" => "11.5", "price" => 100.00, "image_url" => "/images/nb574.jpg", "can_be_returned" => false],
            ["name" => "Vans Old Skool", "description" => "Timeless skateboarding shoe", "color" => "Black/White", "size" => "8.5", "price" => 70.00, "image_url" => "/images/vansoldskool.jpg", "can_be_returned" => true],
            ["name" => "Reebok Classic", "description" => "Retro running shoe", "color" => "White", "size" => "10", "price" => 90.00, "image_url" => "/images/reebokclassic.jpg", "can_be_returned" => false],
            ["name" => "Asics Gel-Kayano", "description" => "Premium running support", "color" => "Blue/White", "size" => "12", "price" => 160.00, "image_url" => "/images/asicsgelkayano.jpg", "can_be_returned" => true],
        ];

        foreach ($shoes as $shoe) {
            Shoe::create($shoe);
        }
    }
}
