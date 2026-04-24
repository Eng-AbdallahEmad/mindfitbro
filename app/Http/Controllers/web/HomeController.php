<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Services\Web\HomeService;

class HomeController extends Controller
{
    public function __construct(private HomeService $homeService)
    {
    }

    public function index()
    {
        $plans        = $this->homeService->getPlans();
        $subscription = $this->homeService->getSubscription();
        $cart         = $this->homeService->getCart();

        return view('app.web.index', [
            'plans'        => $plans,
            'subscription' => $subscription,
            'cart'         => $cart,
        ]);
    }
}
