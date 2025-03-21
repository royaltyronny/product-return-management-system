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
            // Add received_date column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'received_date')) {
                $table->timestamp('received_date')->nullable();
            }
            
            // Add other potentially missing date columns for the return workflow
            if (!Schema::hasColumn('return_requests', 'approved_date')) {
                $table->timestamp('approved_date')->nullable();
            }
            
            if (!Schema::hasColumn('return_requests', 'inspected_date')) {
                $table->timestamp('inspected_date')->nullable();
            }
            
            if (!Schema::hasColumn('return_requests', 'refunded_date')) {
                $table->timestamp('refunded_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $columns = ['received_date', 'approved_date', 'inspected_date', 'refunded_date'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('return_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
