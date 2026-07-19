<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 100);
            $table->string('name_en', 100);
            $table->decimal('discount_percentage', 5, 2);   // 1.00 – 90.00
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Fast lookup for the current active season
            $table->index(['is_active', 'starts_at', 'ends_at'], 'idx_seasons_active_range');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
