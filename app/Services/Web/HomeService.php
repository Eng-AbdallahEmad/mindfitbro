<?php

namespace App\Services\Web;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeService
{
    public function getPlans()
    {
        return Plan::with([
                'features' => fn($q) => $q->orderBy('feature_plan.sort_order'),
                'prices',
            ])
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
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')
                ->orWhereDate('end_date', '>=', now());
            })
            ->latest()
            ->first();
    }

    public function getPopularPlanId(): ?int
    {
        return Cache::remember('popular_plan_id', 86400, function () {
            $minCount = (int) config('plans.popular_min_subscriptions', 5);

            $top = Subscription::query()
                ->whereIn('status', ['approved', 'active', 'expired'])
                ->select('plan_id', DB::raw('COUNT(*) as cnt'))
                ->groupBy('plan_id')
                ->orderByDesc('cnt')
                ->orderBy('plan_id')  // tie-breaker: lowest plan_id wins consistently
                ->first();

            if ($top && $top->cnt >= $minCount) {
                return (int) $top->plan_id;
            }

            // Fallback: manual selection by admin
            return Plan::where('popular', true)->value('id');
        });
    }

    public function getLatestRelevantSubscription()
    {
        if (!Auth::check()) {
            return null;
        }

        return Subscription::query()
            ->with('plan')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['active', 'approved', 'pending_review'])
            ->where(function ($q) {
                $q->whereNull('end_date')
                ->orWhereDate('end_date', '>=', now());
            })
            ->latest()
            ->first();
    }

}