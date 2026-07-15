<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // carts: add currency + widen monetary columns to 3 decimal places for TND
        DB::statement("ALTER TABLE carts
            ADD COLUMN currency CHAR(3) NOT NULL DEFAULT 'SAR' AFTER total,
            MODIFY subtotal        DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY coupon_discount DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY yearly_discount DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY total           DECIMAL(10,3) NOT NULL DEFAULT 0
        ");

        // cart_items: add currency + widen price columns
        DB::statement("ALTER TABLE cart_items
            ADD COLUMN currency CHAR(3) NOT NULL DEFAULT 'SAR' AFTER final_price,
            MODIFY monthly_price DECIMAL(10,3) NOT NULL DEFAULT 0,
            MODIFY final_price   DECIMAL(10,3) NOT NULL DEFAULT 0
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE carts
            DROP COLUMN currency,
            MODIFY subtotal        DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY coupon_discount DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY yearly_discount DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY total           DECIMAL(10,2) NOT NULL DEFAULT 0
        ");

        DB::statement("ALTER TABLE cart_items
            DROP COLUMN currency,
            MODIFY monthly_price DECIMAL(10,2) NOT NULL DEFAULT 0,
            MODIFY final_price   DECIMAL(10,2) NOT NULL DEFAULT 0
        ");
    }
};
