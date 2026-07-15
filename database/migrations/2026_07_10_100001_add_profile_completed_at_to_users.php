<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('profile_completed_at')->nullable()->after('terms_accepted_at');
        });

        // Allow auto-created users to have null gender until they complete profile
        DB::statement('ALTER TABLE users MODIFY COLUMN gender VARCHAR(255) NULL');

        // Backfill: all existing users already have complete profiles
        DB::statement('UPDATE users SET profile_completed_at = NOW() WHERE profile_completed_at IS NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_completed_at');
        });
    }
};
