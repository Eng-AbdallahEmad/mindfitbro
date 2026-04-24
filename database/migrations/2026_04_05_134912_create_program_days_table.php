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
        Schema::create('program_days', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')->constrained()->cascadeOnDelete();

            $table->string('day_name'); // السبت - الأحد
            $table->integer('day_order'); // ترتيب اليوم
            $table->enum('type', ['workout', 'rest'])->default('workout');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_days');
    }
};
