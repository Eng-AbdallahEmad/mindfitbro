<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::statement("ALTER TABLE carts ADD COLUMN duration_months TINYINT UNSIGNED NOT NULL DEFAULT 3 AFTER session_id");
        DB::statement("ALTER TABLE carts DROP COLUMN is_yearly, DROP COLUMN yearly_discount");
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::statement("ALTER TABLE carts
            DROP COLUMN duration_months,
            ADD COLUMN is_yearly TINYINT(1) NOT NULL DEFAULT 0 AFTER session_id,
            ADD COLUMN yearly_discount DECIMAL(10,3) NOT NULL DEFAULT 0 AFTER coupon_discount
        ");
    }
};
