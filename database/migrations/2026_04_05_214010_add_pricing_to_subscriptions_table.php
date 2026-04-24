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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->json('plans_snapshot')->nullable();
            $table->boolean('is_yearly')->default(false);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('yearly_discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
};
