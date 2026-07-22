<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Never intercept admin panel, health-check, or API routes.
        if ($request->is('admin*') || $request->is('up') || $request->is('api*')) {
            return $next($request);
        }

        // ── Maintenance check ──────────────────────────────────────────────
        // Setting::get() uses a per-request static cache (one DB hit per process).
        // PHP-FPM resets static state per request so there is zero cross-request
        // staleness — the only source of delay is the HTTP cache layer, fixed below.
        if (Setting::get('maintenance_mode_enabled', '0') !== '1') {
            // Maintenance is OFF: pass through but stamp every response so
            // browsers / proxies / CDN never serve a stale cached copy of the
            // normal site after maintenance is toggled on.
            return $this->noCache($next($request));
        }

        // Authenticated admins bypass maintenance so they can reach the toggle.
        $admin = Auth::guard('admin')->user();
        if ($admin && $admin->role === 'admin') {
            return $next($request);
        }

        // ── Serve maintenance page ─────────────────────────────────────────
        $eta      = Setting::get('maintenance_eta') ?: null;
        $message  = Setting::get('maintenance_message') ?: 'نعمل على تحسينات لتجربتك. سنعود قريباً!';
        $waNumber = \App\Services\Web\ContactInfo::current()['whatsapp'];

        $response = $this->noCache(
            response()->view('maintenance', compact('message', 'eta', 'waNumber'), 503)
        );

        if ($eta) {
            $seconds = max(0, Carbon::parse($eta)->timestamp - now()->timestamp);
            $response->headers->set('Retry-After', (string) $seconds);
        }

        return $response;
    }

    private function noCache(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma',         'no-cache');
        $response->headers->set('Expires',        'Thu, 01 Jan 1970 00:00:00 GMT');

        return $response;
    }
}
