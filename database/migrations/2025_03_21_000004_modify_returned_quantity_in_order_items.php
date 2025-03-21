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
        // First check if returned_quantity column exists
        if (Schema::hasColumn('order_items', 'returned_quantity')) {
            // Drop the column to recreate it with nullable
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('returned_quantity');
            });
        }
        
        // Add the column back as nullable
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('returned_quantity')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In case we need to roll back, make it NOT NULL again
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'returned_quantity')) {
                $table->dropColumn('returned_quantity');
                $table->integer('returned_quantity')->default(0);
            }
        });
    }
};
