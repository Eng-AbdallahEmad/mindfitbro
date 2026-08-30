<?php

namespace Tests\Feature\Admin;

use App\Models\FxRate;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FxRatesControllerTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_view_the_panel_with_no_rates_fetched_yet(): void
    {
        $response = $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.fx-rates.index'));

        $response->assertOk();
        $response->assertSee('SAR');
        $response->assertSee('لم يتم الجلب بعد');
    }

    public function test_admin_can_view_the_panel_with_a_fresh_rate(): void
    {
        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()]);

        $response = $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.fx-rates.index'));

        $response->assertOk();
        $response->assertSee('13.300000');
        $response->assertSee('محدَّث'); // "fresh" tier label
    }

    public function test_admin_can_view_the_panel_with_a_stale_rate(): void
    {
        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subHours(100)]);

        $response = $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.fx-rates.index'));

        $response->assertOk();
        $response->assertSee('قديم'); // "stale" tier label
    }

    public function test_refresh_button_runs_the_command_and_redirects_back(): void
    {
        Http::fake(fn () => Http::response(['result' => 'success', 'rates' => ['EGP' => 13.3]], 200));

        $response = $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.fx-rates.refresh'));

        $response->assertRedirect(route('admin.fx-rates.index'));
        $response->assertSessionHas('success');
        $this->assertGreaterThan(0, FxRate::count());
    }

    public function test_guest_cannot_view_the_panel(): void
    {
        $this->get(route('admin.fx-rates.index'))
            ->assertRedirect(route('admin.login'));
    }
}
