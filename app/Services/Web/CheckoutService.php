<?php

namespace App\Services\Web;

use App\Models\Subscription;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function checkout(
        Cart $cart,
        bool $guest = false,
        ?string $guestName = null,
        ?string $guestEmail = null
    ): Subscription {
        if ($cart->items->isEmpty()) {
            throw new \Exception('العربة فاضية');
        }

        if (! $guest && ! Auth::check()) {
            throw new \Exception('لازم تسجل دخول الأول');
        }

        return DB::transaction(function () use ($cart, $guest, $guestName, $guestEmail) {

            $plansSnapshot = $cart->items->map(fn ($item) => [
                'plan_id'     => $item->plan_id,
                'plan_name'   => $item->plan->name,
                'quantity'    => $item->quantity,
                'final_price' => $item->final_price,
            ])->toArray();

            $data = [
                'user_id'         => $guest ? null : Auth::id(),
                'plan_id'         => $cart->items->first()->plan_id,
                'start_date'      => null,
                'end_date'        => null,
                'status'          => 'waiting',
                'plans_snapshot'  => $plansSnapshot,
                'is_yearly'       => $cart->is_yearly,
                'subtotal'        => $cart->subtotal,
                'coupon_discount' => $cart->coupon_discount,
                'yearly_discount' => $cart->yearly_discount,
                'total'           => $cart->total,
                'coupon_code'     => $cart->coupon_code,
            ];

            if ($guest) {
                $data['guest_name']  = $guestName;
                $data['guest_email'] = $guestEmail;
                $data['guest_token'] = Str::random(64);
            }

            $subscription = Subscription::create($data);

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
