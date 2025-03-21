<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the UserSeeder to create users
        $this->call(UserSeeder::class);

        // Call the ShoesSeeder to populate the shoes table
        $this->call(ShoesSeeder::class);

        // Call the ShoeReturnabilitySeeder to update the 'can_be_returned' column
        $this->call(ShoeReturnabilitySeeder::class);
        
        // Call the ProductSeeder to create products for the return system
        $this->call(ProductSeeder::class);
        
        // Call the OrderSeeder to create orders for the return system
        $this->call(OrderSeeder::class);
        
        // Call the WarehouseSeeder to create warehouses for the return system
        $this->call(WarehouseSeeder::class);
    }
}
