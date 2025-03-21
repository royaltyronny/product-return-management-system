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
            // Add inspection_date column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'inspection_date')) {
                $table->timestamp('inspection_date')->nullable();
            }
            
            // Add inspection_notes column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'inspection_notes')) {
                $table->text('inspection_notes')->nullable();
            }
            
            // Add quality_check_result column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'quality_check_result')) {
                $table->string('quality_check_result')->nullable();
            }
            
            // Add warehouse_notes column if it doesn't exist
            if (!Schema::hasColumn('return_requests', 'warehouse_notes')) {
                $table->text('warehouse_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $columns = ['inspection_date', 'inspection_notes', 'quality_check_result', 'warehouse_notes'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('return_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
