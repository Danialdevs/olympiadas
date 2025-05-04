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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olympiad_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('school')->nullable();
            $table->string('code')->unique();
            $table->integer('total_score')->default(0);
            $table->json('answers')->nullable();
            $table->timestamp('finished_time')->nullable();
            $table->boolean('used')->default(false);
            $table->string('language');
            $table->string('mentor_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
