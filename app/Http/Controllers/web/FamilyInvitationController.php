<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\FamilyInvitationMail;
use App\Models\Coupon;
use App\Models\FamilyInvitation;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FamilyInvitationController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        [$enabled, $rewardPlanId, $maxInvites, $mode, $fixedValue, $rangeMin, $rangeMax]
            = $this->resolveSettings();

        $subscription = Subscription::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'active'])
            ->latest()
            ->first();

        if (! $enabled || ! $subscription || $subscription->plan_id !== $rewardPlanId) {
            abort(403);
        }

        // Format validation
        $request->validate([
            'invitations'          => 'required|array|min:1',
            'invitations.*.email'  => 'required|email|max:255',
            'invitations.*.name'   => 'required|string|max:255',
        ], [
            'invitations.required'         => 'أضف دعوة واحدة على الأقل',
            'invitations.*.email.required' => 'البريد الإلكتروني مطلوب',
            'invitations.*.email.email'    => 'البريد الإلكتروني غير صحيح',
            'invitations.*.name.required'  => 'الاسم مطلوب',
        ]);

        $rows = $request->input('invitations');

        // Quota check
        $usedInvites = FamilyInvitation::where('subscription_id', $subscription->id)->count();
        $remaining   = max(0, $maxInvites - $usedInvites);

        if ($remaining === 0) {
            return back()
                ->withErrors(['quota' => 'وصلت إلى الحد الأقصى من الدعوات (' . $maxInvites . ')'])
                ->withInput();
        }

        // Per-row business logic validation
        $rowErrors = [];
        $validRows = [];
        $existingEmails = FamilyInvitation::where('subscription_id', $subscription->id)
            ->pluck('invitee_email')
            ->map(fn ($e) => strtolower($e))
            ->toArray();

        $seenInRequest = [];

        foreach ($rows as $i => $row) {
            $email = strtolower(trim($row['email']));
            $name  = trim($row['name']);

            if ($email === strtolower($user->email)) {
                $rowErrors[$i]['email'] = 'لا يمكنك دعوة نفسك';
                continue;
            }

            if (in_array($email, $existingEmails)) {
                $rowErrors[$i]['email'] = 'تم إرسال دعوة لهذا البريد سابقاً';
                continue;
            }

            if (in_array($email, $seenInRequest)) {
                $rowErrors[$i]['email'] = 'البريد الإلكتروني مكرر في هذا الطلب';
                continue;
            }

            $seenInRequest[] = $email;
            $validRows[]     = ['index' => $i, 'email' => $email, 'name' => $name];
        }

        if (! empty($rowErrors)) {
            session()->flash('row_errors', $rowErrors);
            return back()->withInput();
        }

        if (count($validRows) > $remaining) {
            session()->flash('row_errors', [
                count($rows) - 1 => ['email' => "لا يمكن إرسال أكثر من {$remaining} دعوة إضافية"],
            ]);
            return back()->withInput();
        }

        // Process each invitation
        $sent = 0;
        foreach ($validRows as $rowData) {
            $discountValue = $mode === 'range'
                ? round($rangeMin + (mt_rand(0, 1000) / 1000) * ($rangeMax - $rangeMin), 1)
                : $fixedValue;

            // Generate unique FAM-XXXX code
            do {
                $code = 'FAM-' . strtoupper(Str::random(4));
            } while (Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->exists());

            $coupon = Coupon::create([
                'code'       => $code,
                'type'       => 'percentage',
                'value'      => $discountValue,
                'is_active'  => true,
                'expires_at' => now()->addDays(30),
                'max_uses'   => 1,
            ]);

            $invitation = FamilyInvitation::create([
                'subscription_id' => $subscription->id,
                'inviter_user_id' => $user->id,
                'invitee_email'   => $rowData['email'],
                'invitee_name'    => $rowData['name'],
                'coupon_id'       => $coupon->id,
                'status'          => 'pending',
                'sent_at'         => now(),
            ]);

            try {
                Mail::to($rowData['email'])->send(
                    new FamilyInvitationMail($invitation, $coupon, $user->name, (int) round($discountValue))
                );
                $sent++;
            } catch (\Throwable $e) {
                Log::error('FamilyInvitationMail failed', [
                    'invitation_id' => $invitation->id,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        return back()->with('invitation_sent', $sent);
    }

    private function resolveSettings(): array
    {
        return [
            \App\Models\Setting::get('family_reward_enabled', '0') === '1',
            (int) \App\Models\Setting::get('family_reward_plan_id', 0),
            (int) \App\Models\Setting::get('family_reward_max_invites', 5),
            \App\Models\Setting::get('family_reward_discount_mode', 'fixed'),
            (float) \App\Models\Setting::get('family_reward_discount_value', 20),
            (float) \App\Models\Setting::get('family_reward_discount_min', 10),
            (float) \App\Models\Setting::get('family_reward_discount_max', 30),
        ];
    }
}
