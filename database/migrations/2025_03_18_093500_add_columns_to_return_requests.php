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
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            // Add product_id column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'product_id')) {
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
            }
            
            // Add rma_number column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'rma_number')) {
                $table->string('rma_number')->unique();
            }
            
            // Rename reason to return_reason if reason exists and return_reason doesn't
            if (Schema::hasColumn('return_requests', 'reason') && !Schema::hasColumn('return_requests', 'return_reason')) {
                $table->renameColumn('reason', 'return_reason');
            } else if (!Schema::hasColumn('return_requests', 'return_reason')) {
                $table->text('return_reason');
            }
            
            // Add return_category column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'return_category')) {
                $table->string('return_category')->default('defective');
            }
            
            // Add description column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'description')) {
                $table->text('description')->nullable();
            }
            
            // Add evidence_images column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'evidence_images')) {
                $table->json('evidence_images')->nullable();
            }
            
            // Add refund_method column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'refund_method')) {
                $table->string('refund_method')->default('original_payment');
            }
            
            // Add refund_amount column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable();
            }
            
            // Add processing_warehouse_id column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'processing_warehouse_id')) {
                $table->foreignId('processing_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            // Drop foreign key constraints first
            if (Schema::hasColumn('return_requests', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('return_requests', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            
            if (Schema::hasColumn('return_requests', 'processing_warehouse_id')) {
                $table->dropForeign(['processing_warehouse_id']);
                $table->dropColumn('processing_warehouse_id');
            }
            
            // Drop other columns
            if (Schema::hasColumn('return_requests', 'rma_number')) {
                $table->dropColumn('rma_number');
            }
            
            if (Schema::hasColumn('return_requests', 'return_category')) {
                $table->dropColumn('return_category');
            }
            
            if (Schema::hasColumn('return_requests', 'description')) {
                $table->dropColumn('description');
            }
            
            if (Schema::hasColumn('return_requests', 'evidence_images')) {
                $table->dropColumn('evidence_images');
            }
            
            if (Schema::hasColumn('return_requests', 'refund_method')) {
                $table->dropColumn('refund_method');
            }
            
            if (Schema::hasColumn('return_requests', 'refund_amount')) {
                $table->dropColumn('refund_amount');
            }
            
            // Rename return_reason back to reason
            if (Schema::hasColumn('return_requests', 'return_reason') && !Schema::hasColumn('return_requests', 'reason')) {
                $table->renameColumn('return_reason', 'reason');
            }
        });
    }
};
