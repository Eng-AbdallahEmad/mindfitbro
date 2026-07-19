<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();

            // ── Section 1: Basic physical info ──────────────────────
            $table->date('date_of_birth')->nullable();
            $table->decimal('current_weight', 5, 2)->nullable();
            $table->decimal('target_weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();

            // ── Section 2: Training (individually queryable) ─────────
            $table->string('primary_goal', 50)->nullable();  // weight_loss | muscle_gain | endurance | flexibility | general_fitness | other
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->tinyInteger('workout_days_per_week')->nullable();
            $table->smallInteger('session_duration_minutes')->nullable();

            // ── Sections 3-5: Flexible JSON buckets ──────────────────
            $table->json('training_details')->nullable(); // location, equipment, target_duration_weeks, fitness_score
            $table->json('nutrition')->nullable();        // meals_count, snacks, diet_type, water_intake, supplements, meal_timing, preferred_foods, disliked_foods
            $table->json('health')->nullable();           // has_injuries, injuries_details, health_conditions, allergies, medications
            $table->json('lifestyle')->nullable();        // daily_activity, sleep_hours, smoking, commitment_score, challenges

            // ── Section 6: Declaration ───────────────────────────────
            $table->timestamp('declaration_accepted_at')->nullable();
            $table->string('signature_text')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_assessments');
    }
};
