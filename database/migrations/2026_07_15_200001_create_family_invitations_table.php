<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inviter_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('invitee_email');
            $table->string('invitee_name')->nullable();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->enum('status', ['pending', 'redeemed', 'expired'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            // One invitation per email per subscription (no spam per invitee)
            $table->unique(['subscription_id', 'invitee_email']);
            $table->index('inviter_user_id');
            $table->index('status');
            $table->index('coupon_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_invitations');
    }
};
