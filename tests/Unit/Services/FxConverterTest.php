<?php

namespace Tests\Unit\Services;

use App\Exceptions\FxRateNotConfiguredException;
use App\Models\FxRate;
use App\Services\FxConverter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FxConverterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('payment.fx.markup_percent', 0);
        Config::set('payment.fx.rounding', 'none');
        Config::set('payment.fx.stale_after_hours', 48);
        Config::set('payment.fx.max_age_hours', 168);
        Config::set('payment.fx.egp_rates.SAR', null);
        Config::set('payment.fx.egp_rates.TND', null);
        Config::set('payment.fx.egp_rates.USD', null);

        // The non-negotiable principle, enforced mechanically: any HTTP call
        // from within this test class fails it immediately.
        Http::fake(function () {
            $this->fail('FxConverter must never make an HTTP call.');
        });
    }

    public function test_egp_passes_through_untouched(): void
    {
        $result = (new FxConverter())->toEgpCents(199.99, 'EGP');

        $this->assertSame(19999, $result['cents']);
        $this->assertSame(1.0, $result['rate']);
        $this->assertSame('identity', $result['source']);
        Http::assertNothingSent();
    }

    public function test_fresh_rate_converts_correctly_with_fresh_source_label(): void
    {
        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subHours(2)]);

        $result = (new FxConverter())->toEgpCents(100, 'SAR');

        $this->assertSame(133000, $result['cents']);
        $this->assertEqualsWithDelta(13.3, $result['rate'], 0.0001);
        $this->assertSame('er-api:fresh', $result['source']);
        Http::assertNothingSent();
    }

    public function test_stale_rate_still_converts_and_logs_a_warning(): void
    {
        Log::spy();

        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subHours(100)]); // > 48, < 168

        $result = (new FxConverter())->toEgpCents(100, 'SAR');

        $this->assertSame(133000, $result['cents']);
        $this->assertSame('er-api:stale', $result['source']);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(fn ($message, $context) => str_contains($message, 'stale') && ($context['currency'] ?? null) === 'SAR');

        Http::assertNothingSent();
    }

    public function test_expired_rate_falls_back_to_config_and_is_labeled_accordingly(): void
    {
        Config::set('payment.fx.egp_rates.SAR', 13.5);

        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subHours(200)]); // >= 168

        $result = (new FxConverter())->toEgpCents(100, 'SAR');

        $this->assertSame(135000, $result['cents'], 'must use the config fallback rate (13.5), not the expired stored one (13.3)');
        $this->assertSame('config-fallback', $result['source']);
        Http::assertNothingSent();
    }

    public function test_expired_rate_with_no_config_fallback_throws_and_makes_no_http_call(): void
    {
        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()->subHours(200)]);
        // payment.fx.egp_rates.SAR left null in setUp()

        $this->expectException(FxRateNotConfiguredException::class);

        try {
            (new FxConverter())->toEgpCents(100, 'SAR');
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_never_fetched_currency_with_no_config_fallback_throws(): void
    {
        // No FxRate row at all for TND, and no config fallback.
        $this->expectException(FxRateNotConfiguredException::class);

        try {
            (new FxConverter())->toEgpCents(100, 'TND');
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_applies_configured_markup_and_rounding_on_top_of_the_stored_rate(): void
    {
        Config::set('payment.fx.markup_percent', 2);
        Config::set('payment.fx.rounding', 'up_to_nearest_5');

        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()]);

        $result = (new FxConverter())->toEgpCents(100, 'SAR');

        // effective rate = 13.3 * 1.02 = 13.566; 100 * 13.566 = 1356.6 → up to 1360
        $this->assertSame(136000, $result['cents']);
        $this->assertEqualsWithDelta(13.566, $result['rate'], 0.0001);
    }

    public function test_unknown_rounding_mode_throws_rather_than_silently_falling_back(): void
    {
        Config::set('payment.fx.rounding', 'round_to_nearest_banana');
        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()]);

        $this->expectException(\InvalidArgumentException::class);

        (new FxConverter())->toEgpCents(100, 'SAR');
    }

    public function test_currency_is_case_insensitive(): void
    {
        FxRate::create(['currency' => 'SAR', 'rate_to_egp' => 13.3, 'source' => 'er-api', 'fetched_at' => now()]);

        $result = (new FxConverter())->toEgpCents(10, 'sar');

        $this->assertSame((int) round(10 * 13.3 * 100), $result['cents']);
    }
}
