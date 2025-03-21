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
        Schema::table('return_requests', function (Blueprint $table) {
            // Add user_id column
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Add product_id column
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Add rma_number column
            $table->string('rma_number')->unique();
            
            // Rename reason to return_reason
            $table->renameColumn('reason', 'return_reason');
            
            // Add return_category column
            $table->string('return_category');
            
            // Add description column
            $table->text('description')->nullable();
            
            // Add evidence_images column
            $table->json('evidence_images')->nullable();
            
            // Add refund_method column
            $table->string('refund_method')->default('original_payment');
            
            // Add refund_amount column
            $table->decimal('refund_amount', 10, 2)->nullable();
            
            // Add processing_warehouse_id column
            $table->foreignId('processing_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['processing_warehouse_id']);
            
            // Drop columns
            $table->dropColumn('user_id');
            $table->dropColumn('product_id');
            $table->dropColumn('rma_number');
            $table->dropColumn('return_category');
            $table->dropColumn('description');
            $table->dropColumn('evidence_images');
            $table->dropColumn('refund_method');
            $table->dropColumn('refund_amount');
            $table->dropColumn('processing_warehouse_id');
            
            // Rename return_reason back to reason
            $table->renameColumn('return_reason', 'reason');
        });
    }
};
