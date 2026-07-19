<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return view('app.web.complete_profile');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|min:3|max:50|unique:users,username,' . $user->id,
            'phone'    => 'required|string|max:20',
            'gender'   => 'required|in:male,female',
        ], [
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique'   => 'اسم المستخدم مستخدم بالفعل',
            'phone.required'    => 'رقم الهاتف مطلوب',
            'gender.required'   => 'الجنس مطلوب',
        ]);

        $user->update([
            'username'             => $request->username,
            'phone'                => $request->phone,
            'gender'               => $request->gender,
            'profile_completed_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم إكمال ملفك الشخصي بنجاح!');
    }
}
