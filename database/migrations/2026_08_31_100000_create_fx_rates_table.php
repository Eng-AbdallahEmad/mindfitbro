<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per source currency we ever charge in (SAR, TND, USD). Stored
     * RAW — no markup baked in, that's applied at conversion time by
     * FxConverter so the markup can change without needing a re-fetch.
     */
    public function up(): void
    {
        Schema::create('fx_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3)->unique();
            $table->decimal('rate_to_egp', 16, 6);
            $table->string('source', 30);
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fx_rates');
    }
};
