<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->enum('currency', ['SAR', 'EGP', 'TND', 'USD']);
            $table->decimal('price', 10, 3);
            $table->decimal('yearly_discount_rate', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
