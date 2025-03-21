<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a customer user
        $user = User::where('email', 'customer@example.com')->first();
        
        if (!$user) {
            // Create a customer user if none exists
            $user = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]);
        }
        
        // Get products
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->info('No products found. Please run the ProductSeeder first.');
            return;
        }
        
        // Define order statuses to create a variety of orders
        $orderStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];
        
        // Create more orders for the user with different statuses
        for ($i = 1; $i <= 10; $i++) {
            // For the first 6 orders, use different statuses
            // For the rest, use delivered/completed to make them eligible for returns
            $status = ($i <= 6) 
                ? $orderStatuses[$i-1] 
                : ($i % 2 == 0 ? Order::STATUS_DELIVERED : Order::STATUS_COMPLETED);
                
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => 0, // Will be calculated based on items
                'status' => $status,
                'payment_method' => ($i % 3 == 0) ? 'paypal' : 'credit_card',
                'payment_id' => 'PAY-' . strtoupper(Str::random(10)),
                'shipping_address' => [
                    'address' => '123 Main St',
                    'city' => 'Anytown',
                    'state' => 'CA',
                    'zip' => '12345',
                    'country' => 'USA'
                ],
                'billing_address' => [
                    'address' => '123 Main St',
                    'city' => 'Anytown',
                    'state' => 'CA',
                    'zip' => '12345',
                    'country' => 'USA'
                ],
                'shipping_method' => 'standard',
                'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
                'notes' => 'Sample order ' . $i,
                'order_date' => now()->subDays(rand(5, 15)), // Random date within the last 15 days
            ]);
            
            // Add 1-3 random products to the order
            $orderTotal = 0;
            $numItems = rand(1, 3);
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'status' => 'delivered',
                    'returned_quantity' => 0,
                ]);
                
                $orderTotal += $totalPrice;
            }
            
            // Update the order total
            $order->update(['total_amount' => $orderTotal]);
        }
    }
}
