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
        Schema::create('return_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('refund_method');
            $table->string('status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->timestamp('refund_date')->nullable();
            $table->string('store_credit_code')->nullable();
            $table->decimal('restocking_fee_applied', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();
            
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_refunds');
    }
};
