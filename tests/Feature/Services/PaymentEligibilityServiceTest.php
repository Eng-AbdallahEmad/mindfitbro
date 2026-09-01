<?php

namespace Tests\Feature\Services;

use App\Services\Web\PaymentEligibilityService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentEligibilityServiceTest extends TestCase
{
    private function service(): PaymentEligibilityService
    {
        return new PaymentEligibilityService();
    }

    public function test_saudi_arabia_is_eligible_for_the_sar_bank_account(): void
    {
        $method = $this->service()->manualMethodFor('SA');

        $this->assertNotNull($method);
        $this->assertSame('SAR', $method['currency']);
        $this->assertSame('STC Bank', $method['bank_name']);
    }

    public function test_egypt_is_eligible_for_instapay(): void
    {
        $method = $this->service()->manualMethodFor('EG');

        $this->assertNotNull($method);
        $this->assertSame('EGP', $method['currency']);
        $this->assertSame('instapay', $method['type']);
    }

    public function test_tunisia_is_eligible_for_the_tnd_bank_account(): void
    {
        $method = $this->service()->manualMethodFor('TN');

        $this->assertNotNull($method);
        $this->assertSame('TND', $method['currency']);
        $this->assertSame('الشركة التونسية للبنك (STB)', $method['bank_name']);
    }

    public function test_country_codes_are_case_insensitive(): void
    {
        $this->assertNotNull($this->service()->manualMethodFor('sa'));
        $this->assertNotNull($this->service()->manualMethodFor('eg'));
    }

    public function test_unmapped_countries_are_not_eligible(): void
    {
        foreach (['US', 'GB', 'KW', 'AE', 'FR'] as $country) {
            $this->assertNull($this->service()->manualMethodFor($country), "{$country} must not be eligible");
        }
    }

    public function test_null_or_empty_country_fails_closed(): void
    {
        $this->assertNull($this->service()->manualMethodFor(null));
        $this->assertNull($this->service()->manualMethodFor(''));
    }

    public function test_a_currency_disabled_in_config_is_not_eligible_even_if_mapped(): void
    {
        Config::set('payment.manual.SAR.enabled', false);

        $this->assertNull($this->service()->manualMethodFor('SA'));
    }

    public function test_manual_allowed_for_mirrors_manual_method_for(): void
    {
        $this->assertTrue($this->service()->manualAllowedFor('EG'));
        $this->assertFalse($this->service()->manualAllowedFor('US'));
        $this->assertFalse($this->service()->manualAllowedFor(null));
    }
}
