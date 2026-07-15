<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CoachOtpMail;
use App\Models\Attendance;
use App\Models\MemberEvaluation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class MembersController extends Controller
{
    public function create()
    {
        return view('app.admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|min:2|max:150',
            'username'         => 'required|string|min:3|max:50|unique:users,username|alpha_dash',
            'email'            => 'required|email|unique:users,email|max:255',
            'phone'            => 'nullable|string|max:20',
            'gender'           => 'required|in:male,female',
            'role'             => 'required|in:user,coach',
            'status'           => 'required|in:active,inactive,banned',
            'password'         => ['required', 'confirmed', Password::min(8)],
            'date_of_birth'    => 'nullable|date|before:today',
            'height'           => 'nullable|numeric|min:50|max:250',
            'start_weight'     => 'nullable|numeric|min:20|max:500',
            'current_weight'   => 'nullable|numeric|min:20|max:500',
            'goal_weight'      => 'nullable|numeric|min:20|max:500',
        ], [
            'name.required'          => 'الاسم مطلوب',
            'name.min'               => 'الاسم يجب أن يكون حرفين على الأقل',
            'username.required'      => 'اسم المستخدم مطلوب',
            'username.unique'        => 'اسم المستخدم مستخدم بالفعل',
            'username.alpha_dash'    => 'اسم المستخدم يقبل أحرف وأرقام وشرطات فقط',
            'email.required'         => 'البريد الإلكتروني مطلوب',
            'email.email'            => 'البريد الإلكتروني غير صحيح',
            'email.unique'           => 'هذا البريد الإلكتروني مستخدم بالفعل (كمدرب أو كمتدرب)',
            'gender.required'        => 'الجنس مطلوب',
            'role.required'          => 'الدور مطلوب',
            'status.required'        => 'الحالة مطلوبة',
            'password.required'      => 'كلمة المرور مطلوبة',
            'password.confirmed'     => 'تأكيد كلمة المرور غير متطابق',
            'password.min'           => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'date_of_birth.before'   => 'تاريخ الميلاد يجب أن يكون في الماضي',
            'height.numeric'         => 'الطول يجب أن يكون رقماً',
            'start_weight.numeric'   => 'الوزن يجب أن يكون رقماً',
            'current_weight.numeric' => 'الوزن الحالي يجب أن يكون رقماً',
            'goal_weight.numeric'    => 'الوزن المستهدف يجب أن يكون رقماً',
        ]);

        // ── Coach: require OTP email verification ────────────────
        if ($validated['role'] === 'coach') {
            $otp   = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(48);

            Cache::put('coach_otp_' . $token, [
                'otp'      => $otp,
                'attempts' => 0,
                'data'     => array_merge(
                    array_intersect_key($validated, array_flip([
                        'name','username','email','phone','gender','status',
                    ])),
                    ['password' => $request->password]
                ),
            ], now()->addMinutes(15));

            try {
                Mail::to($validated['email'])->send(new CoachOtpMail($otp, $validated['name']));
            } catch (\Throwable $e) {
                Log::error('CoachOtpMail failed', ['email' => $validated['email'], 'err' => $e->getMessage()]);
                Cache::forget('coach_otp_' . $token);
                return back()->withInput()
                    ->with('error', 'فشل إرسال رمز التحقق. تحقق من صحة البريد الإلكتروني وحاول مجدداً.');
            }

            return redirect()->route('admin.members.verify-otp', $token)
                ->with('otp_sent', 'تم إرسال رمز التحقق إلى ' . $validated['email'] . ' — اطلبه من الكوتش لإتمام الإنشاء.');
        }

        // ── Member (user): create immediately ───────────────────
        $user = $this->createUser($validated);

        $profileFields = array_filter([
            'date_of_birth'  => $validated['date_of_birth']  ?? null,
            'height'         => $validated['height']          ?? null,
            'start_weight'   => $validated['start_weight']    ?? null,
            'current_weight' => $validated['current_weight']  ?? null,
            'goal_weight'    => $validated['goal_weight']     ?? null,
        ]);

        if (!empty($profileFields)) {
            $user->profile()->create($profileFields);
        }

        return redirect()->route('admin.members.show', $user)
            ->with('success', 'تم إنشاء العضو بنجاح');
    }

    // ── OTP: show verification page ──────────────────────────────
    public function showVerifyOtp(string $token)
    {
        $cached = Cache::get('coach_otp_' . $token);

        if (! $cached) {
            return redirect()->route('admin.members.create')
                ->with('error', 'انتهت صلاحية رمز التحقق أو الرابط غير صحيح. يرجى إعادة إنشاء الحساب.');
        }

        return view('app.admin.members.verify_otp', [
            'token'        => $token,
            'email'        => $cached['data']['email'],
            'name'         => $cached['data']['name'],
            'attemptsLeft' => 3 - $cached['attempts'],
        ]);
    }

    // ── OTP: verify code and create coach ────────────────────────
    public function verifyOtp(Request $request, string $token)
    {
        $request->validate(
            ['otp' => 'required|digits:6'],
            ['otp.required' => 'رمز التحقق مطلوب', 'otp.digits' => 'رمز التحقق يجب أن يكون 6 أرقام']
        );

        $cached = Cache::get('coach_otp_' . $token);

        if (! $cached) {
            return redirect()->route('admin.members.create')
                ->with('error', 'انتهت صلاحية رمز التحقق. يرجى إعادة إنشاء الحساب.');
        }

        if ($cached['attempts'] >= 3) {
            Cache::forget('coach_otp_' . $token);
            return redirect()->route('admin.members.create')
                ->with('error', 'تجاوزت الحد الأقصى للمحاولات (3). يرجى إعادة إنشاء الحساب.');
        }

        if ($request->otp !== $cached['otp']) {
            $cached['attempts']++;
            $remaining = 3 - $cached['attempts'];

            if ($remaining <= 0) {
                Cache::forget('coach_otp_' . $token);
                return redirect()->route('admin.members.create')
                    ->with('error', 'تجاوزت الحد الأقصى للمحاولات. يرجى إعادة إنشاء الحساب.');
            }

            Cache::put('coach_otp_' . $token, $cached, now()->addMinutes(15));

            return redirect()->route('admin.members.verify-otp', $token)
                ->withErrors(['otp' => 'رمز التحقق غير صحيح. متبقي ' . $remaining . ' محاولات.']);
        }

        // ── OTP correct: create coach account ──
        Cache::forget('coach_otp_' . $token);
        $data = $cached['data'];

        $user = User::create([
            'name'                 => $data['name'],
            'username'             => $data['username'],
            'email'                => $data['email'],
            'phone'                => $data['phone'] ?? null,
            'gender'               => $data['gender'],
            'role'                 => 'coach',
            'status'               => $data['status'],
            'password'             => Hash::make($data['password']),
            'email_verified_at'    => now(),
            'terms_accepted_at'    => now(),
            'profile_completed_at' => now(),
        ]);

        return redirect()->route('admin.coaches.show', $user)
            ->with('success', 'تم إنشاء حساب الكوتش بنجاح — تم التحقق من البريد الإلكتروني');
    }

    // ── OTP: resend new code ─────────────────────────────────────
    public function resendOtp(string $token)
    {
        $cached = Cache::get('coach_otp_' . $token);

        if (! $cached) {
            return redirect()->route('admin.members.create')
                ->with('error', 'انتهت صلاحية الجلسة. يرجى إعادة إنشاء الحساب.');
        }

        $otp              = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cached['otp']      = $otp;
        $cached['attempts'] = 0;

        Cache::put('coach_otp_' . $token, $cached, now()->addMinutes(15));

        try {
            Mail::to($cached['data']['email'])->send(new CoachOtpMail($otp, $cached['data']['name']));
        } catch (\Throwable $e) {
            Log::error('CoachOtpMail resend failed', ['email' => $cached['data']['email'], 'err' => $e->getMessage()]);
            return redirect()->route('admin.members.verify-otp', $token)
                ->with('error', 'فشل إعادة الإرسال. تحقق من البريد الإلكتروني.');
        }

        return redirect()->route('admin.members.verify-otp', $token)
            ->with('otp_sent', 'تم إرسال رمز تحقق جديد إلى ' . $cached['data']['email']);
    }

    // ── Internal: create a user record ──────────────────────────
    private function createUser(array $data): User
    {
        return User::create([
            'name'                 => $data['name'],
            'username'             => $data['username'],
            'email'                => $data['email'],
            'phone'                => $data['phone'] ?? null,
            'gender'               => $data['gender'],
            'role'                 => $data['role'],
            'status'               => $data['status'],
            'password'             => Hash::make($data['password']),
            'terms_accepted_at'    => now(),
            'profile_completed_at' => now(),
        ]);
    }

    public function show(User $member)
    {
        abort_if($member->role !== 'user', 404);

        $member->load([
            'profile',
            'subscriptions.plan',
            'meetingBookings' => fn ($q) => $q->latest()->limit(5),
            'weightLogs'      => fn ($q) => $q->latest('logged_at')->limit(12),
        ]);

        $evaluations = MemberEvaluation::where('user_id', $member->id)
            ->with('coach')
            ->latest('evaluated_at')
            ->limit(5)
            ->get();

        $attendanceStats = Attendance::where('user_id', $member->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'present') as present,
                SUM(status = 'late')    as late,
                SUM(status = 'absent')  as absent
            ")
            ->first();

        $recentAttendance = Attendance::where('user_id', $member->id)
            ->latest('attended_at')
            ->limit(10)
            ->get();

        return view('app.admin.members.show', compact(
            'member', 'evaluations', 'attendanceStats', 'recentAttendance'
        ));
    }

    public function update(Request $request, User $member)
    {
        abort_if($member->role !== 'user', 404);

        $request->validate([
            'name'   => 'required|string|min:2|max:150',
            'phone'  => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,banned',
        ], [
            'name.required' => 'الاسم مطلوب',
            'name.min'      => 'الاسم يجب أن يكون حرفين على الأقل',
            'status.required' => 'الحالة مطلوبة',
        ]);

        $member->update($request->only('name', 'phone', 'status'));

        return back()->with('success', 'تم تحديث بيانات العضو بنجاح');
    }

    public function updateStatus(User $member)
    {
        abort_if($member->role !== 'user', 404);

        $newStatus = $member->status === 'banned' ? 'active' : 'banned';
        $member->update(['status' => $newStatus]);

        $msg = $newStatus === 'banned' ? 'تم حظر العضو بنجاح' : 'تم رفع الحظر عن العضو';

        return back()->with('success', $msg);
    }

    public function destroy(User $member): \Illuminate\Http\RedirectResponse
    {
        abort_if($member->role !== 'user', 404);

        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'تم حذف العضو بنجاح');
    }

    public function index(Request $request)
    {
        $query = User::where('role', 'user');

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

        $members = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::where('role', 'user')->count(),
            'active'   => User::where('role', 'user')->where('status', 'active')->count(),
            'inactive' => User::where('role', 'user')->where('status', 'inactive')->count(),
            'banned'   => User::where('role', 'user')->where('status', 'banned')->count(),
        ];

        return view('app.admin.members.index', compact('members', 'stats'));
    }
}
