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
        // Seed users (optional)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Call the ShoesSeeder to populate the shoes table
        $this->call(ShoesSeeder::class);

        // Call the ShoeReturnabilitySeeder to update the 'can_be_returned' column
        $this->call(ShoeReturnabilitySeeder::class);
    }
    $this->call(UserSeeder::class);
    

}
