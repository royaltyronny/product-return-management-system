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
        Schema::create('return_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('overall_satisfaction')->nullable();
            $table->integer('process_rating')->nullable();
            $table->integer('support_rating')->nullable();
            $table->integer('timeliness_rating')->nullable();
            $table->text('comments')->nullable();
            $table->text('suggestions')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_surveys');
    }
};
