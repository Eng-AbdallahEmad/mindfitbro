<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuestAccountController extends Controller
{
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
                'name'                 => $request->name,
                'username'             => $request->username,
                'phone'                => $request->phone,
                'email'                => $request->email,
                'gender'               => $request->gender,
                'password'             => Hash::make($request->password),
                'terms_accepted_at'    => now(),
                'profile_completed_at' => now(),
            ]);

            $subscription->update([
                'user_id'     => $user->id,
                'guest_token' => null,
            ]);

            Auth::login($user);
        });

        return redirect()->route('booking.show', $subscription->id)
            ->with('success', 'تم إنشاء حسابك ورُبط بباقتك — حدد موعد جلستك الأولى');
    }
}
