<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cart_items')->truncate();

        DB::statement("ALTER TABLE cart_items
            CHANGE COLUMN monthly_price price DECIMAL(10,3) NOT NULL,
            DROP COLUMN yearly_discount_rate
        ");
    }

    public function down(): void
    {
        DB::table('cart_items')->truncate();

        DB::statement("ALTER TABLE cart_items
            CHANGE COLUMN price monthly_price DECIMAL(10,2) NOT NULL,
            ADD COLUMN yearly_discount_rate DECIMAL(5,2) NULL AFTER monthly_price
        ");
    }
};
