<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $revenueRows = Subscription::whereNotNull('start_date')
            ->selectRaw('currency, SUM(total) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        $stats = [
            'members'            => User::where('role', 'user')->count(),
            'coaches'            => User::where('role', 'coach')->count(),
            'subscriptions'      => Subscription::whereNotNull('start_date')->count(),
            'revenue_by_currency'=> $revenueRows->pluck('total', 'currency')->toArray(),
        ];

        $recentSubscriptions = Subscription::with(['plan', 'user'])
            ->whereNotNull('start_date')
            ->latest()
            ->limit(6)
            ->get();

        $recentMembers = User::where('role', 'user')
            ->latest()
            ->limit(6)
            ->get();

        // Step 6: unreviewed manual orders past the WARNING threshold —
        // visible on the landing page so it's seen without navigating to
        // the subscriptions list. Surfacing only, never auto-resolved.
        $overdueManualReviews = Subscription::where('status', Subscription::STATUS_PENDING_REVIEW)
            ->where('payment_gateway', Subscription::GATEWAY_MANUAL)
            ->where(function ($q) {
                $q->where('payment_intended_at', '<', now()->subHours((int) config('payment.manual_review_thresholds.warning_hours', 48)))
                  ->orWhere(function ($q2) {
                      $q2->whereNull('payment_intended_at')
                         ->where('created_at', '<', now()->subHours((int) config('payment.manual_review_thresholds.warning_hours', 48)));
                  });
            })
            ->count();

        return view('app.admin.dashboard', compact('stats', 'recentSubscriptions', 'recentMembers', 'overdueManualReviews'));
    }
}
