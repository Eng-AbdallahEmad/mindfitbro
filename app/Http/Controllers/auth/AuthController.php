<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    // ========================
    // Views
    // ========================
    function showLogin()
    {
        return view('auth.web.login');
    }

    function showRegister()
    {
        return view('auth.web.register');
    }

    // ========================
    // Register
    // ========================
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|min:3|max:150',
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'phone'    => 'required|string|max:20',
            'email'    => 'required|email|unique:users,email',
            'gender'   => 'required|in:male,female',
            'password' => 'required|string|min:6|confirmed',
            'terms'    => 'required',
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
            'terms.required'    => 'يجب الموافقة على الشروط والأحكام',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // إنشاء المستخدم
        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'gender'    => $request->gender,
            'password'  => Hash::make($request->password),
            'terms_accepted_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'تم إنشاء الحساب 🎉');
    }

    // ========================
    // Login
    // ========================
    function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ], [
            'username.required' => 'اسم المستخدم مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        // 🔥 Case Sensitive Check
        $user = User::whereRaw('BINARY username = ?', [$request->username])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'اسم المستخدم غير صحيح',
            ])->withInput();
        }

        // Check Password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'كلمة المرور غير صحيحة',
            ])->withInput();
        }

        // Login
        Auth::login($user, $request->has('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'تم تسجيل الدخول بنجاح 👋');
    }

    // ========================
    // Logout
    // ========================
    function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج 👋');
    }

    // ========================
    // Forgot Password
    // ========================
    function showForgotPassword()
    {
        return view('auth.web.forget');
    }

    function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email'    => 'البريد الإلكتروني غير صحيح',
            'email.exists'   => 'لا يوجد حساب مرتبط بهذا البريد الإلكتروني',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
        }

        return back()->withErrors(['email' => 'حدث خطأ أثناء إرسال الرابط، يرجى المحاولة مرة أخرى']);
    }

    // ========================
    // Reset Password
    // ========================
    function showResetPassword(Request $request, string $token)
    {
        return view('auth.web.reset_password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required'     => 'البريد الإلكتروني مطلوب',
            'email.email'        => 'البريد الإلكتروني غير صحيح',
            'password.required'  => 'كلمة المرور الجديدة مطلوبة',
            'password.min'       => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'كلمتا السر غير متطابقتين',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'تم إعادة تعيين كلمة المرور بنجاح، يمكنك تسجيل الدخول الآن');
        }

        $errors = [
            Password::INVALID_TOKEN => 'رابط الاستعادة غير صالح أو منتهي الصلاحية، اطلب رابطاً جديداً',
            Password::INVALID_USER  => 'لا يوجد حساب مرتبط بهذا البريد الإلكتروني',
        ];

        return back()->withErrors(['email' => $errors[$status] ?? 'حدث خطأ، يرجى المحاولة مرة أخرى']);
    }
}
