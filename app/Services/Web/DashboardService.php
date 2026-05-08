<?php

namespace App\Services\Web;

use App\Models\Attendance;
use App\Models\Program;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSubscription()
    {
        if (! Auth::check()) {
            return null;
        }

        return Subscription::with([
            'plan',
            'meetingBookings' => function ($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'completed'])
                  ->latest()
                  ->limit(1);
            },
        ])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['active', 'waiting'])
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->latest('created_at')
            ->first();
    }

    public function getProgress(Subscription $subscription): array
    {
        $user    = Auth::user();
        $profile = $user->profile;
        $program = Program::where('user_id', $user->id)->with('days')->latest()->first();

        // ── الأوزان ──────────────────────────────────────────
        $startWeight   = $profile?->start_weight   ?? 0;
        $currentWeight = $profile?->current_weight ?? $startWeight;
        $goalWeight    = $profile?->goal_weight     ?? 0;

        // ── تقدم الرحلة بناءً على الحضور الفعلي ─────────────
        $totalWeeks = $program?->total_weeks ?? 1;

        $workoutDaysPerWeek = 0;
        if ($program) {
            foreach ($program->days as $day) {
                if ($day->type !== 'rest') {
                    $workoutDaysPerWeek++;
                }
            }
        }
        $workoutDaysPerWeek = max(1, $workoutDaysPerWeek);
        $totalWorkoutDays   = $totalWeeks * $workoutDaysPerWeek;

        // أيام التمرين فقط (day_order: 1=أحد … 7=سبت = DAYOFWEEK() في MySQL)
        $workoutDayOrders = $program
            ? $program->days->where('type', '!=', 'rest')->pluck('day_order')->toArray()
            : [];

        $attendedQuery = Attendance::where('user_id', $user->id)
            ->whereIn('status', ['present', 'late']);

        if (! empty($workoutDayOrders)) {
            $attendedQuery->whereIn(DB::raw('DAYOFWEEK(attended_at)'), $workoutDayOrders);
        }

        $attendedDays = min($attendedQuery->count(), $totalWorkoutDays);

        $weeksDone = min((int) floor($attendedDays / $workoutDaysPerWeek), $totalWeeks);

        $pct = $totalWorkoutDays > 0
            ? min(100, (int) round(($attendedDays / $totalWorkoutDays) * 100))
            : 0;

        // ── الستريك (أيام تدريب متتالية) ─────────────────────
        $streak = $this->calcStreak($user->id);

        // ── أيام الأسبوع الحالي ───────────────────────────────
        $weekDays = $this->getWeekDays($user->id, $program);

        return [
            'startWeight'   => $startWeight,
            'currentWeight' => $currentWeight,
            'goalWeight'    => $goalWeight,
            'weeksDone'     => $weeksDone,
            'totalWeeks'    => $totalWeeks,
            'pct'           => $pct,
            'streak'        => $streak,
            'weekDays'      => $weekDays,
        ];
    }

    // ── حساب الستريك (أيام تمرين متتالية — الراحة لا تكسر الستريك) ──
    private function calcStreak(int $userId): int
    {
        // جيب برنامج المستخدم لتحديد أيام الراحة
        $program     = Program::where('user_id', $userId)->with('days')->latest()->first();
        $programDays = [];
        if ($program) {
            foreach ($program->days as $day) {
                $programDays[(int) $day->day_order] = $day->type;
            }
        }

        // جيب سجل الحضور لآخر سنة دفعة واحدة بدل كويري لكل يوم
        $attendanceMap = Attendance::where('user_id', $userId)
            ->where('attended_at', '>=', now()->subYear()->toDateString())
            ->whereIn('status', ['present', 'late'])
            ->get()
            ->keyBy(fn ($a) => $a->attended_at->toDateString());

        $streak    = 0;
        $date      = now()->startOfDay();
        $skipToday = true; // اليوم ممكن مجاش الجيم لسه — لا تكسر الستريك

        for ($guard = 0; $guard < 365; $guard++) {
            $dayOrder = $date->dayOfWeek + 1; // 1=أحد … 7=سبت
            $isRest   = ($programDays[$dayOrder] ?? 'workout') === 'rest';

            if ($isRest) {
                $date->subDay();
                continue; // أيام الراحة شفافة — متحسبش ومتكسرش
            }

            $attended = isset($attendanceMap[$date->toDateString()]);

            if ($attended) {
                $streak++;
                $skipToday = false;
            } elseif ($skipToday) {
                $skipToday = false; // ابدأ من امبارح لو اليوم لسه متسجلش
            } else {
                break;
            }

            $date->subDay();
        }

        return $streak;
    }

    // ── أيام الأسبوع الحالي (بناءً على سجل الحضور) ──────────
    private function getWeekDays(int $userId, ?Program $program): array
    {
        $labels = app()->getLocale() === 'ar'
            ? ['أح', 'إث', 'ث', 'أر', 'خ', 'ج', 'س']
            : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $today     = now()->startOfDay();
        $weekStart = now()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $weekEnd   = $weekStart->copy()->addDays(6);

        // جيب سجلات الحضور لكل أيام الأسبوع الحالي دفعة واحدة
        $weekAttendances = Attendance::where('user_id', $userId)
            ->whereBetween('attended_at', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get()
            ->keyBy(fn ($a) => $a->attended_at->toDateString());

        // أيام البرنامج (راحة / تمرين)
        $programDays = [];
        if ($program) {
            foreach ($program->days()->orderBy('day_order')->get() as $day) {
                $programDays[(int) $day->day_order] = $day->type;
            }
        }

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date     = $weekStart->copy()->addDays($i);
            $dayOrder = $i + 1; // 1=أحد ... 7=سبت
            $isRest   = ($programDays[$dayOrder] ?? 'workout') === 'rest';
            $dateStr  = $date->toDateString();

            if ($date->gt($today)) {
                $status = $isRest ? 'rest' : 'upcoming';
            } elseif ($date->eq($today)) {
                if ($isRest) {
                    $status = 'rest';
                } else {
                    $att    = $weekAttendances[$dateStr] ?? null;
                    $status = match (true) {
                        $att && in_array($att->status, ['present', 'late']) => 'done',
                        $att && $att->status === 'absent'                   => 'missed',
                        default                                              => 'today',
                    };
                }
            } else {
                if ($isRest) {
                    $status = 'rest';
                } else {
                    $att    = $weekAttendances[$dateStr] ?? null;
                    $status = match (true) {
                        $att === null                              => 'upcoming',
                        in_array($att->status, ['present','late'])=> 'done',
                        default                                    => 'missed',
                    };
                }
            }

            $days[] = ['label' => $labels[$i], 'status' => $status];
        }

        return $days;
    }
}