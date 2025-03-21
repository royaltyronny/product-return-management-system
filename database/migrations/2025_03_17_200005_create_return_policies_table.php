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
        // Skip if the return_policies table already exists
        if (Schema::hasTable('return_policies')) {
            return;
        }
        
        Schema::create('return_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('return_period_days');
            $table->decimal('restocking_fee_percentage', 5, 2)->default(0);
            $table->boolean('requires_receipt')->default(true);
            $table->boolean('requires_original_packaging')->default(false);
            $table->boolean('allows_partial_returns')->default(true);
            $table->string('applies_to_category')->nullable();
            $table->unsignedBigInteger('applies_to_product_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('applies_to_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_policies');
    }
};
