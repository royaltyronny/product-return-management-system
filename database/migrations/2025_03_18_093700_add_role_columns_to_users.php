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
        Schema::table('users', function (Blueprint $table) {
            // Add role column if it doesn't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('customer');
            }
            
            // Add phone column if it doesn't exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            
            // Add address column if it doesn't exist
            if (!Schema::hasColumn('users', 'address')) {
                $table->json('address')->nullable();
            }
            
            // Add profile_image column if it doesn't exist
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }
};
