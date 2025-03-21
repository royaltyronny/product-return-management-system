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
        // Skip if the return_requests table already exists
        if (Schema::hasTable('return_requests')) {
            return;
        }
        
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('rma_number')->unique();
            $table->text('return_reason');
            $table->string('return_category');
            $table->text('description')->nullable();
            $table->json('evidence_images')->nullable();
            $table->string('status')->default('pending');
            $table->string('refund_method')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->decimal('restocking_fee', 10, 2)->default(0);
            $table->string('pickup_location')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('quality_check_result')->nullable();
            $table->text('warehouse_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processing_warehouse_id')->nullable();
            $table->timestamps();
            
            $table->foreign('processing_warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
