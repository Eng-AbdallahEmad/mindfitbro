<?php

namespace App\Http\Middleware;

use App\Services\Web\CurrencyService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DetectCurrency
{
    public function __construct(private CurrencyService $currencyService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('currency')) {
            [$currency, $source] = $this->detect($request->ip());
            session(['currency' => $currency]);
            Log::debug('[DetectCurrency] fresh detection', [
                'ip'       => $request->ip(),
                'source'   => $source,
                'currency' => $currency,
            ]);
        }

        return $next($request);
    }

    /** Returns [currency, source] */
    private function detect(?string $ip): array
    {
        // ── Testing override (local dev) ──────────────────────────────
        if (!app()->isProduction() && config('services.location.testing_enabled')) {
            $country  = strtoupper(trim(config('services.location.testing_country_code', '')));
            $currency = $country ? $this->currencyService->fromCountryCode($country) : 'SAR';
            return [$currency, "testing:{$country}"];
        }

        // ── Localhost / private ranges → SAR ─────────────────────────
        if (!$ip || in_array($ip, ['127.0.0.1', '::1'])) {
            return ['SAR', 'localhost'];
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return ['SAR', 'private-range'];
        }

        // ── Live IP lookup ────────────────────────────────────────────
        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=countryCode");
            if ($response->ok()) {
                $country  = $response->json('countryCode', '');
                $currency = $this->currencyService->fromCountryCode($country);
                return [$currency, "ip-api:{$country}"];
            }
        } catch (\Throwable) {
            // API unreachable — fall through
        }

        return ['SAR', 'fallback'];
    }
}
