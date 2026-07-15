<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
                MODIFY COLUMN status ENUM(
                    'pending_review','approved','active','expired',
                    'rejected','cancelled','waiting'
                ) NOT NULL DEFAULT 'pending_review',
                ADD COLUMN receipt_path    VARCHAR(500)        NULL AFTER payment_method_key,
                ADD COLUMN rejection_reason TEXT               NULL AFTER receipt_path,
                ADD COLUMN reviewed_by     BIGINT UNSIGNED     NULL AFTER rejection_reason,
                ADD COLUMN reviewed_at     TIMESTAMP           NULL AFTER reviewed_by,
                ADD CONSTRAINT fk_subscriptions_reviewed_by
                    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
                DROP FOREIGN KEY fk_subscriptions_reviewed_by,
                DROP COLUMN reviewed_at,
                DROP COLUMN reviewed_by,
                DROP COLUMN rejection_reason,
                DROP COLUMN receipt_path,
                MODIFY COLUMN status ENUM(
                    'active','expired','cancelled','waiting'
                ) NOT NULL DEFAULT 'waiting'
        ");
    }
};
