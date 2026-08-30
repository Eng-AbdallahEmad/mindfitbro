<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Subscription;
use App\Services\OrderApprovalService;
use App\Services\Paymob\PaymobClient;
use App\Services\Paymob\PaymobResolution;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Server-to-server transaction callback. This is the SOLE source of truth
 * for activation (decision D5) — the browser redirect (PaymobCallbackController)
 * never mutates anything.
 *
 * Return-code classification (Batch 6, step 11):
 *   - 401: HMAC failed to verify — we don't trust the request at all. Not a
 *     "retry" signal in the transient/permanent sense; Paymob's own client
 *     wouldn't produce this, so nothing further to do.
 *   - 200 for every case we've EXPLICITLY made a decision about, even a bad
 *     one (unresolved/mismatched order, amount/currency mismatch, or an
 *     event already processed) — these are permanent outcomes; retrying
 *     changes nothing, and returning non-2xx here would just make Paymob
 *     retry forever against a decision that will never change.
 *   - 500 ONLY from the catch-all for a genuinely UNEXPECTED exception (a
 *     transient DB error, an uncaught bug) — these might succeed on a retry,
 *     or at minimum give us a chance to notice and fix before Paymob's retry
 *     window closes. This is the one case where "try again" is the right
 *     instinct.
 */
class PaymobWebhookController extends Controller
{
    public function handle(Request $request, PaymobClient $paymobClient, OrderApprovalService $approvalService): HttpResponse
    {
        $payload = $request->all();
        $type = $payload['type'] ?? null;
        $obj = $payload['obj'] ?? [];
        $receivedHmac = (string) $request->query('hmac', $request->input('hmac', ''));

        // Step 1: log receipt immediately, before any verification — every
        // hit gets an audit trail, tampered or not. Never the hmac/secret.
        Log::info('Paymob webhook received', [
            'type' => $type,
            'paymob_order_id' => data_get($obj, 'order.id'),
            'paymob_transaction_id' => data_get($obj, 'id'),
            'success' => data_get($obj, 'success'),
        ]);

        // TEMPORARY (Batch 6 live sandbox test only) — logs the full raw
        // body so the real payload shape can be captured, with the hmac
        // itself and known card/billing fields redacted. Gated behind an
        // env flag defaulting OFF; remove this block entirely once the
        // sandbox transaction is captured and confirmed.
        if (config('services.paymob.log_raw_webhook_payload')) {
            $sanitizedPayload = $payload;
            unset($sanitizedPayload['hmac']);
            if (isset($sanitizedPayload['obj']['source_data'])) {
                unset($sanitizedPayload['obj']['source_data']['pan']);
            }
            Log::info('Paymob webhook RAW PAYLOAD (temporary, sandbox capture)', $sanitizedPayload);
        }

        // Step 2: verify HMAC FIRST. Touch nothing in the DB on failure.
        if ($receivedHmac === '' || !$paymobClient->verifyHmac($obj, $receivedHmac)) {
            Log::warning('Paymob webhook HMAC verification failed', [
                'paymob_order_id' => data_get($obj, 'order.id'),
            ]);

            return response('', 401);
        }

        // Step 3: only transaction callbacks are handled here.
        if ($type !== 'TRANSACTION') {
            Log::info('Paymob webhook ignored: unhandled type', ['type' => $type]);

            return response('', 200);
        }

        // Step 4: resolve the subscription (Batch 5 C2 stale-intention semantics).
        $resolution = $paymobClient->resolveSubscription($obj);

        if (!$resolution->isValid()) {
            // resolveSubscription() already logged CRITICAL with full
            // context for mismatch/unresolved — this is the "customer may
            // have paid and we lost them" case, loud on purpose.
            Log::critical('PAYMOB WEBHOOK UNRESOLVED — a real payment may exist with no matching order. Manual investigation required.', [
                'outcome' => $resolution->outcome,
                'paymob_order_id' => data_get($obj, 'order.id'),
                'merchant_order_id' => data_get($obj, 'merchant_order_id'),
            ]);

            return response('', 200);
        }

        $subscription = $resolution->subscription;

        try {
            return DB::transaction(function () use ($subscription, $obj, $paymobClient, $approvalService) {
                $locked = Subscription::whereKey($subscription->id)->lockForUpdate()->firstOrFail();

                // Step 6: idempotency — the unique index on
                // paymob_transaction_id is the backstop, this is the
                // primary guard. Deliberately keyed on "has THIS row already
                // been marked paid", not "has any transaction id ever been
                // seen": paymob_transaction_id is only ever written on the
                // SUCCESS path (below) — never on a failed/declined
                // attempt — specifically so a customer who fails once and
                // then successfully retries (a fresh Paymob order on the
                // SAME subscription row, Batch 5) can still be approved by
                // that later webhook instead of being permanently blocked.
                $incomingTransactionId = (string) (data_get($obj, 'id') ?? '');

                $alreadyProcessed = $locked->paymob_transaction_id !== null
                    || ($incomingTransactionId !== '' && Subscription::where('paymob_transaction_id', $incomingTransactionId)->exists());

                if ($alreadyProcessed) {
                    Log::info('Paymob webhook: already processed, no-op', [
                        'subscription_id' => $locked->id,
                        'incoming_transaction_id' => $incomingTransactionId,
                        'stored_transaction_id' => $locked->paymob_transaction_id,
                    ]);

                    return response('', 200);
                }

                $success = filter_var(data_get($obj, 'success'), FILTER_VALIDATE_BOOLEAN);

                if (!$success) {
                    // Step 9: failed/declined/voided. paymob_transaction_id
                    // is deliberately NOT set here — see the comment above.
                    $isRefunded = filter_var(data_get($obj, 'is_refunded'), FILTER_VALIDATE_BOOLEAN);

                    $locked->update([
                        'status' => $isRefunded ? Subscription::STATUS_REFUNDED : Subscription::STATUS_PAYMENT_FAILED,
                        'payment_failure_reason' => (string) (data_get($obj, 'data.message') ?? 'فشلت عملية الدفع.'),
                    ]);

                    Log::info('Paymob webhook: transaction not successful, order marked', [
                        'subscription_id' => $locked->id,
                        'status' => $locked->status,
                    ]);

                    return response('', 200);
                }

                // Step 7: amount/currency must match what we persisted at
                // order-creation time — verifyChargedAmount() already logs
                // CRITICAL internally on mismatch. Underpayment (or a
                // corrupted/mismatched callback) must never activate a plan.
                if (!$paymobClient->verifyChargedAmount($locked, $obj)) {
                    return response('', 200);
                }

                // Step 8: success, verified — set paid, re-check the coupon
                // (C3: warn-and-approve, never re-price), then approve.
                $locked->update([
                    'paid_at' => now(),
                    'paymob_transaction_id' => $incomingTransactionId,
                ]);

                $this->warnIfCouponSoldOutSinceCheckout($locked);

                $approvalService->approve($locked, reviewedBy: null);

                return response('', 200);
            });
        } catch (\Throwable $e) {
            // Catch-all: genuinely unexpected (transient DB error, a bug we
            // haven't anticipated) — see the class docblock for why this,
            // and only this, gets a 500.
            Log::error('Paymob webhook: unexpected exception', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('', 500);
        }
    }

    /**
     * C3: the coupon is validated at checkout but only consumed at payment
     * (Coupon::CONSUMED_STATUSES excludes awaiting_payment). If it sold out
     * in between, the customer already paid the agreed price — honor it,
     * log a warning for visibility, and approve anyway. Never re-price.
     */
    private function warnIfCouponSoldOutSinceCheckout(Subscription $subscription): void
    {
        if (!$subscription->coupon_code) {
            return;
        }

        $coupon = Coupon::where('code', $subscription->coupon_code)->first();

        if ($coupon && $coupon->max_uses !== null && $coupon->usageCount() >= $coupon->max_uses) {
            Log::warning('Paymob webhook: coupon sold out between checkout and payment — honoring the original price and approving anyway', [
                'subscription_id' => $subscription->id,
                'coupon_code' => $subscription->coupon_code,
                'max_uses' => $coupon->max_uses,
            ]);
        }
    }
}
