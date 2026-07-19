<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FamilyInvitation;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\BeforeAfter;
use App\Models\Testimonial;
use App\Models\Video;
use App\Services\Web\HomeService;
use App\Services\Web\CurrencyService;
use App\Services\Web\SeasonService;

class HomeController extends Controller
{
    public function __construct(
        private HomeService     $homeService,
        private CurrencyService $currencyService,
        private SeasonService   $seasonService,
    ) {}

    public function index()
    {
        $plans        = $this->homeService->getPlans();
        $subscription = $this->homeService->getSubscription();
        $popularPlanId = $this->homeService->getPopularPlanId();

        $settings      = Setting::pluck('value', 'key');
        $videos        = Video::active()->orderBy('sort_order')->get();
        $testimonials  = Testimonial::active()->orderBy('sort_order')->get();
        $beforeAfters  = BeforeAfter::active()->orderBy('sort_order')->get();
        $partners      = Partner::where('is_active', true)->orderBy('sort_order')->get();

        $currency     = $this->currencyService->current();
        $currencyMeta = $this->currencyService->jsConfig($currency);

        $activeSeason       = $this->seasonService->getActive();
        $rewardSubscription = $this->homeService->getLatestRelevantSubscription();
        $rewardMaxInvites   = (int) ($settings->get('family_reward_max_invites') ?: 5);
        $rewardPlanId       = (int) ($settings->get('family_reward_plan_id') ?: 0);
        $rewardUsedInvites  = ($rewardSubscription && $rewardSubscription->plan_id === $rewardPlanId)
            ? FamilyInvitation::where('subscription_id', $rewardSubscription->id)->count()
            : 0;

        return view('app.web.index', [
            'plans'               => $plans,
            'popularPlanId'       => $popularPlanId,
            'subscription'        => $subscription,
            'settings'            => $settings,
            'videos'              => $videos,
            'testimonials'        => $testimonials,
            'beforeAfters'        => $beforeAfters,
            'partners'            => $partners,
            'currency'            => $currency,
            'currencyMeta'        => $currencyMeta,
            'activeSeason'        => $activeSeason,
            'rewardSubscription'  => $rewardSubscription,
            'rewardUsedInvites'   => $rewardUsedInvites,
            'rewardMaxInvites'    => $rewardMaxInvites,
        ]);
    }
}
