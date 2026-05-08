<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\PurchaseConfirmationMail;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Web\CartService;
use App\Services\Web\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService     $cartService,
        private CheckoutService $checkoutService,
    ) {}

    // ── Process Checkout ─────────────────────────────────────────
    public function process(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'العربة فاضية');
        }

        // ── Guest Checkout ────────────────────────────────────────
        if (! Auth::check()) {
            $request->validate([
                'guest_name'  => 'required|string|min:2|max:150',
                'guest_email' => 'required|email|max:255',
            ], [
                'guest_name.required'  => 'الاسم الكامل مطلوب',
                'guest_name.min'       => 'الاسم يجب أن يكون حرفين على الأقل',
                'guest_email.required' => 'البريد الإلكتروني مطلوب',
                'guest_email.email'    => 'صيغة البريد الإلكتروني غير صحيحة',
            ]);

            // ── 1. Create subscription (must succeed) ────────────
            try {
                $subscription = $this->checkoutService->checkout(
                    $cart,
                    guest: true,
                    guestName: $request->guest_name,
                    guestEmail: $request->guest_email
                );
            } catch (\Exception $e) {
                return redirect()->route('cart.index')
                    ->with('error', 'حصل خطأ أثناء إنشاء الطلب: ' . $e->getMessage());
            }

            session([
                'guest_checkout_id'    => $subscription->id,
                'guest_checkout_token' => $subscription->guest_token,
            ]);

            // ── 2. Send email (non-blocking — failure won't stop checkout) ──
            try {
                Mail::to($subscription->guest_email)
                    ->send(new PurchaseConfirmationMail($subscription));
            } catch (\Throwable $e) {
                \Log::error('Purchase confirmation mail failed', [
                    'subscription_id' => $subscription->id,
                    'guest_email'     => $subscription->guest_email,
                    'error'           => $e->getMessage(),
                ]);
            }

            return redirect()->route('checkout.success', $subscription->id);
        }

        // ── Authenticated Checkout ────────────────────────────────
        try {
            $subscription = $this->checkoutService->checkout($cart);

        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'حصل خطأ: ' . $e->getMessage());
        }

        return redirect()->route('checkout.success', $subscription->id);
    }

    // ── Success Page ─────────────────────────────────────────────
    public function success(int $subscriptionId)
    {
        if (Auth::check()) {
            $subscription = Subscription::where('user_id', Auth::id())
                ->findOrFail($subscriptionId);
        } else {
            // Guest: verify via session
            abort_unless(
                session('guest_checkout_id') === $subscriptionId,
                403
            );
            $subscription = Subscription::findOrFail($subscriptionId);
        }

        return view('app.web.checkout-success', compact('subscription'));
    }

    // ── Complete Account (from email link) ───────────────────────
    public function completeAccount(string $token)
    {
        $subscription = Subscription::where('guest_token', $token)->firstOrFail();

        if ($subscription->user_id) {
            return redirect()->route('dashboard')
                ->with('success', 'باقتك مرتبطة بحسابك بالفعل!');
        }

        return view('auth.web.complete_account', compact('subscription', 'token'));
    }

    public function storeCompleteAccount(Request $request, string $token)
    {
        $subscription = Subscription::where('guest_token', $token)->firstOrFail();

        if ($subscription->user_id) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name'     => 'required|string|min:2|max:150',
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'phone'    => 'required|string|max:20',
            'email'    => 'required|email|unique:users,email',
            'gender'   => 'required|in:male,female',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'     => 'الاسم مطلوب',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique'   => 'اسم المستخدم مستخدم بالفعل',
            'phone.required'    => 'رقم الهاتف مطلوب',
            'email.required'    => 'البريد الإلكتروني مطلوب',
            'email.email'       => 'البريد الإلكتروني غير صحيح',
            'email.unique'      => 'البريد الإلكتروني مستخدم بالفعل',
            'gender.required'   => 'الجنس مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed'=> 'كلمتا السر غير متطابقتين',
        ]);

        DB::transaction(function () use ($request, $subscription) {
            $user = User::create([
                'name'               => $request->name,
                'username'           => $request->username,
                'phone'              => $request->phone,
                'email'              => $request->email,
                'gender'             => $request->gender,
                'password'           => Hash::make($request->password),
                'terms_accepted_at'  => now(),
            ]);

            $subscription->update([
                'user_id'     => $user->id,
                'guest_token' => null,
            ]);

            Auth::login($user);
        });

        return redirect()->route('booking.show', $subscription->id)
            ->with('success', 'تم إنشاء حسابك ورُبط بباقتك 🎉 — حدد موعد جلستك الأولى');
    }
}
