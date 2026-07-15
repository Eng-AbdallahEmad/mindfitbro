<?php

namespace App\Services\Web;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function __construct(private CurrencyService $currencyService) {}

    public function getOrCreateCart(): Cart
    {
        $userId    = Auth::id();
        $sessionId = session()->getId();
        $currency  = $this->currencyService->current();

        $cart = Cart::query()
            ->when($userId,
                fn ($q) => $q->where('user_id', $userId),
                fn ($q) => $q->where('session_id', $sessionId)
            )
            ->latest()
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id'         => $userId,
                'session_id'      => $userId ? null : $sessionId,
                'duration_months' => 3,
                'coupon_code'     => null,
                'currency'        => $currency,
                'subtotal'        => 0,
                'coupon_discount' => 0,
                'total'           => 0,
            ]);
        } elseif ($cart->currency !== $currency) {
            $this->repriceItemsForCurrency($cart, $currency);
        }

        return $cart->load('items.plan');
    }

    public function addPlan(int $planId, int $quantity = 1): Cart
    {
        $cart     = $this->getOrCreateCart();
        $currency = $this->currencyService->current();
        $duration = (int) $cart->duration_months;

        $plan      = Plan::with('prices')->where('is_active', true)->findOrFail($planId);
        $planPrice = $plan->priceFor($currency, $duration)
                  ?? $plan->priceFor('SAR', $duration);
        $price     = $planPrice ? (float) $planPrice->price : 0;

        $existing = $cart->items()->where('plan_id', $plan->id)->first();

        if ($existing) {
            $existing->increment('quantity', max(1, $quantity));
        } else {
            $cart->items()->create([
                'plan_id'    => $plan->id,
                'quantity'   => max(1, $quantity),
                'price'      => $price,
                'final_price' => $price,
                'currency'   => $currency,
            ]);
        }

        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function setDuration(int $months): Cart
    {
        $months = in_array($months, [3, 6]) ? $months : 3;
        $cart   = $this->getOrCreateCart();

        $cart->update(['duration_months' => $months]);

        // Reprice all items for the new duration
        $cart->load('items.plan.prices');
        $currency = $cart->currency;

        foreach ($cart->items as $item) {
            $plan      = $item->plan;
            $planPrice = $plan?->priceFor($currency, $months)
                      ?? $plan?->priceFor('SAR', $months);
            $newPrice  = $planPrice ? (float) $planPrice->price : (float) $item->price;
            $item->update(['price' => $newPrice]);
        }

        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function updateQuantity(int $itemId, int $quantity): Cart
    {
        $cart = $this->getOrCreateCart();
        $item = $cart->items()->findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function removeItem(int $itemId): Cart
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->findOrFail($itemId)->delete();
        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function applyCoupon(?string $couponCode): Cart
    {
        $cart = $this->getOrCreateCart();
        $cart->update([
            'coupon_code' => $couponCode ? strtoupper(trim($couponCode)) : null,
        ]);
        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function recalculateCart(?Cart $cart = null): Cart
    {
        $cart ??= $this->getOrCreateCart();
        $cart->load('items.plan');

        $subtotal = 0;

        foreach ($cart->items as $item) {
            $unitPrice   = (float) $item->price;
            $qty         = (int) $item->quantity;
            $itemTotal   = round($unitPrice * $qty, 3);

            $item->update(['final_price' => $itemTotal]);
            $subtotal += $itemTotal;
        }

        $subtotal       = round($subtotal, 3);
        $couponDiscount = $this->calculateCouponDiscount($subtotal, $cart->coupon_code);
        $total          = max(0, round($subtotal - $couponDiscount, 3));

        $cart->update([
            'subtotal'        => $subtotal,
            'coupon_discount' => round($couponDiscount, 3),
            'total'           => $total,
        ]);

        return $cart->fresh('items.plan');
    }

    protected function calculateCouponDiscount(float $subtotal, ?string $couponCode): float
    {
        if (!$couponCode) {
            return 0;
        }
        $coupon = Coupon::findActive($couponCode);
        return $coupon ? $coupon->calculateDiscount($subtotal) : 0;
    }

    private function repriceItemsForCurrency(Cart $cart, string $newCurrency): void
    {
        $cart->load('items.plan.prices');
        $duration = (int) $cart->duration_months;

        foreach ($cart->items as $item) {
            $plan      = $item->plan;
            $planPrice = $plan?->priceFor($newCurrency, $duration)
                      ?? $plan?->priceFor('SAR', $duration);
            $newPrice  = $planPrice ? (float) $planPrice->price : (float) $item->price;

            $item->update(['price' => $newPrice, 'currency' => $newCurrency]);
        }

        $cart->update(['currency' => $newCurrency]);
        $this->recalculateCart($cart->fresh('items.plan'));
    }
}
