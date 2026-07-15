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

        return view('app.admin.dashboard', compact('stats', 'recentSubscriptions', 'recentMembers'));
    }
}
