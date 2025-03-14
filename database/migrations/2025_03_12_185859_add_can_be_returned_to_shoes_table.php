<?php

namespace Illuminate\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shoes', function (Blueprint $table) {
            // Add the 'can_be_returned' column with a default value (true or false)
            $table->boolean('can_be_returned')->default(true); // Default is true, meaning the item can be returned
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shoes', function (Blueprint $table) {
            // Drop the 'can_be_returned' column if the migration is rolled back
            $table->dropColumn('can_be_returned');
        });
    }
};
