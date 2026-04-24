<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\MeetingBooking;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function show(Subscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        $booking = MeetingBooking::query()
            ->where('subscription_id', $subscription->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->latest()
            ->first();

        return view('app.web.schedule-meeting', compact('subscription', 'booking'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'date'            => ['required', 'date'],
            'time'            => ['required', 'date_format:H:i'],
            'meet_link'       => ['nullable', 'url'],
        ], [
            'subscription_id.required' => 'الاشتراك مطلوب.',
            'subscription_id.exists'   => 'الاشتراك غير موجود.',
            'date.required'            => 'تاريخ الاجتماع مطلوب.',
            'date.date'                => 'تاريخ الاجتماع غير صحيح.',
            'time.required'            => 'وقت الاجتماع مطلوب.',
            'time.date_format'         => 'وقت الاجتماع غير صحيح.',
            'meet_link.url'            => 'رابط الاجتماع غير صحيح.',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);

        $this->authorizeSubscription($subscription);

        $meetingDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->date . ' ' . $request->time,
            config('app.timezone')
        );

        if ($meetingDateTime->isPast()) {
            return response()->json([
                'message' => 'لا يمكن حجز موعد في وقت سابق.',
            ], 422);
        }

        $alreadyBooked = MeetingBooking::query()
            ->where('subscription_id', $subscription->id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->exists();

        if ($alreadyBooked) {
            return response()->json([
                'message' => 'تم حجز اجتماع بالفعل لهذا الاشتراك.',
            ], 422);
        }

        $sameSlotBooked = MeetingBooking::query()
            ->where('meeting_date', $request->date)
            ->where('meeting_time', $request->time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($sameSlotBooked) {
            return response()->json([
                'message' => 'هذا الموعد محجوز بالفعل، اختر موعدًا آخر.',
            ], 422);
        }

        $booking = MeetingBooking::create([
            'user_id'         => Auth::id(),
            'subscription_id' => $subscription->id,
            'meeting_date'    => $request->date,
            'meeting_time'    => $request->time,
            'meet_link'       => $request->meet_link,
            'status'          => 'pending',
        ]);

        return response()->json([
            'message' => 'تم حجز الاجتماع بنجاح.',
            'booking' => [
                'id'           => $booking->id,
                'meeting_date' => $booking->meeting_date,
                'meeting_time' => $booking->meeting_time,
                'meet_link'    => $booking->meet_link,
                'status'       => $booking->status,
            ],
        ]);
    }

    public function update(Request $request, MeetingBooking $booking)
    {
        $this->authorizeSubscription($booking->subscription);

        $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $meetingDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->date . ' ' . $request->time,
            config('app.timezone')
        );

        if ($meetingDateTime->isPast()) {
            return response()->json(['message' => 'لا يمكن حجز موعد في وقت سابق.'], 422);
        }

        $sameSlotBooked = MeetingBooking::query()
            ->where('id', '!=', $booking->id)
            ->where('meeting_date', $request->date)
            ->where('meeting_time', $request->time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($sameSlotBooked) {
            return response()->json(['message' => 'هذا الموعد محجوز بالفعل، اختر موعدًا آخر.'], 422);
        }

        $booking->update([
            'meeting_date' => $request->date,
            'meeting_time' => $request->time,
            'status'       => 'pending',
        ]);

        return response()->json([
            'message' => 'تم تعديل الموعد بنجاح.',
            'booking' => [
                'id'           => $booking->id,
                'meeting_date' => $booking->meeting_date,
                'meeting_time' => $booking->meeting_time,
                'status'       => $booking->status,
            ],
        ]);
    }

    private function authorizeSubscription(Subscription $subscription): void
    {
        if (!Auth::check()) {
            abort(403, 'يجب تسجيل الدخول أولاً.');
        }

        if ((int) $subscription->user_id !== (int) Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الاشتراك.');
        }
    }
}