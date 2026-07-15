<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('plan_prices')->truncate();

        DB::statement("ALTER TABLE plan_prices
            DROP INDEX plan_prices_plan_id_currency_unique,
            DROP COLUMN yearly_discount_rate,
            ADD COLUMN duration_months TINYINT UNSIGNED NOT NULL DEFAULT 3 AFTER currency,
            ADD UNIQUE KEY plan_prices_plan_currency_duration_unique (plan_id, currency, duration_months)
        ");
    }

    public function down(): void
    {
        DB::table('plan_prices')->truncate();

        DB::statement("ALTER TABLE plan_prices
            DROP INDEX plan_prices_plan_currency_duration_unique,
            DROP COLUMN duration_months,
            ADD COLUMN yearly_discount_rate DECIMAL(5,2) NULL,
            ADD UNIQUE KEY plan_prices_plan_id_currency_unique (plan_id, currency)
        ");
    }
};
