<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Full status enum before this migration (for reference, from
     * 2026_07_09_300001_extend_subscriptions_for_new_flow.php):
     *   'pending_review','approved','active','expired','rejected','cancelled','waiting'
     * 'waiting' had 0 rows and no write path anywhere in the app (confirmed
     * before writing this migration) — dropped rather than carried forward.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('paymob_order_id', 64)->nullable()->after('coupon_code');
            $table->string('paymob_transaction_id', 64)->nullable()->after('paymob_order_id');
            $table->string('paymob_intention_id', 64)->nullable()->after('paymob_transaction_id');
            $table->char('charged_currency', 3)->nullable()->after('paymob_intention_id');
            $table->unsignedBigInteger('charged_amount_cents')->nullable()->after('charged_currency');
            $table->decimal('fx_rate', 12, 6)->nullable()->after('charged_amount_cents');
            $table->string('fx_rate_source', 64)->nullable()->after('fx_rate');
            $table->timestamp('payment_intended_at')->nullable()->after('fx_rate_source');
            $table->timestamp('paid_at')->nullable()->after('payment_intended_at');
            $table->text('payment_failure_reason')->nullable()->after('paid_at');
            $table->string('payment_gateway', 20)->nullable()->default('paymob')->after('payment_failure_reason');

            // Idempotency backstop for the future webhook handler (Batch 6).
            // MySQL unique indexes allow multiple NULLs, so unpaid rows are unaffected.
            $table->unique('paymob_transaction_id');
            $table->index('paymob_order_id');

            // Matches the query shape the Batch 7 abandoned-payment sweeper will run.
            $table->index(['status', 'payment_intended_at']);
        });

        // Every row that exists as of this migration came through the manual
        // flow by definition — one statement, not row-by-row, since this is a
        // single unconditional column set across the whole table.
        DB::table('subscriptions')->update(['payment_gateway' => 'manual']);

        // Same NOT NULL / DEFAULT as before, value list extended with
        // awaiting_payment / payment_failed / refunded, 'waiting' dropped (see
        // class docblock). No charset/collation override needed — the original
        // column had none either, so it continues to use the table default.
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM(
            'pending_review','awaiting_payment','approved','active','expired',
            'rejected','cancelled','payment_failed','refunded'
        ) NOT NULL DEFAULT 'pending_review'");
    }

    public function down(): void
    {
        // Reverting the enum would silently mangle any row holding a status
        // value that only exists because of this migration. Refuse instead.
        $blocked = DB::table('subscriptions')
            ->whereIn('status', ['awaiting_payment', 'payment_failed', 'refunded'])
            ->count();

        if ($blocked > 0) {
            throw new \RuntimeException(
                "Cannot roll back 2026_08_30_100000_add_paymob_fields_to_subscriptions: ".
                "{$blocked} subscription(s) hold a status value introduced by this ".
                "migration (awaiting_payment/payment_failed/refunded). Reverting the ".
                "status ENUM would silently corrupt that data. Reassign those rows to ".
                "a pre-existing status manually before rolling back."
            );
        }

        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM(
            'pending_review','approved','active','expired','rejected','cancelled','waiting'
        ) NOT NULL DEFAULT 'pending_review'");

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique(['paymob_transaction_id']);
            $table->dropIndex(['paymob_order_id']);
            $table->dropIndex(['status', 'payment_intended_at']);

            $table->dropColumn([
                'paymob_order_id',
                'paymob_transaction_id',
                'paymob_intention_id',
                'charged_currency',
                'charged_amount_cents',
                'fx_rate',
                'fx_rate_source',
                'payment_intended_at',
                'paid_at',
                'payment_failure_reason',
                'payment_gateway',
            ]);
        });
    }
};
