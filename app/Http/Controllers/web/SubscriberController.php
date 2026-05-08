<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\MemberEvaluation;
use App\Models\Program;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WeightLog;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        $coach = auth()->user();

        $subscribers = Subscription::with(['user', 'user.profile', 'plan'])
            ->where('status', 'active')
            ->latest()
            ->get();

        $userIds = $subscribers->pluck('user_id');
        $today   = now()->toDateString();

        $todayAttendances = Attendance::where('attended_at', $today)
            ->whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');

        $latestEvals = MemberEvaluation::whereIn('user_id', $userIds)
            ->orderBy('evaluated_at', 'desc')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($evals) => $evals->first());

        // ── Determine rest-day users (day_order: 1=Sun … 7=Sat) ──
        $todayDayOrder = now()->dayOfWeek + 1;

        $programs = Program::whereIn('user_id', $userIds)
            ->with('days')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->sortByDesc('created_at')->first());

        $restDayUserIds = $userIds->filter(function ($uid) use ($programs, $todayDayOrder) {
            $prog = $programs[$uid] ?? null;
            if (! $prog) return false;
            $day = $prog->days->firstWhere('day_order', $todayDayOrder);
            return $day && $day->type === 'rest';
        })->values();

        $presentToday  = $todayAttendances->whereIn('status', ['present', 'late'])->count();
        $absentToday   = $todayAttendances->where('status', 'absent')->count();
        $notMarked     = $subscribers
            ->filter(fn ($sub) => ! $restDayUserIds->contains($sub->user_id))
            ->filter(fn ($sub) => ! $todayAttendances->has($sub->user_id))
            ->count();
        $evalsThisWeek = MemberEvaluation::where('coach_id', $coach->id)
            ->whereBetween('evaluated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return view('app.web.subscribers', compact(
            'coach',
            'subscribers',
            'todayAttendances',
            'latestEvals',
            'restDayUserIds',
            'presentToday',
            'absentToday',
            'notMarked',
            'evalsThisWeek'
        ));
    }

    public function show(int $userId)
    {
        $coach  = auth()->user();
        $member = User::with('profile')->findOrFail($userId);

        $subscription = Subscription::with('plan')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();

        $program     = Program::where('user_id', $userId)->with('days')->latest()->first();
        $programDays = [];
        if ($program) {
            foreach ($program->days as $day) {
                $programDays[(int) $day->day_order] = $day->type;
            }
        }

        $todayDayOrder = now()->dayOfWeek + 1;
        $todayIsRest   = ($programDays[$todayDayOrder] ?? 'workout') === 'rest';

        // All attendance records (for stats + recent list)
        $allAttendances = Attendance::where('user_id', $userId)
            ->orderByDesc('attended_at')
            ->get();

        $presentCount   = $allAttendances->where('status', 'present')->count();
        $lateCount      = $allAttendances->where('status', 'late')->count();
        $absentCount    = $allAttendances->where('status', 'absent')->count();
        $markedCount    = $allAttendances->count();
        $attendanceRate = $markedCount > 0
            ? round((($presentCount + $lateCount) / $markedCount) * 100)
            : 0;

        $todayAttendance = $allAttendances->first(
            fn ($a) => $a->attended_at->toDateString() === now()->toDateString()
        );

        // 90-day heatmap grid
        $attMap90 = $allAttendances
            ->filter(fn ($a) => $a->attended_at->gte(now()->subDays(89)->startOfDay()))
            ->keyBy(fn ($a) => $a->attended_at->toDateString());

        // Pad left so the grid starts on Sunday
        $start90   = now()->subDays(89)->startOfDay();
        $padBefore = $start90->dayOfWeek; // 0=Sun…6=Sat

        $grid90 = array_fill(0, $padBefore, ['date' => '', 'day' => '', 'status' => 'empty']);
        for ($i = 89; $i >= 0; $i--) {
            $date    = now()->subDays($i)->startOfDay();
            $dateStr = $date->toDateString();
            $dayOrd  = $date->dayOfWeek + 1;
            $isRest  = ($programDays[$dayOrd] ?? 'workout') === 'rest';
            $att     = $attMap90[$dateStr] ?? null;

            if ($isRest) {
                $status = 'rest';
            } elseif ($date->isToday() && ! $att) {
                $status = 'today';
            } elseif (! $att) {
                $status = 'none';
            } else {
                $status = $att->status;
            }

            $grid90[] = [
                'date'   => $date->isoFormat('D MMM Y'),
                'day'    => $date->isoFormat('dddd'),
                'status' => $status,
            ];
        }

        $recentAttendance = $allAttendances->take(30);

        $evaluations = MemberEvaluation::where('user_id', $userId)
            ->orderByDesc('evaluated_at')
            ->get();

        $weightLogs = WeightLog::where('user_id', $userId)
            ->orderBy('logged_at')
            ->get();

        return view('app.web.subscriber_profile', compact(
            'coach', 'member', 'subscription', 'program',
            'todayIsRest', 'todayAttendance',
            'presentCount', 'lateCount', 'absentCount', 'markedCount', 'attendanceRate',
            'grid90', 'recentAttendance',
            'evaluations', 'weightLogs'
        ));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'status'      => 'required|in:present,late,absent',
            'attended_at' => 'nullable|date',
            'notes'       => 'nullable|string|max:500',
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'user_id'     => $validated['user_id'],
                'attended_at' => $validated['attended_at'] ?? now()->toDateString(),
            ],
            [
                'coach_id' => auth()->id(),
                'status'   => $validated['status'],
                'notes'    => $validated['notes'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'status' => $attendance->status]);
    }

    public function storeEvaluation(Request $request)
    {
        $validated = $request->validate([
            'user_id'             => 'required|exists:users,id',
            'weight'              => 'required|numeric|min:20|max:400',
            'height'              => 'nullable|numeric|min:50|max:280',
            'body_fat_percentage' => 'nullable|numeric|min:1|max:70',
            'muscle_mass'         => 'nullable|numeric|min:10|max:200',
            'fitness_level'       => 'required|in:beginner,intermediate,advanced',
            'notes'               => 'nullable|string|max:1000',
            'evaluated_at'        => 'nullable|date',
        ]);

        $validated['coach_id']    = auth()->id();
        $validated['evaluated_at'] = $validated['evaluated_at'] ?? now()->toDateString();

        $eval = MemberEvaluation::create($validated);

        UserProfile::where('user_id', $validated['user_id'])
            ->update(['current_weight' => $validated['weight']]);

        WeightLog::create([
            'user_id'   => $validated['user_id'],
            'weight'    => $validated['weight'],
            'logged_at' => $validated['evaluated_at'],
        ]);

        return response()->json([
            'success' => true,
            'eval'    => [
                'id'            => $eval->id,
                'weight'        => $eval->weight,
                'fitness_level' => $eval->fitness_level,
                'evaluated_at'  => $eval->evaluated_at->format('Y-m-d'),
            ],
        ]);
    }
}
