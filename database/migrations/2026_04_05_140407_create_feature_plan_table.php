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
        Schema::create('feature_plan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_included')->default(true); // بدل check
            $table->integer('sort_order')->default(0); // ترتيب الميزة داخل الباقة

            $table->timestamps();

            $table->unique(['plan_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_plan');
    }
};
