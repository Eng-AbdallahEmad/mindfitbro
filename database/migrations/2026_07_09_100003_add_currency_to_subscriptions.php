<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE subscriptions
            ADD COLUMN currency            CHAR(3)     NOT NULL DEFAULT 'SAR'  AFTER total,
            ADD COLUMN payment_method_key  VARCHAR(20) NULL                    AFTER currency,
            MODIFY subtotal        DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY coupon_discount DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY yearly_discount DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY total           DECIMAL(10,3) NOT NULL DEFAULT 0
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE subscriptions
            DROP COLUMN currency,
            DROP COLUMN payment_method_key,
            MODIFY subtotal        DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY coupon_discount DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY yearly_discount DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY total           DECIMAL(10,2) NOT NULL DEFAULT 0
        ");
    }
};
