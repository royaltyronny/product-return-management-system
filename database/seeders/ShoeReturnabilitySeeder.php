<?php

namespace Database\Seeders;

use App\Models\Shoe;
use Illuminate\Database\Seeder;

class ShoeReturnabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Add logic to insert data into the shoes table
        Shoe::whereIn('name', [
            'Air Max 270', 
            'Yeezy Boost 350', 
            'Jordan 1 Retro',
            'Converse All-Star',
            'Adidas Ultraboost',
            'Puma RS-X'
        ])
        ->update(['can_be_returned' => true]);
        
        Shoe::whereIn('name', [
            'New Balance 574', 
            'Vans Old Skool', 
            'Reebok Classic', 
            'Asics Gel-Kayano'
        ])
        ->update(['can_be_returned' => false]);
    }
}
