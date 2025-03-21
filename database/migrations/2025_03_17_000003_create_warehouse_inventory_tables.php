<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseInventoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('quantity_damaged')->default(0);
            $table->integer('quantity_refurbished')->default(0);
            $table->integer('quantity_pending')->default(0);
            $table->string('location_code')->nullable();
            $table->timestamp('last_updated_at');
            $table->unique(['warehouse_id', 'product_id']);
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('movement_type'); // add, remove, transfer, adjustment, return
            $table->integer('quantity');
            $table->enum('direction', ['in', 'out']);
            $table->string('inventory_type')->nullable(); // regular, damaged, refurbished, pending
            $table->string('reference_type')->nullable(); // order, return_request, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('location_code')->nullable();
            $table->text('notes')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('warehouse_inventory');
    }
}
