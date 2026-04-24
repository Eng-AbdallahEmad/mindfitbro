<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\MeetingBooking;
use App\Models\Program;
use App\Models\UserProfile;
use App\Models\WeightLog;
use App\Models\ProgramDay;
use App\Models\Subscription;
use App\Models\Plan;
use App\Services\Web\CoachDashboardService;
use App\Services\Web\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $subscription = $this->dashboardService->getSubscription();
        $progress     = $subscription && $subscription->status === 'active'
                        ? $this->dashboardService->getProgress($subscription)
                        : [];

        return view('app.web.dashboard', compact('subscription', 'progress'));
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
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $duration  = $subscription->plan->duration_days ?? 30;

            $duration = (int) $duration;

            $subscription->update([
                'status'     => 'active',
                'start_date' => $startDate,
                'end_date'   => $startDate->copy()->addDays($duration),
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

        return back()->with('success', 'تم تفعيل الباقة وإنشاء البرنامج بنجاح ✓');
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

        return back()->with('success', 'تم حفظ رابط الاجتماع بنجاح ✓');
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

        return back()->with('success', 'تم تحديث بيانات العميل بنجاح ✓');
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