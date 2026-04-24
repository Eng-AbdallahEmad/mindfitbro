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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('session_id')->nullable()->index();

            $table->boolean('is_yearly')->default(false);
            $table->string('coupon_code')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('yearly_discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};