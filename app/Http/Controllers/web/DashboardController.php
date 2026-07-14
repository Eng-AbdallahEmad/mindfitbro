<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\MeetingBooking;
use App\Models\MemberEvaluation;
use App\Models\Program;
use App\Models\UserProfile;
use App\Models\WeightLog;
use App\Models\ProgramDay;
use App\Models\Subscription;
use App\Models\Plan;
use App\Mail\MeetingLinkMail;
use App\Services\Web\CoachDashboardService;
use App\Services\Web\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService      $dashboardService,
        private CoachDashboardService $coachDashboardService,
    ) {}

    // ══════════════════════════════════════════════
    // Dashboard Index
    // ══════════════════════════════════════════════

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'coach') {
            $data = $this->coachDashboardService->getData();
            return view('app.web.dashboard', $data);
        }

        // ── 1. Primary query: active / approved / waiting ──────────────────────
        $subscription   = $this->dashboardService->getSubscription();
        $rejectedRecent = false;
        $booking        = null;

        if ($subscription) {
            $dashboardState = $this->dashboardService->resolveState($subscription);
            $booking        = $subscription->meetingBookings->first();
        } else {
            // ── Option B: Fallback queries (in priority order) ─────────────────
            $pendingSub = Subscription::where('user_id', Auth::id())
                ->where('status', Subscription::STATUS_PENDING_REVIEW)
                ->with('plan')
                ->latest()
                ->first();

            if ($pendingSub) {
                $subscription   = $pendingSub;
                $dashboardState = 'pending_review';
            } else {
                $expiredSub = Subscription::where('user_id', Auth::id())
                    ->where('status', Subscription::STATUS_EXPIRED)
                    ->with('plan')
                    ->latest()
                    ->first();

                if ($expiredSub) {
                    $subscription   = $expiredSub;
                    $dashboardState = 'completed';
                } else {
                    $rejectedSub    = Subscription::where('user_id', Auth::id())
                        ->whereIn('status', [Subscription::STATUS_REJECTED, Subscription::STATUS_CANCELLED])
                        ->latest()
                        ->first();
                    $rejectedRecent = $rejectedSub?->status === Subscription::STATUS_REJECTED;
                    $dashboardState = 'no_sub';
                }
            }
        }

        $plan = $subscription?->plan;

        // ── 2. Booking step (meeting_phase only) ───────────────────────────────
        $bookingStep = 1;
        $meetingDone = false;
        if ($booking) {
            $bookingStep = 2;
            $meetingDone = \Carbon\Carbon::parse($booking->meeting_date)->isPast();
        }
        $hasBooking = $booking !== null;

        // ── 3. Subscription timing ─────────────────────────────────────────────
        $daysLeft       = 0;
        $totalDays      = 1;
        $subPct         = 0;
        $daysUntilStart = 0;
        $startDateIso   = null;

        if ($subscription?->end_date) {
            $daysLeft  = (int) now()->startOfDay()
                            ->diffInDays($subscription->end_date->startOfDay(), false);
            $totalDays = $subscription->start_date
                ? max(1, (int) $subscription->start_date->diffInDays($subscription->end_date))
                : 1;
            $daysUsed  = $subscription->start_date
                ? (int) $subscription->start_date->diffInDays(now())
                : 0;
            $subPct    = $totalDays > 0
                ? min(100, (int) round(($daysUsed / $totalDays) * 100))
                : 0;
        }

        if ($dashboardState === 'upcoming' && $subscription?->start_date) {
            $daysUntilStart = (int) now()->startOfDay()
                                 ->diffInDays($subscription->start_date->startOfDay());
            $startDateIso   = $subscription->start_date->toDateString();
        }

        // ── 3b. Auto-set journey_started_at for missed day-one ────────────────
        // When start_date < today and column is still null, set it silently
        if ($dashboardState === 'active' && $subscription && is_null($subscription->journey_started_at)) {
            $subscription->update(['journey_started_at' => now()]);
        }

        // ── 4. Progress data ───────────────────────────────────────────────────
        $progress = [];
        if (in_array($dashboardState, ['active', 'start_ceremony'])) {
            $progress = $this->dashboardService->getProgress($subscription);
        } elseif ($dashboardState === 'completed') {
            $profile  = UserProfile::where('user_id', Auth::id())->first();
            $progress = [
                'startWeight'   => $profile?->start_weight   ?? 0,
                'currentWeight' => $profile?->current_weight ?? 0,
                'goalWeight'    => $profile?->goal_weight     ?? 0,
            ];
        }

        $startWeight   = $progress['startWeight']   ?? 0;
        $currentWeight = $progress['currentWeight'] ?? 0;
        $goalWeight    = $progress['goalWeight']    ?? 0;
        $weeksDone     = $progress['weeksDone']     ?? 0;
        $totalWeeks    = $progress['totalWeeks']    ?? 0;
        $pct           = $progress['pct']           ?? 0;
        $streak        = $progress['streak']        ?? 0;
        $weekDays      = $progress['weekDays']      ?? [];
        $hasProgram    = $progress['hasProgram']    ?? false;

        $wRange     = abs($goalWeight - $startWeight);
        $wDone      = abs($currentWeight - $startWeight);
        $wPct       = $wRange > 0
            ? min(100, (int) round(($wDone / $wRange) * 100))
            : 0;
        $wRemaining = round(abs($goalWeight - $currentWeight), 1);
        $wLosing    = $goalWeight < $startWeight;

        // null = no program yet (active partial shows "program being prepared" placeholder)
        $todayDayStatus = (!$hasProgram || empty($weekDays))
            ? null
            : ($weekDays[now()->dayOfWeek]['status'] ?? 'upcoming');

        // ── 5. Evaluations (active + start_ceremony) ──────────────────────────
        $evaluations = in_array($dashboardState, ['active', 'start_ceremony'])
            ? MemberEvaluation::where('user_id', Auth::id())
                ->with('coach')
                ->orderByDesc('evaluated_at')
                ->get()
            : null;

        // ── 6. Completed: attendance percentage estimate ────────────────────────
        $attendancePct = 0;
        if ($dashboardState === 'completed' && $subscription?->start_date && $subscription?->end_date) {
            $worked     = Attendance::where('user_id', Auth::id())
                ->whereIn('status', ['present', 'late'])
                ->whereBetween('attended_at', [
                    $subscription->start_date->toDateString(),
                    $subscription->end_date->toDateString(),
                ])
                ->count();
            $periodDays = (int) $subscription->start_date->diffInDays($subscription->end_date);
            $estTotal   = (int) round($periodDays * 5 / 7);
            $attendancePct = $estTotal > 0
                ? min(100, (int) round($worked / $estTotal * 100))
                : 0;
        }

        return view('app.web.dashboard', compact(
            'dashboardState', 'subscription', 'plan', 'booking', 'hasBooking',
            'bookingStep', 'meetingDone', 'rejectedRecent',
            'daysLeft', 'totalDays', 'subPct', 'daysUntilStart', 'startDateIso',
            'startWeight', 'currentWeight', 'goalWeight',
            'wRange', 'wDone', 'wPct', 'wRemaining', 'wLosing',
            'weeksDone', 'totalWeeks', 'pct', 'streak', 'weekDays',
            'todayDayStatus', 'evaluations', 'attendancePct', 'progress'
        ));
    }

    // ══════════════════════════════════════════════
    // Start Journey Ceremony (idempotent)
    // ══════════════════════════════════════════════

    public function startJourney(): JsonResponse
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $subscription) {
            return response()->json(['error' => 'No active subscription'], 422);
        }

        // Idempotent: already acknowledged
        if (! is_null($subscription->journey_started_at)) {
            return response()->json(['ok' => true]);
        }

        // Only valid on the actual start day
        if ($subscription->start_date?->toDateString() !== now()->toDateString()) {
            return response()->json(['error' => 'Not start day'], 422);
        }

        $subscription->update(['journey_started_at' => now()]);

        return response()->json(['ok' => true]);
    }

    // ══════════════════════════════════════════════
    // Confirm Booking + Activate Subscription
    // ══════════════════════════════════════════════

    public function confirmBooking(Request $request, MeetingBooking $booking): RedirectResponse
    {
        $request->validate([
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date', 'after:start_date'],
            'height'       => ['required', 'numeric', 'min:100', 'max:250'],
            'start_weight' => ['required', 'numeric', 'min:30', 'max:300'],
            'goal_weight'  => ['required', 'numeric', 'min:30', 'max:300'],
            'day_types'    => ['nullable', 'array'],
            'day_types.*'  => ['in:workout,rest'],
        ]);

        $subscription = $booking->subscription;
        $user         = $subscription->user;

        DB::transaction(function () use ($request, $booking, $subscription, $user) {

            // ── 1. تأكيد الحجز ──────────────────────────────────
            $booking->update(['status' => 'confirmed']);

            // ── 2. تفعيل الاشتراك وحفظ التواريخ ─────────────────
            $startDate      = \Carbon\Carbon::parse($request->start_date);
            $durationMonths = (int) ($subscription->duration_months ?? 3);

            $subscription->update([
                'status'     => 'active',
                'start_date' => $startDate,
                'end_date'   => $startDate->copy()->addMonths($durationMonths),
            ]);

            // ── 3. حساب عدد الأسابيع ─────────────────────────────
            $start      = \Carbon\Carbon::parse($request->start_date);
            $end        = \Carbon\Carbon::parse($request->end_date);
            $totalWeeks = (int) ceil($start->diffInDays($end) / 7);
            $totalWeeks = max(1, $totalWeeks);

            // ── 4. إنشاء البرنامج التدريبي ───────────────────────
            $program = Program::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'title'       => 'برنامج تدريبي - ' . ($subscription->plan->name ?? 'البرنامج'),
                    'total_weeks' => $totalWeeks,
                    'start_date'  => $request->start_date,
                ]
            );

            // ── 5. إنشاء أيام البرنامج (من اختيار الكوتش) ────────
            $dayNames = [
                1 => 'الأحد',
                2 => 'الاثنين',
                3 => 'الثلاثاء',
                4 => 'الأربعاء',
                5 => 'الخميس',
                6 => 'الجمعة',
                7 => 'السبت',
            ];

            $dayTypes = $request->input('day_types', [
                1 => 'workout',
                2 => 'workout',
                3 => 'workout',
                4 => 'rest',
                5 => 'workout',
                6 => 'workout',
                7 => 'rest',
            ]);

            // حذف الأيام القديمة وإنشاء من جديد
            $program->days()->delete();

            foreach ($dayTypes as $order => $type) {
                ProgramDay::create([
                    'program_id' => $program->id,
                    'day_name'   => $dayNames[$order] ?? "يوم {$order}",
                    'day_order'  => $order,
                    'type'       => $type,
                ]);
            }

            // ── 6. ملف تعريف العميل ───────────────────────────────
            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'height'         => $request->height,
                    'start_weight'   => $request->start_weight,
                    'current_weight' => $request->start_weight,
                    'goal_weight'    => $request->goal_weight,
                ]
            );

            // ── 7. سجل الوزن الأول ───────────────────────────────
            WeightLog::firstOrCreate(
                [
                    'user_id'   => $user->id,
                    'logged_at' => $request->start_date,
                ],
                [
                    'weight' => $request->start_weight,
                ]
            );
        });

        return back()->with('success', 'تم تفعيل الباقة وإنشاء البرنامج بنجاح');
    }

    // ══════════════════════════════════════════════
    // Reject Booking
    // ══════════════════════════════════════════════

    public function rejectBooking(MeetingBooking $booking): RedirectResponse
    {
        $booking->update(['status' => 'cancelled']);

        return back()->with('error', 'تم رفض الحجز');
    }

    // ══════════════════════════════════════════════
    // Update Meet Link
    // ══════════════════════════════════════════════

    public function updateMeetLink(Request $request, MeetingBooking $booking): RedirectResponse
    {
        $request->validate([
            'meet_link' => ['required', 'url', 'starts_with:https://meet.google.com/'],
        ]);

        $booking->update(['meet_link' => $request->input('meet_link')]);

        $user = $booking->user;
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new MeetingLinkMail($booking, $user->name));
            } catch (\Throwable) {
                // Don't fail the request if mail sending fails
            }
        }

        return back()->with('success', 'تم حفظ رابط الاجتماع وإرسال إشعار للمشترك بنجاح');
    }

    // ══════════════════════════════════════════════
    // Update Client Data (Coach)
    // ══════════════════════════════════════════════

    public function updateClient(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'height'         => ['nullable', 'numeric', 'min:100', 'max:250'],
            'start_weight'   => ['nullable', 'numeric', 'min:30', 'max:300'],
            'current_weight' => ['nullable', 'numeric', 'min:30', 'max:300'],
            'goal_weight'    => ['nullable', 'numeric', 'min:30', 'max:300'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'plan_id'        => ['nullable', 'exists:plans,id'],
            'day_types'      => ['nullable', 'array'],
            'day_types.*'    => ['in:workout,rest'],
        ]);

        DB::transaction(function () use ($data, $subscription) {

            // ── 1. تحديث بيانات الاشتراك ────────────────────────
            $subscription->update(array_filter([
                'start_date' => $data['start_date'] ?? null,
                'end_date'   => $data['end_date']   ?? null,
                'plan_id'    => $data['plan_id']    ?? null,
            ], fn($v) => !is_null($v)));

            // ── 2. تحديث UserProfile ─────────────────────────────
            $profileData = array_filter([
                'height'         => $data['height']         ?? null,
                'start_weight'   => $data['start_weight']   ?? null,
                'current_weight' => $data['current_weight'] ?? null,
                'goal_weight'    => $data['goal_weight']    ?? null,
            ], fn($v) => !is_null($v));

            if (!empty($profileData)) {
                UserProfile::updateOrCreate(
                    ['user_id' => $subscription->user_id],
                    $profileData
                );
            }

            // ── 3. لو start_weight اتغير، سجّل في WeightLog ──────
            if (!empty($data['start_weight']) && !empty($data['start_date'])) {
                WeightLog::updateOrCreate(
                    [
                        'user_id'   => $subscription->user_id,
                        'logged_at' => $data['start_date'],
                    ],
                    ['weight' => $data['start_weight']]
                );
            }

            // ── 4. تحديث أيام التمرين والراحة في program_days ────
            if (!empty($data['day_types'])) {
                $program = Program::where('user_id', $subscription->user_id)->first();

                if ($program) {
                    $dayNames = [
                        1 => 'الأحد',
                        2 => 'الاثنين',
                        3 => 'الثلاثاء',
                        4 => 'الأربعاء',
                        5 => 'الخميس',
                        6 => 'الجمعة',
                        7 => 'السبت',
                    ];

                    foreach ($data['day_types'] as $dayOrder => $type) {
                        ProgramDay::updateOrCreate(
                            [
                                'program_id' => $program->id,
                                'day_order'  => $dayOrder,
                            ],
                            [
                                'day_name' => $dayNames[$dayOrder] ?? "يوم {$dayOrder}",
                                'type'     => $type,
                            ]
                        );
                    }
                }
            }
        });

        return back()->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    // ══════════════════════════════════════════════
    // Show Booking Page
    // ══════════════════════════════════════════════

    public function bookings(Request $request)
    {
        $counts = [
            'pending'   => MeetingBooking::where('status', 'pending')->count(),
            'confirmed' => MeetingBooking::where('status', 'confirmed')->count(),
            'completed' => MeetingBooking::where('status', 'completed')->count(),
            'cancelled' => MeetingBooking::where('status', 'cancelled')->count(),
        ];

        $query = MeetingBooking::with(['subscription.user', 'subscription.plan'])
            ->orderByDesc('meeting_date')
            ->orderByDesc('meeting_time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('subscription.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('app.web.bookings', compact('bookings', 'counts'));
    }
}