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
        Schema::create('return_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained()->onDelete('cascade');
            $table->string('shipping_carrier');
            $table->string('tracking_number')->nullable();
            $table->string('shipping_label_url')->nullable();
            $table->string('status')->default('label_created');
            $table->timestamp('estimated_delivery_date')->nullable();
            $table->timestamp('actual_delivery_date')->nullable();
            $table->timestamp('pickup_date')->nullable();
            $table->json('pickup_address')->nullable();
            $table->unsignedBigInteger('destination_warehouse_id')->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_shipments');
    }
};
