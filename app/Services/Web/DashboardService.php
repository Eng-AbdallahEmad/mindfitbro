<?php

namespace App\Services\Web;

use App\Models\Subscription;
use App\Models\UserWorkoutLog;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;

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
        $program = Program::where('user_id', $user->id)->latest()->first();

        // ── الأوزان ──────────────────────────────────────────
        $startWeight   = $profile?->start_weight   ?? 0;
        $currentWeight = $profile?->current_weight ?? $startWeight;
        $goalWeight    = $profile?->goal_weight     ?? 0;

        // ── تقدم الرحلة ──────────────────────────────────────
        $totalWeeks = $program?->total_weeks ?? 1;
        $weeksDone  = 0;

        if ($subscription->start_date) {
            $daysPassed = (int) $subscription->start_date->startOfDay()->diffInDays(now()->startOfDay());
            $weeksDone  = (int) floor($daysPassed / 7);
            $weeksDone  = min($weeksDone, $totalWeeks);
        }

        $pct = $totalWeeks > 0
            ? min(100, (int) round(($weeksDone / $totalWeeks) * 100))
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

    // ── حساب الستريك ─────────────────────────────────────────
    private function calcStreak(int $userId): int
    {
        $streak = 0;
        $date   = now()->startOfDay();

        while (true) {
            $logged = UserWorkoutLog::where('user_id', $userId)
                ->whereDate('date', $date)
                ->where('status', 'done')
                ->exists();

            if (! $logged) break;

            $streak++;
            $date->subDay();
        }

        return $streak;
    }

    // ── أيام الأسبوع الحالي ──────────────────────────────────
    private function getWeekDays(int $userId, $program): array
    {
        $labels = ['أح', 'إث', 'ث', 'أر', 'خ', 'ج', 'س'];
        $today  = now()->startOfDay();
        $days   = [];

        // أيام الأسبوع من الأحد للسبت
        $weekStart = now()->startOfWeek(\Carbon\Carbon::SUNDAY);

        // جيب أيام البرنامج بشكل مضمون
        $programDays = [];

        if ($program) {
            foreach ($program->days()->orderBy('day_order')->get() as $day) {
                $programDays[(int) $day->day_order] = $day->type;
            }
        }

        for ($i = 0; $i < 7; $i++) {
            $date     = $weekStart->copy()->addDays($i);
            $dayOrder = $i + 1; // 1=أحد ... 7=سبت
            $isRest   = ($programDays[$dayOrder] ?? 'workout') === 'rest';

            if ($date->gt($today)) {
                $status = $isRest ? 'rest' : 'upcoming';
            } elseif ($date->eq($today)) {
                $status = $isRest ? 'rest' : 'today';
            } else {
                if ($isRest) {
                    $status = 'rest';
                } else {
                    $logged = UserWorkoutLog::where('user_id', $userId)
                        ->whereDate('date', $date)
                        ->where('status', 'done')
                        ->exists();

                    $status = $logged ? 'done' : 'upcoming';
                }
            }

            $days[] = [
                'label'  => $labels[$i],
                'status' => $status,
            ];
        }

        return $days;
    }
}