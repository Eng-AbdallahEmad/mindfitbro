<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('weight', 5, 2);
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('body_fat_percentage', 4, 2)->nullable();
            $table->decimal('muscle_mass', 5, 2)->nullable();
            $table->enum('fitness_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->text('notes')->nullable();
            $table->date('evaluated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_evaluations');
    }
};
