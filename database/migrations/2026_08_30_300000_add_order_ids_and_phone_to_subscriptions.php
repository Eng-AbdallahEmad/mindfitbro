<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * paymob_order_ids: every attempt's Paymob order id, appended on each
     * intention. paymob_order_id (Batch 2) stays as the current/latest for
     * convenience — this is the full history, needed so a callback carrying
     * a superseded order id (customer paid from an old browser tab after
     * retrying) can still be recognized as a legitimate stale-but-valid
     * payment rather than an unrecognized/critical mismatch (Batch 5 C2).
     *
     * billing_phone: the phone number collected at checkout and sent to
     * Paymob's billing_data. Persisted so a retry (same subscription row,
     * same charge) can resend it without re-asking the customer.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->json('paymob_order_ids')->nullable()->after('paymob_order_id');
            $table->string('billing_phone', 20)->nullable()->after('paymob_intention_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['paymob_order_ids', 'billing_phone']);
        });
    }
};
