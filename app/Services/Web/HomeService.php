<?php

namespace App\Services\Web;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class HomeService
{
    public function getPlans()
    {
        return Plan::with(['features' => function ($query) {
                $query->orderBy('feature_plan.sort_order');
            }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getSubscription()
    {
        if (!Auth::check()) {
            return null;
        }

        return Subscription::query()
            ->with('plan')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['active', 'waiting'])
            ->where(function ($q) {
                $q->whereNull('end_date')
                ->orWhereDate('end_date', '>=', now());
            })
            ->latest()
            ->first();
    }

    public function getCart()
    {
        if (!Auth::check()) {
            return null;
        }

        return \App\Models\Cart::with('items')
            ->where('user_id', Auth::id())
            ->whereHas('items')
            ->latest()
            ->first();
    }
}