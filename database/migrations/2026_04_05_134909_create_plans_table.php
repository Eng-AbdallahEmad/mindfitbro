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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('icon_bg')->nullable();
            $table->string('icon_color')->nullable();
            $table->text('desc')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('popular')->default(false);
            $table->string('btn_class')->nullable();
            $table->integer('duration_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
