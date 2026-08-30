<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\OrderNotApprovableException;
use App\Exceptions\OrderNotRejectableException;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\OrderApprovalService;
use App\Services\OrderRejectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SubscriptionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        // ── Search ──────────────────────────────────────────────
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                })
                ->orWhere('guest_name', 'like', "%{$search}%")
                ->orWhere('guest_email', 'like', "%{$search}%")
                ->orWhere('coupon_code', 'like', "%{$search}%");
            });
        }

        // ── Filters ─────────────────────────────────────────────
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($plan = $request->input('plan')) {
            $query->where('plan_id', $plan);
        }

        if ($type = $request->input('type')) {
            if ($type === 'legacy') {
                $query->whereNull('duration_months');
            } else {
                $query->where('duration_months', (int) $type);
            }
        }

        if ($member = $request->input('member')) {
            if ($member === 'guest') {
                $query->whereNull('user_id');
            } else {
                $query->whereNotNull('user_id');
            }
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();

        $plans = Plan::orderBy('sort_order')->get(['id', 'name']);

        $revenueRows = Subscription::whereIn('status', ['active', 'approved', 'expired'])
            ->selectRaw('currency, SUM(total) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        $stats = [
            'total'          => Subscription::count(),
            'active'         => Subscription::where('status', 'active')->count(),
            'pending_review' => Subscription::where('status', 'pending_review')->count(),
            'approved'       => Subscription::where('status', 'approved')->count(),
            'expired'        => Subscription::where('status', 'expired')->count(),
            'revenue_by_currency' => $revenueRows->pluck('total', 'currency')->toArray(),
        ];

        return view('app.admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'meetingBookings', 'reviewer']);

        return view('app.admin.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status'          => 'required|in:pending_review,approved,active,expired,rejected,cancelled,waiting',
            'duration_months' => 'nullable|in:3,6',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ], [
            'status.required'         => 'الحالة مطلوبة',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
        ]);

        $subscription->update($request->only('status', 'duration_months', 'start_date', 'end_date'));
        Cache::forget('popular_plan_id');

        return back()->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    // ── Phase B: Approve ────────────────────────────────────────
    public function approve(Request $request, Subscription $subscription, OrderApprovalService $approvalService)
    {
        try {
            $approvalService->approve($subscription, Auth::guard('admin')->id());
        } catch (OrderNotApprovableException $e) {
            abort(422);
        }

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'تم الموافقة على الاشتراك وإرسال إيميل التأكيد للعميل');
    }

    // ── Phase B: Reject ──────────────────────────────────────────
    public function reject(Request $request, Subscription $subscription, OrderRejectionService $rejectionService)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:1000',
        ], [
            'rejection_reason.required' => 'سبب الرفض مطلوب',
            'rejection_reason.min'      => 'اكتب سبباً واضحاً (5 أحرف على الأقل)',
        ]);

        try {
            $rejectionService->reject($subscription, $request->rejection_reason, Auth::guard('admin')->id());
        } catch (OrderNotRejectableException $e) {
            abort(422);
        }

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'تم رفض الاشتراك وإرسال إيميل الإشعار للعميل');
    }

    // ── View Receipt (private file) ──────────────────────────────
    public function viewReceipt(Subscription $subscription)
    {
        abort_if(! $subscription->receipt_path, 404);
        abort_unless(Storage::disk('local')->exists($subscription->receipt_path), 404);

        $path     = Storage::disk('local')->path($subscription->receipt_path);
        $mimeType = mime_content_type($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="receipt-' . $subscription->id . '"',
        ]);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
