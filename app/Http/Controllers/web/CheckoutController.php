<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\Web\CartService;
use App\Services\Web\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService     $cartService,
        private CheckoutService $checkoutService,
    ) {}

    public function process(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'لازم تسجل دخول الأول');
        }

        $cart = $this->cartService->getOrCreateCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'العربة فاضية');
        }

        try {
            $subscription = $this->checkoutService->checkout($cart);

            return redirect()->route('checkout.success', $subscription->id);

        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'حصل خطأ: ' . $e->getMessage());
        }
    }

    public function success(int $subscriptionId)
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->findOrFail($subscriptionId);

        return view('app.web.checkout-success', compact('subscription'));
    }
}