<?php

namespace Tests\Feature\Admin;

use App\Models\Coupon;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ManualReviewStalenessTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    // ── Model helpers ────────────────────────────────────────────────

    public function test_review_staleness_is_null_outside_pending_review_manual(): void
    {
        $paid = Subscription::factory()->paidViaPaymob()->create();
        $this->assertNull($paid->reviewWaitingSince());
        $this->assertNull($paid->reviewStalenessLevel());
    }

    public function test_review_staleness_levels_follow_configured_thresholds(): void
    {
        Config::set('payment.manual_review_thresholds.warning_hours', 48);
        Config::set('payment.manual_review_thresholds.urgent_hours', 168);

        $fresh   = Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(1)]);
        $warning = Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(60)]);
        $urgent  = Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(200)]);

        $this->assertSame('normal', $fresh->reviewStalenessLevel());
        $this->assertSame('warning', $warning->reviewStalenessLevel());
        $this->assertSame('urgent', $urgent->reviewStalenessLevel());
    }

    // ── Admin dashboard banner ──────────────────────────────────────

    public function test_dashboard_shows_overdue_banner_only_when_reviews_are_overdue(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertDontSee('بانتظار المراجعة منذ فترة طويلة');

        Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(100)]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $response->assertSee('بانتظار المراجعة منذ فترة طويلة');
    }

    public function test_dashboard_banner_ignores_fresh_reviews(): void
    {
        $admin = $this->admin();
        Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(2)]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $response->assertDontSee('بانتظار المراجعة منذ فترة طويلة');
    }

    // ── Admin list: sort/filter/badge ───────────────────────────────

    public function test_sort_by_waiting_orders_oldest_first(): void
    {
        $admin = $this->admin();
        $newer = Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(5)]);
        $older = Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(500)]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.subscriptions.index', ['status' => 'pending_review', 'sort' => 'waiting']));

        $response->assertOk();
        $ids = $response->viewData('subscriptions')->pluck('id')->values()->all();
        $this->assertTrue(array_search($older->id, $ids) < array_search($newer->id, $ids), 'older (more overdue) row must come first');
    }

    public function test_stale_badge_shown_for_overdue_manual_review(): void
    {
        $admin = $this->admin();
        Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(200)]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.subscriptions.index', ['status' => 'pending_review']));

        $response->assertSee('متأخر جداً');
    }

    public function test_no_stale_badge_for_fresh_manual_review(): void
    {
        $admin = $this->admin();
        Subscription::factory()->guest()->pendingReview()->create(['payment_intended_at' => now()->subHours(1)]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.subscriptions.index', ['status' => 'pending_review']));

        $response->assertDontSee('متأخر جداً');
        $response->assertDontSee('متأخر (');
    }

    // ── Detail page: waiting time + coupon capacity ─────────────────

    public function test_detail_page_shows_waiting_time_and_holding_coupon(): void
    {
        $admin = $this->admin();
        Coupon::create(['code' => 'HOLD5', 'type' => 'fixed', 'value' => 10, 'is_active' => true, 'max_uses' => 5]);
        $subscription = Subscription::factory()->guest()->pendingReview()->create([
            'payment_intended_at' => now()->subHours(72),
            'coupon_code' => 'HOLD5',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.subscriptions.show', $subscription));

        $response->assertOk();
        $response->assertSee('بانتظار المراجعة منذ');
        $response->assertSee('يحجز سعة كوبون HOLD5');
    }

    public function test_detail_page_omits_coupon_capacity_note_when_coupon_is_unlimited(): void
    {
        $admin = $this->admin();
        Coupon::create(['code' => 'UNLIMITED', 'type' => 'fixed', 'value' => 10, 'is_active' => true, 'max_uses' => null]);
        $subscription = Subscription::factory()->guest()->pendingReview()->create([
            'payment_intended_at' => now()->subHours(10),
            'coupon_code' => 'UNLIMITED',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.subscriptions.show', $subscription));

        $response->assertDontSee('يحجز سعة كوبون');
    }
}
