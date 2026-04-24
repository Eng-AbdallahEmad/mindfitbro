<?php

namespace App\Services\Web;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getOrCreateCart(): Cart
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        $cart = Cart::query()
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }, function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })
            ->latest()
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id'         => $userId,
                'session_id'      => $userId ? null : $sessionId,
                'is_yearly'       => false,
                'coupon_code'     => null,
                'subtotal'        => 0,
                'coupon_discount' => 0,
                'yearly_discount' => 0,
                'total'           => 0,
            ]);
        }

        return $cart->load('items.plan');
    }

    public function addPlan(int $planId, int $quantity = 1): Cart
    {
        $cart = $this->getOrCreateCart();

        $plan = Plan::query()
            ->where('is_active', true)
            ->findOrFail($planId);

        $item = $cart->items()->where('plan_id', $plan->id)->first();

        if ($item) {
            $item->increment('quantity', max(1, $quantity));
        } else {
            $cart->items()->create([
                'plan_id'               => $plan->id,
                'quantity'              => max(1, $quantity),
                'monthly_price'         => $plan->price,
                'yearly_discount_rate'  => $plan->yearly_discount_rate ?? 1,
                'final_price'           => $plan->price,
            ]);
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
            $item->update([
                'quantity' => $quantity,
            ]);
        }

        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function removeItem(int $itemId): Cart
    {
        $cart = $this->getOrCreateCart();

        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        return $this->recalculateCart($cart->fresh('items.plan'));
    }

    public function toggleYearly(bool $isYearly): Cart
    {
        $cart = $this->getOrCreateCart();

        $cart->update([
            'is_yearly' => $isYearly,
        ]);

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

        $subtotal = 0;          // السعر الأصلي قبل خصم السنوي
        $yearlyDiscount = 0;    // إجمالي خصم السنوي
        $discountedSubtotal = 0; // السعر بعد خصم السنوي وقبل الكوبون

        foreach ($cart->items as $item) {
            $baseMonthlyPrice = (float) $item->monthly_price;
            $rate = (float) ($item->yearly_discount_rate ?: 1);
            $quantity = (int) $item->quantity;

            if ($cart->is_yearly) {
                $originalYearlyPrice = round($baseMonthlyPrice * 12 * $quantity, 2);
                $discountedMonthlyPrice = round($baseMonthlyPrice * $rate, 2);
                $finalYearlyPrice = round($discountedMonthlyPrice * 12 * $quantity, 2);
                $itemYearlyDiscount = round($originalYearlyPrice - $finalYearlyPrice, 2);

                $item->update([
                    'final_price' => $finalYearlyPrice,
                ]);

                $subtotal += $originalYearlyPrice;
                $yearlyDiscount += $itemYearlyDiscount;
                $discountedSubtotal += $finalYearlyPrice;
            } else {
                $monthlyPrice = round($baseMonthlyPrice * $quantity, 2);

                $item->update([
                    'final_price' => $monthlyPrice,
                ]);

                $subtotal += $monthlyPrice;
                $discountedSubtotal += $monthlyPrice;
            }
        }

        // الكوبون يتحسب بعد خصم السنوي
        $couponDiscount = $this->calculateCouponDiscount($discountedSubtotal, $cart->coupon_code);

        $total = max(0, $discountedSubtotal - $couponDiscount);

        $cart->update([
            'subtotal'        => round($subtotal, 2),
            'yearly_discount' => round($yearlyDiscount, 2),
            'coupon_discount' => round($couponDiscount, 2),
            'total'           => round($total, 2),
        ]);

        return $cart->fresh('items.plan');
    }

    protected function calculateCouponDiscount(float $subtotal, ?string $couponCode): float
    {
        if (!$couponCode) {
            return 0;
        }

        $validCoupons = [
            'MFB10'      => 10,
            'MINDFITBRO' => 10,
            'WELCOME'    => 10,
            'EID2025'    => 10,
        ];

        $discountPercent = $validCoupons[$couponCode] ?? 0;

        if ($discountPercent <= 0) {
            return 0;
        }

        return round($subtotal * ($discountPercent / 100), 2);
    }
}