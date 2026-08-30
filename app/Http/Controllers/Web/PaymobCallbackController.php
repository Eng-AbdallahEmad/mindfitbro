<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\Paymob\PaymobClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * READ-ONLY (decision D5). The webhook (PaymobWebhookController) is the sole
 * source of truth for activation — this controller never mutates a
 * subscription under any circumstance, regardless of what the query string
 * claims.
 */
class PaymobCallbackController extends Controller
{
    public function show(Request $request, PaymobClient $paymobClient)
    {
        $flat = $request->query();

        $this->bestEffortVerifyRedirectHmac($flat);

        $subscription = $this->resolveFromRedirect($flat);

        abort_unless($subscription, 404);

        return view('app.web.purchase.paymob_callback', compact('subscription'));
    }

    public function status(Request $request, Subscription $subscription)
    {
        $authorized = (Auth::check() && $subscription->user_id === Auth::id())
            || (!Auth::check()
                && $subscription->guest_token
                && hash_equals($subscription->guest_token, (string) $request->query('guest_token', '')));

        abort_unless($authorized, 403);

        return response()->json([
            'status' => $subscription->status,
            'is_paid' => $subscription->isPaid(),
        ]);
    }

    /**
     * Best-effort per Batch 4 B5: the redirect's flat key names were never
     * confirmed against a real Paymob redirect (only the server-to-server
     * transaction callback's nested shape was documented). This reshapes
     * the flat query params into the same nested structure
     * PaymobClient::verifyHmac() expects and tries it — informational only,
     * per D5 this page renders regardless of the outcome. On failure it
     * logs only the received PARAMETER NAMES (never values), so a real
     * sandbox redirect can be used to correct this mapping (Batch 6, done
     * at the end of this batch).
     */
    private function bestEffortVerifyRedirectHmac(array $flat): void
    {
        $receivedHmac = (string) ($flat['hmac'] ?? '');

        if ($receivedHmac === '') {
            Log::warning('Paymob redirect callback missing hmac parameter', [
                'received_param_names' => array_keys($flat),
            ]);

            return;
        }

        $nested = array_merge($flat, [
            'order' => ['id' => $flat['order'] ?? null],
            'source_data' => [
                'pan' => $flat['source_data_pan'] ?? null,
                'sub_type' => $flat['source_data_sub_type'] ?? null,
                'type' => $flat['source_data_type'] ?? null,
            ],
        ]);

        if (!app(PaymobClient::class)->verifyHmac($nested, $receivedHmac)) {
            Log::warning('Paymob redirect callback HMAC did not verify (best-effort flat-key mapping, unconfirmed — informational only, page still renders)', [
                'received_param_names' => array_keys($flat),
            ]);
        }
    }

    private function resolveFromRedirect(array $flat): ?Subscription
    {
        $paymobOrderId = (string) ($flat['order'] ?? '');
        $merchantOrderId = (string) ($flat['merchant_order_id'] ?? '');

        if ($paymobOrderId !== '') {
            $subscription = Subscription::where('paymob_order_id', $paymobOrderId)
                ->orWhereJsonContains('paymob_order_ids', $paymobOrderId)
                ->first();

            if ($subscription) {
                return $subscription;
            }
        }

        if ($merchantOrderId !== '' && preg_match('/^sub-(\d+)-/', $merchantOrderId, $m)) {
            return Subscription::find((int) $m[1]);
        }

        return null;
    }
}
