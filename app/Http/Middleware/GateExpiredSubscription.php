<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GateExpiredSubscription
{
    // Routes the gate must never block — renewal / journey pages / public routes
    private const EXCLUDED = [
        'home', 'home.*',
        'purchase.*',
        'journey.*',
        'logout',
        'locale.*',
        'currency.*',
        'complete-profile.*',
        'password.*',
        'privacy-policy',
        'terms-of-service',
        'about-us',
        'contact-us',
        'delivery-policy',
        'refund-cancellation-policy',
        'calorie-calculator',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only gate authenticated regular users
        if (! Auth::check() || Auth::user()->role !== 'user') {
            return $next($request);
        }

        // Explicit route exclusions — avoid redirect loops
        if ($request->routeIs(self::EXCLUDED)) {
            return $next($request);
        }

        // Check if the LATEST subscription (by id) is expired
        $latest = Auth::user()
            ->subscriptions()
            ->latest('id')
            ->first();

        if ($latest && $latest->status === Subscription::STATUS_EXPIRED) {
            return redirect()->route('journey.show', $latest->id);
        }

        return $next($request);
    }
}
