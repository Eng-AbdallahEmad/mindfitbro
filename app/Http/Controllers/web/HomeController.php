<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\BeforeAfter;
use App\Models\Testimonial;
use App\Models\Video;
use App\Services\Web\HomeService;
use App\Services\Web\CurrencyService;

class HomeController extends Controller
{
    public function __construct(
        private HomeService     $homeService,
        private CurrencyService $currencyService,
    ) {}

    public function index()
    {
        $plans        = $this->homeService->getPlans();
        $subscription = $this->homeService->getSubscription();

        $settings      = Setting::pluck('value', 'key');
        $videos        = Video::active()->orderBy('sort_order')->get();
        $testimonials  = Testimonial::active()->orderBy('sort_order')->get();
        $beforeAfters  = BeforeAfter::active()->orderBy('sort_order')->get();

        $currency     = $this->currencyService->current();
        $currencyMeta = $this->currencyService->jsConfig($currency);

        return view('app.web.index', [
            'plans'        => $plans,
            'subscription' => $subscription,
            'settings'     => $settings,
            'videos'       => $videos,
            'testimonials' => $testimonials,
            'beforeAfters' => $beforeAfters,
            'currency'     => $currency,
            'currencyMeta' => $currencyMeta,
        ]);
    }
}
