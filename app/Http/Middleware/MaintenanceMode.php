<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Never block admin panel, health-check, or non-web routes
        if ($request->is('admin*') || $request->is('up') || $request->is('api*')) {
            return $next($request);
        }

        if (Setting::get('maintenance_mode_enabled', '0') !== '1') {
            return $next($request);
        }

        // Authenticated admins bypass maintenance
        $admin = Auth::guard('admin')->user();
        if ($admin && $admin->role === 'admin') {
            return $next($request);
        }

        $eta       = Setting::get('maintenance_eta') ?: null;
        $message   = Setting::get('maintenance_message') ?: 'نعمل على تحسينات لتجربتك. سنعود قريباً!';
        $waNumber  = Setting::get('whatsapp_number', '966593035979');

        $response = response()->view('maintenance', compact('message', 'eta', 'waNumber'), 503);

        if ($eta) {
            $seconds = max(0, Carbon::parse($eta)->timestamp - now()->timestamp);
            $response->headers->set('Retry-After', (string) $seconds);
        }

        return $response;
    }
}
