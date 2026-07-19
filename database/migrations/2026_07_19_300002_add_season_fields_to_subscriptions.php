<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
                ADD COLUMN season_id                  BIGINT UNSIGNED NULL    AFTER coupon_discount,
                ADD COLUMN season_name                VARCHAR(100)    NULL    AFTER season_id,
                ADD COLUMN season_discount_percentage DECIMAL(5,2)    NULL    AFTER season_name,
                ADD COLUMN season_discount            DECIMAL(10,3)   NOT NULL DEFAULT 0 AFTER season_discount_percentage,
                ADD CONSTRAINT fk_subscriptions_season
                    FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE SET NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
                DROP FOREIGN KEY fk_subscriptions_season,
                DROP COLUMN season_discount,
                DROP COLUMN season_discount_percentage,
                DROP COLUMN season_name,
                DROP COLUMN season_id
        ");
    }
};
