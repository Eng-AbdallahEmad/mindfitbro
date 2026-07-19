<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE family_invitations
            MODIFY COLUMN status ENUM('pending','used','redeemed','expired')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert any 'used' rows to 'pending' before removing the value from the enum
        DB::statement("UPDATE family_invitations SET status = 'pending' WHERE status = 'used'");

        DB::statement("
            ALTER TABLE family_invitations
            MODIFY COLUMN status ENUM('pending','redeemed','expired')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
