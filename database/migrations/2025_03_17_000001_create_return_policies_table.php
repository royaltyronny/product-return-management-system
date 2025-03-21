<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('return_period_days')->default(30);
            $table->decimal('restocking_fee_percentage', 5, 2)->default(0);
            $table->boolean('requires_receipt')->default(true);
            $table->boolean('requires_original_packaging')->default(false);
            $table->boolean('allows_partial_returns')->default(true);
            $table->boolean('applies_to_all_products')->default(false);
            $table->boolean('active')->default(true);
            $table->date('effective_date');
            $table->date('expiration_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Create pivot tables for policy relationships
        Schema::create('product_return_policy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_policy_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('category_return_policy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_policy_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('category_return_policy');
        Schema::dropIfExists('product_return_policy');
        Schema::dropIfExists('return_policies');
    }
}
