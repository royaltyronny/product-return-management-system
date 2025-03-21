<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'phone' => '555-123-4567',
            'address' => json_encode([
                'street' => '123 Admin St',
                'city' => 'Admin City',
                'state' => 'CA',
                'zip' => '90210',
                'country' => 'USA'
            ])
        ]);

        // Create support agent user
        User::create([
            'name' => 'Support Agent',
            'email' => 'support@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPPORT_AGENT,
            'phone' => '555-234-5678',
            'address' => json_encode([
                'street' => '456 Support Ave',
                'city' => 'Support City',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA'
            ])
        ]);
        
        // Create warehouse staff user
        User::create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_WAREHOUSE_STAFF,
            'phone' => '555-345-6789',
            'address' => json_encode([
                'street' => '789 Warehouse Blvd',
                'city' => 'Warehouse City',
                'state' => 'TX',
                'zip' => '75001',
                'country' => 'USA'
            ])
        ]);
        
        // Create finance team user
        User::create([
            'name' => 'Finance Team',
            'email' => 'finance@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_FINANCE,
            'phone' => '555-456-7890',
            'address' => json_encode([
                'street' => '101 Finance St',
                'city' => 'Finance City',
                'state' => 'IL',
                'zip' => '60601',
                'country' => 'USA'
            ])
        ]);

        // Create customer user
        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_CUSTOMER,
            'phone' => '555-567-8901',
            'address' => json_encode([
                'street' => '202 Customer Ave',
                'city' => 'Customer City',
                'state' => 'FL',
                'zip' => '33101',
                'country' => 'USA'
            ])
        ]);
    }
}
