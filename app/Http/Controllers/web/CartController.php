<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function index()
    {
        $cart = $this->cartService->getOrCreateCart();
        return view('app.web.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'plan_id'  => 'required|exists:plans,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $this->cartService->addPlan(
            $request->plan_id,
            $request->quantity ?? 1
        );

            return redirect()->route('cart.index')->with('success', 'تمت إضافة الباقة إلى العربة بنجاح');
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->cartService->updateQuantity(
            $request->item_id,
            $request->quantity
        );

        return response()->json($this->cartSummary($cart));
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $cart = $this->cartService->removeItem($request->item_id);

        return response()->json($this->cartSummary($cart));
    }

    public function toggleYearly(Request $request)
    {
        $request->validate([
            'is_yearly' => 'required|boolean',
        ]);

        $cart = $this->cartService->toggleYearly($request->is_yearly);

        return response()->json($this->cartSummary($cart));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $cart = $this->cartService->applyCoupon($request->coupon_code);

        $validCoupons = ['MFB10', 'MINDFITBRO', 'WELCOME', 'EID2025'];
        $code         = strtoupper(trim($request->coupon_code ?? ''));
        $isValid      = $code && in_array($code, $validCoupons);

        return response()->json([
            ...$this->cartSummary($cart),
            'coupon_valid'   => $isValid,
            'coupon_invalid' => $code && !$isValid,
        ]);
    }

    // ─── Helper: بيرجع كل البيانات المحتاجة للـ JS ───────────
    private function cartSummary(\App\Models\Cart $cart): array
    {
        return [
            'count'           => $cart->items->count(),
            'subtotal'        => number_format($cart->subtotal, 2),
            'coupon_discount' => number_format($cart->coupon_discount, 2),
            'yearly_discount' => number_format($cart->yearly_discount, 2),
            'total'           => number_format($cart->total, 2),
            'is_yearly'       => $cart->is_yearly,
            'has_coupon'      => (bool) $cart->coupon_code,
            'items'           => $cart->items->map(fn($item) => [
                'id'          => $item->id,
                'plan_id'     => $item->plan_id,
                'quantity'    => $item->quantity,
                'final_price' => number_format($item->final_price, 2),
                'original_price' => number_format($item->monthly_price * 12 * $item->quantity, 2),
            ]),
        ];
    }
}