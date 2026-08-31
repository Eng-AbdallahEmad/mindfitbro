<?php
namespace Tests\Feature\Admin;
use App\Models\MeetingBooking;
use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;

class AdminSubViewsSmokeTest extends TestCase
{
    private function admin(): User { return User::factory()->admin()->create(); }

    public function test_index_page_renders_with_a_subscription_that_has_an_active_booking(): void
    {
        $subscription = Subscription::factory()->create(['status' => Subscription::STATUS_APPROVED]);
        MeetingBooking::factory()->create(['subscription_id' => $subscription->id, 'meet_link' => 'https://meet.google.com/aaa-bbbb-ccc']);

        $response = $this->actingAs($this->admin(), 'admin')->get(route('admin.subscriptions.index'));
        $response->assertOk();
        $response->assertSee('رابط الاجتماع');
        $response->assertSee('نشط');
    }

    public function test_show_page_renders_with_prefilled_meeting_link(): void
    {
        $subscription = Subscription::factory()->create(['status' => Subscription::STATUS_APPROVED]);
        MeetingBooking::factory()->create(['subscription_id' => $subscription->id, 'meet_link' => 'https://meet.google.com/aaa-bbbb-ccc']);

        $response = $this->actingAs($this->admin(), 'admin')->get(route('admin.subscriptions.show', $subscription));
        $response->assertOk();
        $response->assertSee('https://meet.google.com/aaa-bbbb-ccc');
    }
}
