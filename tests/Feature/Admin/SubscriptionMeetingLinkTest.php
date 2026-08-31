<?php

namespace Tests\Feature\Admin;

use App\Mail\MeetingLinkMail;
use App\Models\MeetingBooking;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubscriptionMeetingLinkTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_set_meeting_link_and_customer_is_emailed_with_their_date_and_the_link(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $customer->id, 'status' => Subscription::STATUS_APPROVED]);
        $booking = MeetingBooking::factory()->create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.meeting-link', $subscription), [
                'meet_link' => 'https://meet.google.com/abc-defg-hij',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('reopen_subscription_id', $subscription->id);

        $booking->refresh();
        $this->assertSame('https://meet.google.com/abc-defg-hij', $booking->meet_link);

        Mail::assertSent(MeetingLinkMail::class, function ($mail) use ($booking, $customer) {
            return $mail->hasTo($customer->email)
                && $mail->booking->id === $booking->id
                && $mail->booking->meet_link === 'https://meet.google.com/abc-defg-hij'
                && $mail->isChange === false;
        });
    }

    public function test_changing_an_existing_link_sends_a_different_change_email(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $customer->id, 'status' => Subscription::STATUS_APPROVED]);
        $booking = MeetingBooking::factory()->confirmed()->create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'meet_link' => 'https://meet.google.com/old-link-xyz',
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.meeting-link', $subscription), [
                'meet_link' => 'https://meet.google.com/new-link-abc',
            ])
            ->assertRedirect();

        $this->assertSame('https://meet.google.com/new-link-abc', $booking->fresh()->meet_link);

        Mail::assertSent(MeetingLinkMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email) && $mail->isChange === true;
        });
    }

    public function test_saving_the_same_link_again_is_a_no_op_and_sends_no_mail(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $customer->id]);
        $booking = MeetingBooking::factory()->confirmed()->create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'meet_link' => 'https://meet.google.com/same-link-abc',
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.meeting-link', $subscription), [
                'meet_link' => 'https://meet.google.com/same-link-abc',
            ])
            ->assertRedirect();

        $this->assertSame('https://meet.google.com/same-link-abc', $booking->fresh()->meet_link);
        Mail::assertNothingSent();
    }

    public function test_picks_the_latest_active_booking_when_more_than_one_exists(): void
    {
        Mail::fake();

        $customer = User::factory()->create();
        $subscription = Subscription::factory()->create(['user_id' => $customer->id]);

        $older = MeetingBooking::factory()->create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'status' => 'cancelled', // not active — must be ignored
        ]);
        $latestActive = MeetingBooking::factory()->confirmed()->create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
        ]);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.meeting-link', $subscription), [
                'meet_link' => 'https://meet.google.com/xyz-1234-abc',
            ])
            ->assertRedirect();

        $this->assertNull($older->fresh()->meet_link);
        $this->assertSame('https://meet.google.com/xyz-1234-abc', $latestActive->fresh()->meet_link);
    }

    public function test_no_active_booking_redirects_back_with_an_error_and_sends_no_mail(): void
    {
        Mail::fake();

        $subscription = Subscription::factory()->create();

        $response = $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.meeting-link', $subscription), [
                'meet_link' => 'https://meet.google.com/abc-defg-hij',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    public function test_non_google_meet_link_is_rejected(): void
    {
        $subscription = Subscription::factory()->create();
        MeetingBooking::factory()->create(['subscription_id' => $subscription->id, 'status' => 'pending']);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.subscriptions.meeting-link', $subscription), [
                'meet_link' => 'https://zoom.us/j/123456',
            ])
            ->assertSessionHasErrors('meet_link');
    }

    // ── Status dropdown restricted to 3 values (this task) ──────────────

    public function test_update_accepts_only_active_expired_or_cancelled(): void
    {
        $subscription = Subscription::factory()->create(['status' => Subscription::STATUS_APPROVED]);
        $admin = $this->admin();

        foreach (['active', 'expired', 'cancelled'] as $status) {
            $this->actingAs($admin, 'admin')
                ->put(route('admin.subscriptions.update', $subscription), ['status' => $status])
                ->assertRedirect();

            $this->assertSame($status, $subscription->fresh()->status);
        }
    }

    public function test_update_rejects_statuses_outside_the_new_allowed_list(): void
    {
        $subscription = Subscription::factory()->create(['status' => Subscription::STATUS_APPROVED]);

        foreach (['approved', 'pending_review', 'rejected', 'waiting'] as $status) {
            $this->actingAs($this->admin(), 'admin')
                ->put(route('admin.subscriptions.update', $subscription), ['status' => $status])
                ->assertSessionHasErrors('status');
        }

        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->fresh()->status, 'status must be unchanged');
    }
}
