<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\Web\CartService;
use App\Services\Web\CurrencyService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService     $cartService,
        private CurrencyService $currencyService,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getOrCreateCart();
        return view('app.web.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'plan_id'        => 'required|exists:plans,id',
            'quantity'       => 'nullable|integer|min:1',
            'duration_months' => 'nullable|integer|in:3,6',
        ]);

        if ($request->filled('duration_months')) {
            $this->cartService->setDuration((int) $request->duration_months);
        }

        $this->cartService->addPlan($request->plan_id, $request->quantity ?? 1);

        return redirect()->route('cart.index')->with('success', 'تمت إضافة الباقة إلى العربة بنجاح');
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->cartService->updateQuantity($request->item_id, $request->quantity);

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

    public function setDuration(Request $request)
    {
        $request->validate([
            'duration_months' => 'required|integer|in:3,6',
        ]);

        $cart = $this->cartService->setDuration((int) $request->duration_months);

        return response()->json($this->cartSummary($cart));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $code   = strtoupper(trim($request->coupon_code ?? ''));
        $cart   = $this->cartService->applyCoupon($code ?: null);
        $coupon = $code ? Coupon::findActive($code) : null;

        return response()->json([
            ...$this->cartSummary($cart),
            'coupon_valid'   => $code && $coupon !== null,
            'coupon_invalid' => $code && $coupon === null,
        ]);
    }

    private function cartSummary(\App\Models\Cart $cart): array
    {
        $dec = $this->currencyService->decimals($cart->currency);

        return [
            'count'           => $cart->items->count(),
            'currency'        => $cart->currency,
            'currency_meta'   => $this->currencyService->jsConfig($cart->currency),
            'duration_months' => (int) $cart->duration_months,
            'subtotal'        => number_format((float) $cart->subtotal,        $dec),
            'coupon_discount' => number_format((float) $cart->coupon_discount, $dec),
            'total'           => number_format((float) $cart->total,           $dec),
            'has_coupon'      => (bool) $cart->coupon_code,
            'items'           => $cart->items->map(fn ($item) => [
                'id'          => $item->id,
                'plan_id'     => $item->plan_id,
                'quantity'    => $item->quantity,
                'final_price' => number_format((float) $item->final_price, $dec),
                'unit_price'  => number_format((float) $item->price, $dec),
            ]),
        ];
    }
}
