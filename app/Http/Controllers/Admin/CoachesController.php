<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberEvaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CoachesController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'coach')
            ->withCount('evaluationsAsCoach');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        $coaches = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::where('role', 'coach')->count(),
            'active'   => User::where('role', 'coach')->where('status', 'active')->count(),
            'inactive' => User::where('role', 'coach')->where('status', 'inactive')->count(),
            'banned'   => User::where('role', 'coach')->where('status', 'banned')->count(),
        ];

        return view('app.admin.coaches.index', compact('coaches', 'stats'));
    }

    public function show(User $coach)
    {
        abort_if($coach->role !== 'coach', 404);

        $evaluations = MemberEvaluation::where('coach_id', $coach->id)
            ->with('member')
            ->latest('evaluated_at')
            ->paginate(10);

        $evalStats = [
            'total'       => MemberEvaluation::where('coach_id', $coach->id)->count(),
            'this_month'  => MemberEvaluation::where('coach_id', $coach->id)
                                ->whereMonth('evaluated_at', now()->month)
                                ->whereYear('evaluated_at', now()->year)
                                ->count(),
            'last_eval'   => MemberEvaluation::where('coach_id', $coach->id)
                                ->latest('evaluated_at')
                                ->value('evaluated_at'),
            'members'     => MemberEvaluation::where('coach_id', $coach->id)
                                ->distinct('user_id')
                                ->count('user_id'),
        ];

        return view('app.admin.coaches.show', compact('coach', 'evaluations', 'evalStats'));
    }

    public function update(Request $request, User $coach)
    {
        abort_if($coach->role !== 'coach', 404);

        $rules = [
            'name'   => 'required|string|min:2|max:150',
            'phone'  => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,banned',
        ];

        $messages = [
            'name.required' => 'الاسم مطلوب',
            'name.min'      => 'الاسم يجب أن يكون حرفين على الأقل',
            'status.required' => 'الحالة مطلوبة',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::min(8)];
            $messages['password.confirmed'] = 'تأكيد كلمة المرور غير متطابق';
            $messages['password.min']       = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        }

        $request->validate($rules, $messages);

        $data = $request->only('name', 'phone', 'status');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $coach->update($data);

        return back()->with('success', 'تم تحديث بيانات المدرب بنجاح');
    }

    public function updateStatus(User $coach)
    {
        abort_if($coach->role !== 'coach', 404);

        $newStatus = $coach->status === 'banned' ? 'active' : 'banned';
        $coach->update(['status' => $newStatus]);

        $msg = $newStatus === 'banned' ? 'تم حظر المدرب بنجاح' : 'تم رفع الحظر عن المدرب';

        return back()->with('success', $msg);
    }

    public function destroy(User $coach)
    {
        abort_if($coach->role !== 'coach', 404);

        $coach->delete();

        return redirect()->route('admin.coaches.index')->with('success', 'تم حذف المدرب بنجاح');
    }
}
