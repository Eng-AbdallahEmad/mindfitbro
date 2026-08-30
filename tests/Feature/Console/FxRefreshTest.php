<?php

namespace Tests\Feature\Console;

use App\Models\FxRate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FxRefreshTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.fx.primary', 'er_api');
        Config::set('services.fx.fallback', 'currency_api');
        Config::set('payment.fx.sanity_deviation_percent', 15);
    }

    private function fakeErApi(array $rates): void
    {
        Http::fake(function ($request) use ($rates) {
            foreach ($rates as $currency => $egp) {
                if (str_contains($request->url(), "open.er-api.com/v6/latest/{$currency}")) {
                    return Http::response(['result' => 'success', 'rates' => ['EGP' => $egp]], 200);
                }
            }

            return Http::response([], 404);
        });
    }

    public function test_fetches_and_stores_rates_from_primary_provider(): void
    {
        $this->fakeErApi(['SAR' => 13.3, 'TND' => 17.3, 'USD' => 50.25]);

        Artisan::call('fx:refresh');

        $this->assertSame(3, FxRate::count());
        $sar = FxRate::where('currency', 'SAR')->firstOrFail();
        $this->assertEqualsWithDelta(13.3, (float) $sar->rate_to_egp, 0.0001);
        $this->assertSame('er-api', $sar->source);
        $this->assertNotNull($sar->fetched_at);
    }

    public function test_falls_back_to_secondary_provider_when_primary_fails(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), 'open.er-api.com')) {
                return Http::response(['message' => 'down'], 500);
            }
            if (str_contains($request->url(), 'currencies/sar.json')) {
                return Http::response(['sar' => ['egp' => 13.36]], 200);
            }
            if (str_contains($request->url(), 'currencies/tnd.json')) {
                return Http::response(['tnd' => ['egp' => 17.15]], 200);
            }
            if (str_contains($request->url(), 'currencies/usd.json')) {
                return Http::response(['usd' => ['egp' => 50.03]], 200);
            }

            return Http::response([], 404);
        });

        Artisan::call('fx:refresh');

        $this->assertSame(3, FxRate::count());
        $sar = FxRate::where('currency', 'SAR')->firstOrFail();
        $this->assertSame('currency-api', $sar->source);
        $this->assertEqualsWithDelta(13.36, (float) $sar->rate_to_egp, 0.0001);
    }

    public function test_both_providers_down_leaves_stored_rates_untouched_and_logs_error(): void
    {
        Log::spy();

        $existing = FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subDay()]);
        $originalFetchedAt = $existing->fetched_at;

        Http::fake(fn () => Http::response(['message' => 'down'], 500));

        Artisan::call('fx:refresh');

        $existing->refresh();
        $this->assertEqualsWithDelta(13.3, (float) $existing->rate_to_egp, 0.0001, 'stored rate must be untouched');
        $this->assertSame('er-api', $existing->source);
        $this->assertTrue($originalFetchedAt->equalTo($existing->fetched_at));

        Log::shouldHaveReceived('error')
            ->withArgs(fn ($message) => str_contains($message, 'both providers failed'));
    }

    public function test_sanity_guard_rejects_a_rate_deviating_beyond_the_threshold_and_keeps_the_old_one(): void
    {
        Log::spy();

        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subDay()]);

        // 100 vs 13.3 is a huge deviation, way past the 15% default limit.
        $this->fakeErApi(['SAR' => 100.0, 'TND' => 17.3, 'USD' => 50.25]);

        Artisan::call('fx:refresh');

        $sar = FxRate::where('currency', 'SAR')->firstOrFail();
        $this->assertEqualsWithDelta(13.3, (float) $sar->rate_to_egp, 0.0001, 'the wild fetched value must be rejected');

        Log::shouldHaveReceived('critical')
            ->withArgs(fn ($message) => str_contains($message, 'sanity guard'));

        // TND/USD had no prior stored value, so no deviation baseline exists
        // — they should still be stored normally.
        $this->assertNotNull(FxRate::where('currency', 'TND')->first());
        $this->assertNotNull(FxRate::where('currency', 'USD')->first());
    }

    public function test_rejects_zero_and_negative_rates(): void
    {
        $this->fakeErApi(['SAR' => 0, 'TND' => -5, 'USD' => 50.25]);

        Artisan::call('fx:refresh');

        $this->assertNull(FxRate::where('currency', 'SAR')->first());
        $this->assertNull(FxRate::where('currency', 'TND')->first());
        $this->assertNotNull(FxRate::where('currency', 'USD')->first());
    }

    public function test_is_idempotent_when_run_repeatedly(): void
    {
        $this->fakeErApi(['SAR' => 13.3, 'TND' => 17.3, 'USD' => 50.25]);

        Artisan::call('fx:refresh');
        Artisan::call('fx:refresh');
        Artisan::call('fx:refresh');

        $this->assertSame(3, FxRate::count(), 'must update in place, never accumulate duplicate rows');
    }
}
