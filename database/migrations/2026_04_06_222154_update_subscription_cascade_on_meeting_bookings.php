<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_bookings', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            
            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('meeting_bookings', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            
            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->nullOnDelete();
        });
    }
};
