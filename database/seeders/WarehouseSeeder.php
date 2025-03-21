<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Main Distribution Center',
                'code' => 'MDC-001',
                'address' => '123 Logistics Way',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip_code' => '60007',
                'country' => 'USA',
                'contact_name' => 'John Warehouse',
                'contact_email' => 'jwarehouse@example.com',
                'contact_phone' => '(312) 555-1234',
                'is_active' => true,
                'can_process_returns' => true,
                'can_process_refurbishment' => true,
                'created_by' => 1, // Admin user
                'updated_by' => 1,
            ],
            [
                'name' => 'East Coast Facility',
                'code' => 'ECF-002',
                'address' => '456 Shipping Avenue',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'USA',
                'contact_name' => 'Sarah Manager',
                'contact_email' => 'smanager@example.com',
                'contact_phone' => '(212) 555-6789',
                'is_active' => true,
                'can_process_returns' => true,
                'can_process_refurbishment' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'West Coast Hub',
                'code' => 'WCH-003',
                'address' => '789 Return Boulevard',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90001',
                'country' => 'USA',
                'contact_name' => 'Mike Logistics',
                'contact_email' => 'mlogistics@example.com',
                'contact_phone' => '(213) 555-4321',
                'is_active' => true,
                'can_process_returns' => true,
                'can_process_refurbishment' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
