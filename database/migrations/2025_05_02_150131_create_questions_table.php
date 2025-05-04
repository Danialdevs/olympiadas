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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olympiad_id')->constrained()->onDelete('cascade');
            $table->json('question_text');
            $table->json('option_a')->nullable();
            $table->json('option_b')->nullable();
            $table->json('option_c')->nullable();
            $table->json('option_d')->nullable();
            $table->json('option_e')->nullable();
            $table->json('option_f')->nullable();
            $table->json('option_g')->nullable();
            $table->char('correct_option', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
