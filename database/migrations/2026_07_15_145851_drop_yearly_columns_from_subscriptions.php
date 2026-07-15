<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['is_yearly', 'yearly_discount']);
        });
    }

    // Restores column structure only — data must be recovered from subscriptions_backup_20260715
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('is_yearly')->default(false)->after('plans_snapshot');
            $table->decimal('yearly_discount', 10, 3)->default(0)->after('coupon_discount');
        });
    }
};
