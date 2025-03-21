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
        // Skip if the products table already exists
        if (Schema::hasTable('products')) {
            return;
        }
        
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('can_be_returned')->default(true);
            $table->integer('return_period_days')->default(30);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('warehouse_location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
