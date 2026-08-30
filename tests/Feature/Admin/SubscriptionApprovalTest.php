<?php

namespace Tests\Feature\Admin;

use App\Mail\OrderApprovedMail;
use App\Mail\OrderRejectedMail;
use App\Models\Coupon;
use App\Models\FamilyInvitation;
use App\Models\MeetingBooking;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Characterization tests for SubscriptionsController::approve()/reject(),
 * written against the UNCHANGED controller (audit :117-232 / :235-281)
 * before the OrderApprovalService/OrderRejectionService extraction (Batch 3
 * Part C). These pin down every side effect the audit identified so the
 * extraction can be verified to be behavior-preserving.
 */
class SubscriptionApprovalTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    // ── approve: existing user, completed profile ──────────────────────

    public function test_approve_links_existing_completed_profile_user_by_email(): void
    {
        Mail::fake();

        $admin = $this->admin();

        $existingUser = User::factory()->create([
            'profile_completed_at' => now(),
        ]);

        $subscription = Subscription::factory()->guest()->create([
            'guest_email' => $existingUser->email,
        ]);

        $usersBefore = User::count();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.subscriptions.approve', $subscription));

        $response->assertRedirect(route('admin.subscriptions.show', $subscription));

        $this->assertSame($usersBefore, User::count(), 'no new user should be created');

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->status);
        $this->assertSame($existingUser->id, $subscription->user_id);
        $this->assertNull($subscription->guest_name);
        $this->assertNull($subscription->guest_email);
        $this->assertNull($subscription->guest_token);

        Mail::assertSent(OrderApprovedMail::class, function ($mail) use ($existingUser) {
            return $mail->hasTo($existingUser->email)
                && $mail->accountAutoCreated === false
                && $mail->isGuest === true;
        });
    }

    // ── approve: guest with no matching user → new account ─────────────

    public function test_approve_creates_new_user_for_unmatched_guest_email(): void
    {
        Mail::fake();

        $admin = $this->admin();

        $subscription = Subscription::factory()->guest()->create();
        $guestEmail = $subscription->guest_email;
        $guestToken = $subscription->guest_token;

        $usersBefore = User::count();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.subscriptions.approve', $subscription));

        $response->assertRedirect(route('admin.subscriptions.show', $subscription));

        $this->assertSame($usersBefore + 1, User::count());

        $newUser = User::where('email', $guestEmail)->first();
        $this->assertNotNull($newUser);
        $this->assertSame('user', $newUser->role);
        $this->assertSame('active', $newUser->status);
        $this->assertNull($newUser->profile_completed_at);

        $subscription->refresh();
        $this->assertSame($newUser->id, $subscription->user_id);
        $this->assertNull($subscription->guest_name);
        $this->assertNull($subscription->guest_email);
        // guest_token is intentionally preserved — it's the setup-account key
        $this->assertSame($guestToken, $subscription->guest_token);

        $expectedUrl = route('setup-account.show', $guestToken);

        Mail::assertSent(OrderApprovedMail::class, function ($mail) use ($guestEmail, $expectedUrl) {
            return $mail->hasTo($guestEmail)
                && $mail->accountAutoCreated === true
                && $mail->isGuest === true
                && $mail->passwordSetUrl === $expectedUrl;
        });
    }

    // ── approve: family-invitation coupon gets redeemed ─────────────────

    public function test_approve_redeems_family_invitation_coupon(): void
    {
        Mail::fake();

        $inviter = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'FAM-REDEEM']);

        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $customer->id,
            'coupon_code' => $coupon->code,
        ]);

        $invitation = FamilyInvitation::create([
            'subscription_id' => $subscription->id,
            'inviter_user_id' => $inviter->id,
            'invitee_email' => $customer->email,
            'coupon_id' => $coupon->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.approve', $subscription))
            ->assertRedirect(route('admin.subscriptions.show', $subscription));

        $invitation->refresh();
        $this->assertSame('redeemed', $invitation->status);
        $this->assertNotNull($invitation->redeemed_at);
    }

    // ── approve: status/reviewer fields set, activation untouched ──────

    public function test_approve_sets_review_fields_but_does_not_activate_or_touch_bookings(): void
    {
        Mail::fake();

        $admin = $this->admin();
        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $customer->id,
            'start_date' => null,
            'end_date' => null,
        ]);

        $booking = MeetingBooking::create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'meeting_date' => now()->addDay()->toDateString(),
            'meeting_time' => '10:00',
            'status' => 'pending',
            'slot_lock' => now()->addDay()->format('Y-m-d').' 10:00',
        ]);
        $bookingBefore = $booking->toArray();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.subscriptions.approve', $subscription))
            ->assertRedirect(route('admin.subscriptions.show', $subscription));

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->status);
        $this->assertSame($admin->id, $subscription->reviewed_by);
        $this->assertNotNull($subscription->reviewed_at);

        // Audit finding: approval does NOT activate the subscription.
        $this->assertNull($subscription->start_date);
        $this->assertNull($subscription->end_date);

        // Audit finding: approval does NOT touch meeting_bookings.
        $booking->refresh();
        $this->assertSame($bookingBefore['status'], $booking->status);
        $this->assertSame($bookingBefore['slot_lock'], $booking->slot_lock);
        $this->assertSame(1, MeetingBooking::count());
    }

    // ── approve: guard ───────────────────────────────────────────────

    public function test_approve_on_non_pending_review_order_returns_422(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => Subscription::STATUS_APPROVED,
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.approve', $subscription))
            ->assertStatus(422);
    }

    // ── approve: cache invalidation ─────────────────────────────────────

    public function test_approve_clears_popular_plan_cache(): void
    {
        Mail::fake();
        Cache::put('popular_plan_id', 5, 60);

        $subscription = Subscription::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.approve', $subscription));

        $this->assertNull(Cache::get('popular_plan_id'));
    }

    // ── approve: mail failure does not roll back the approval ──────────

    public function test_approve_mail_failure_is_logged_and_does_not_roll_back(): void
    {
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')->once()->andThrow(new \Exception('SMTP unreachable'));
        Mail::shouldReceive('to')->once()->andReturn($mailer);
        // spy() (not shouldReceive()) so any OTHER incidental Log call in the
        // request lifecycle is simply recorded, not treated as an unexpected
        // call — a strict shouldReceive() here previously caused an unmatched
        // call to cascade into Laravel's own exception-reporting logger.
        Log::spy();

        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $customer->id]);

        $response = $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.approve', $subscription));

        $response->assertRedirect(route('admin.subscriptions.show', $subscription));
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->fresh()->status);

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn ($message, $context) => $message === 'OrderApprovedMail failed' && is_array($context));
    }

    // ── reject: status, reason, family-invitation revert, mail ─────────

    public function test_reject_sets_status_reverts_used_invitation_and_sends_mail(): void
    {
        Mail::fake();

        $inviter = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'FAM-REJECT']);
        $customer = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $customer->id,
            'coupon_code' => $coupon->code,
        ]);

        $invitation = FamilyInvitation::create([
            'subscription_id' => $subscription->id,
            'inviter_user_id' => $inviter->id,
            'invitee_email' => $customer->email,
            'coupon_id' => $coupon->id,
            'status' => 'used',
        ]);

        $admin = $this->admin();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.subscriptions.reject', $subscription), [
                'rejection_reason' => 'الإيصال غير واضح، برجاء إعادة الإرسال',
            ]);

        $response->assertRedirect(route('admin.subscriptions.show', $subscription));

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_REJECTED, $subscription->status);
        $this->assertSame('الإيصال غير واضح، برجاء إعادة الإرسال', $subscription->rejection_reason);
        $this->assertSame($admin->id, $subscription->reviewed_by);
        $this->assertNotNull($subscription->reviewed_at);

        $invitation->refresh();
        $this->assertSame('pending', $invitation->status);

        Mail::assertSent(OrderRejectedMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    // ── reject: guard ────────────────────────────────────────────────

    public function test_reject_on_non_pending_review_order_returns_422(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => Subscription::STATUS_REJECTED,
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.reject', $subscription), [
                'rejection_reason' => 'سبب أي',
            ])
            ->assertStatus(422);
    }
}
