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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->default(0)->after('user_id');
            $table->string('status')->default('pending')->after('total_amount');
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_id')->nullable()->after('payment_method');
            $table->json('shipping_address')->nullable()->after('payment_id');
            $table->json('billing_address')->nullable()->after('shipping_address');
            $table->string('shipping_method')->nullable()->after('billing_address');
            $table->string('tracking_number')->nullable()->after('shipping_method');
            $table->text('notes')->nullable()->after('tracking_number');
            $table->timestamp('order_date')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount',
                'status',
                'payment_method',
                'payment_id',
                'shipping_address',
                'billing_address',
                'shipping_method',
                'tracking_number',
                'notes',
                'order_date'
            ]);
        });
    }
};
