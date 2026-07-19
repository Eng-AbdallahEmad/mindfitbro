<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CoachRating;
use App\Models\MemberEvaluation;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserWorkoutLog;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class JourneyController extends Controller
{
    // ── Show post-program page ───────────────────────────────────
    public function show(Subscription $subscription)
    {
        abort_if($subscription->user_id !== Auth::id(), 403);
        abort_if($subscription->status !== Subscription::STATUS_EXPIRED, 404);

        $stats = $this->gatherStats($subscription, Auth::user());

        return view('app.web.journey.complete', array_merge(
            ['subscription' => $subscription, 'user' => Auth::user()],
            $stats
        ));
    }

    // ── Submit coach rating ──────────────────────────────────────
    public function rate(Request $request, Subscription $subscription)
    {
        abort_if($subscription->user_id !== Auth::id(), 403);
        abort_if($subscription->status !== Subscription::STATUS_EXPIRED, 422);

        // Guard duplicate
        if (CoachRating::where('subscription_id', $subscription->id)->exists()) {
            return redirect()->route('journey.show', $subscription)
                ->with('info', 'لقد أرسلت تقييمك مسبقاً');
        }

        $data = $request->validate([
            'stars'   => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'stars.required' => 'يرجى اختيار عدد النجوم',
            'stars.min'      => 'الحد الأدنى نجمة واحدة',
            'stars.max'      => 'الحد الأقصى 5 نجوم',
        ]);

        // Resolve coach from subscription period
        $coachId = $this->resolveCoachId($subscription);

        CoachRating::create([
            'subscription_id' => $subscription->id,
            'user_id'         => Auth::id(),
            'coach_id'        => $coachId,
            'stars'           => $data['stars'],
            'comment'         => $data['comment'] ?? null,
        ]);

        return redirect()->route('journey.show', $subscription)
            ->with('success', 'شكراً جزيلاً على تقييمك!');
    }

    // ── Generate PDF report ──────────────────────────────────────
    public function pdf(Subscription $subscription)
    {
        // Owner-only
        abort_if($subscription->user_id !== Auth::id(), 403);
        abort_if($subscription->status !== Subscription::STATUS_EXPIRED, 404);

        $stats = $this->gatherStats($subscription, Auth::user());

        // No data → no PDF
        abort_if(! $stats['hasData'], 404);

        ini_set('memory_limit', '256M');

        try {
            $mpdf = new Mpdf([
                'mode'            => 'utf-8',
                'format'          => 'A4',
                'margin_top'      => 20,
                'margin_right'    => 15,
                'margin_bottom'   => 20,
                'margin_left'     => 15,
                'default_font'    => 'dejavusans',
                'autoScriptToLang'=> true,
                'autoLangToFont'  => true,
            ]);

            $mpdf->SetDirectionality('rtl');
            $mpdf->SetTitle('تقرير الرحلة - MindFitBro');
            $mpdf->SetAuthor('MindFitBro');

            $html = view('app.web.journey.pdf', array_merge(
                ['subscription' => $subscription, 'user' => Auth::user()],
                $stats
            ))->render();

            $mpdf->WriteHTML($html);

            $filename = 'journey-report-' . $subscription->id . '.pdf';

            return response()->streamDownload(
                fn () => print($mpdf->Output('', 'S')),
                $filename,
                ['Content-Type' => 'application/pdf']
            );

        } catch (MpdfException $e) {
            abort(500, 'تعذّر إنشاء التقرير، يرجى المحاولة مرة أخرى.');
        }
    }

    // ── Arabic rendering smoke-test (dev only) ───────────────────
    public function pdfTest()
    {
        abort_unless(app()->environment('local', 'development'), 404);

        ini_set('memory_limit', '256M');

        $mpdf = new Mpdf([
            'mode'            => 'utf-8',
            'format'          => 'A4',
            'default_font'    => 'dejavusans',
            'autoScriptToLang'=> true,
            'autoLangToFont'  => true,
        ]);
        $mpdf->SetDirectionality('rtl');

        $mpdf->WriteHTML('
            <!DOCTYPE html>
            <html dir="rtl" lang="ar">
            <head><meta charset="UTF-8">
            <style>
                body { direction: rtl; font-family: dejavusans, sans-serif; }
                h1   { color: #174DAD; text-align: center; }
                table { width:100%; border-collapse:collapse; margin-top:20px; }
                th, td { border:1px solid #ccc; padding:8px; text-align:right; }
                th { background:#174DAD; color:#fff; }
            </style></head>
            <body>
                <h1>تقرير رحلتك — MindFitBro</h1>
                <p>هذا اختبار للتأكد من عمل الخط العربي بشكل صحيح مع اتصال الحروف.</p>
                <p>الوزن الأولي: <strong>90 كجم</strong> — الوزن النهائي: <strong>82 كجم</strong></p>
                <table>
                    <tr><th>البند</th><th>القيمة</th></tr>
                    <tr><td>الحضور</td><td>24 جلسة</td></tr>
                    <tr><td>التمارين المكتملة</td><td>72%</td></tr>
                    <tr><td>نسبة الدهون</td><td>انخفضت 3%</td></tr>
                </table>
            </body>
            </html>
        ');

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    // ── Private helpers ──────────────────────────────────────────

    private function gatherStats(Subscription $subscription, User $user): array
    {
        $start = $subscription->start_date;
        $end   = $subscription->end_date;

        // Weight logs within subscription period
        $weightLogs  = WeightLog::where('user_id', $user->id)
            ->when($start, fn ($q) => $q->where('logged_at', '>=', $start))
            ->when($end,   fn ($q) => $q->where('logged_at', '<=', $end))
            ->orderBy('logged_at')
            ->get();

        $firstWeight = $weightLogs->first();
        $lastWeight  = $weightLogs->count() > 1 ? $weightLogs->last() : null;
        $weightDelta = ($firstWeight && $lastWeight)
            ? round((float)$lastWeight->weight - (float)$firstWeight->weight, 2)
            : null;

        // Member evaluations within period
        $evaluations = MemberEvaluation::where('user_id', $user->id)
            ->when($start, fn ($q) => $q->where('evaluated_at', '>=', $start))
            ->when($end,   fn ($q) => $q->where('evaluated_at', '<=', $end))
            ->orderBy('evaluated_at')
            ->get();

        $firstEval = $evaluations->first();
        $lastEval  = $evaluations->count() > 1 ? $evaluations->last() : ($evaluations->first());

        // Attendance within period
        $attendances   = Attendance::where('user_id', $user->id)
            ->when($start, fn ($q) => $q->where('attended_at', '>=', $start))
            ->when($end,   fn ($q) => $q->where('attended_at', '<=', $end))
            ->get();

        $attendPresent = $attendances->where('status', 'present')->count();
        $attendLate    = $attendances->where('status', 'late')->count();
        $attendAbsent  = $attendances->where('status', 'absent')->count();
        $attendTotal   = $attendances->count();
        $attendRate    = $attendTotal > 0
            ? round(($attendPresent + $attendLate) / $attendTotal * 100)
            : null;

        // Workout logs within period
        $workoutLogs   = UserWorkoutLog::where('user_id', $user->id)
            ->when($start, fn ($q) => $q->where('date', '>=', $start))
            ->when($end,   fn ($q) => $q->where('date', '<=', $end))
            ->get();

        $workoutDone    = $workoutLogs->where('status', 'done')->count();
        $workoutSkipped = $workoutLogs->where('status', 'skipped')->count();
        $workoutTotal   = $workoutDone + $workoutSkipped;
        $workoutRate    = $workoutTotal > 0
            ? round($workoutDone / $workoutTotal * 100)
            : null;

        // Any data at all?
        $hasData = $weightLogs->isNotEmpty()
            || $evaluations->isNotEmpty()
            || $attendances->isNotEmpty();

        // Coach resolution
        $coachId = $this->resolveCoachId($subscription);
        $coach   = $coachId ? User::find($coachId) : null;

        // Existing rating
        $existingRating = CoachRating::where('subscription_id', $subscription->id)->first();

        // User profile
        $profile = $user->profile;

        return compact(
            'weightLogs', 'firstWeight', 'lastWeight', 'weightDelta',
            'firstEval', 'lastEval', 'evaluations',
            'attendPresent', 'attendLate', 'attendAbsent', 'attendTotal', 'attendRate',
            'workoutDone', 'workoutSkipped', 'workoutTotal', 'workoutRate',
            'hasData', 'coach', 'existingRating', 'profile'
        );
    }

    private function resolveCoachId(Subscription $subscription): ?int
    {
        $start = $subscription->start_date;
        $end   = $subscription->end_date;

        $lastEval = MemberEvaluation::where('user_id', $subscription->user_id)
            ->when($start, fn ($q) => $q->where('evaluated_at', '>=', $start))
            ->when($end,   fn ($q) => $q->where('evaluated_at', '<=', $end))
            ->latest('evaluated_at')
            ->first();

        if ($lastEval) {
            return $lastEval->coach_id;
        }

        $lastAttendance = Attendance::where('user_id', $subscription->user_id)
            ->when($start, fn ($q) => $q->where('attended_at', '>=', $start))
            ->when($end,   fn ($q) => $q->where('attended_at', '<=', $end))
            ->latest('attended_at')
            ->first();

        return $lastAttendance?->coach_id;
    }
}
