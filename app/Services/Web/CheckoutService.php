<?php

namespace App\Services\Web;

use App\Models\Subscription;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckoutService
{
    public function checkout(Cart $cart): Subscription
    {
        if ($cart->items->isEmpty()) {
            throw new \Exception('العربة فاضية');
        }

        if (!Auth::check()) {
            throw new \Exception('لازم تسجل دخول الأول');
        }

        return DB::transaction(function () use ($cart) {

            $now       = Carbon::now();
            $startDate = $now->toDateString();
            $endDate   = $cart->is_yearly
                ? $now->copy()->addYear()->toDateString()
                : $now->copy()->addMonth()->toDateString();

            $plansSnapshot = $cart->items->map(fn($item) => [
                'plan_id'     => $item->plan_id,
                'plan_name'   => $item->plan->name,
                'quantity'    => $item->quantity,
                'final_price' => $item->final_price,
            ])->toArray();

            $mainPlanId = $cart->items->first()->plan_id;

            $status = 'waiting';

            $subscription = Subscription::create([
                'user_id'         => Auth::id(),
                'plan_id'         => $mainPlanId,
                'start_date'      => $status === 'waiting' ? null : $startDate,
                'end_date'        => $status === 'waiting' ? null : $endDate,
                'status'          => $status,
                'plans_snapshot'  => $plansSnapshot,
                'is_yearly'       => $cart->is_yearly,
                'subtotal'        => $cart->subtotal,
                'coupon_discount' => $cart->coupon_discount,
                'yearly_discount' => $cart->yearly_discount,
                'total'           => $cart->total,
                'coupon_code'     => $cart->coupon_code,
            ]);

            $cart->items()->delete();

            $cart->update([
                'subtotal'        => 0,
                'coupon_discount' => 0,
                'yearly_discount' => 0,
                'total'           => 0,
                'coupon_code'     => null,
            ]);

            return $subscription;
        });
    }
}