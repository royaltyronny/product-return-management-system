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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('quantity_reserved')->default(0);
            $table->integer('quantity_pending_return')->default(0);
            $table->integer('quantity_returned')->default(0);
            $table->string('location_code')->nullable();
            $table->string('status')->default('in_stock');
            $table->timestamp('last_inventory_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint to ensure one product per warehouse
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
