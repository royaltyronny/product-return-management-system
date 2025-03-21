<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if the orders table already exists
        if (Schema::hasTable('orders')) {
            return;
        }
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('status');
            $table->string('payment_method');
            $table->string('payment_id')->nullable();
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->string('shipping_method');
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('order_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
