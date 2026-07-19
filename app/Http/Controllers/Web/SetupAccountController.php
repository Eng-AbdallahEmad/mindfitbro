<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupAccountController extends Controller
{
    public function show(string $token)
    {
        $subscription = Subscription::where('guest_token', $token)
            ->whereNotNull('user_id')
            ->with('user')
            ->firstOrFail();

        if ($subscription->user?->profile_completed_at) {
            return redirect()->route('dashboard')
                ->with('success', 'حسابك مكتمل بالفعل، يمكنك تسجيل الدخول.');
        }

        return view('auth.web.setup_account', compact('subscription', 'token'));
    }

    public function store(Request $request, string $token)
    {
        $subscription = Subscription::where('guest_token', $token)
            ->whereNotNull('user_id')
            ->with('user')
            ->firstOrFail();

        $user = $subscription->user;

        if ($user->profile_completed_at) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'username' => [
                'required', 'string', 'min:3', 'max:50',
                'unique:users,username,' . $user->id,
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'password' => 'required|string|min:8|confirmed',
            'phone'    => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{7,20}$/'],
            'gender'   => 'required|in:male,female',
        ], [
            'username.required' => 'اسم المستخدم مطلوب',
            'username.min'      => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل',
            'username.max'      => 'اسم المستخدم يجب أن لا يتجاوز 50 حرفاً',
            'username.unique'   => 'اسم المستخدم مستخدم بالفعل',
            'username.regex'    => 'اسم المستخدم يقبل فقط حروف إنجليزية وأرقام و (_)',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min'      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'=> 'كلمتا المرور غير متطابقتين',
            'phone.required'    => 'رقم الهاتف مطلوب',
            'phone.regex'       => 'صيغة رقم الهاتف غير صحيحة',
            'gender.required'   => 'الجنس مطلوب',
        ]);

        DB::transaction(function () use ($request, $user, $subscription) {
            $user->update([
                'username'             => $request->username,
                'password'             => Hash::make($request->password),
                'phone'                => $request->phone,
                'gender'               => $request->gender,
                'profile_completed_at' => now(),  // set BEFORE login to pass middleware
            ]);

            $subscription->update(['guest_token' => null]);

            Auth::login($user);
        });

        return redirect()->route('dashboard')
            ->with('success', 'تم إعداد حسابك بنجاح! مرحباً بك في MindFitBro.');
    }
}
