<?php
namespace Tests\Feature\Web;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ShowFormSmokeTest extends TestCase
{
    public function test_show_form_renders(): void
    {
        Config::set('services.paymob.enabled', true);
        Config::set('payment.fx.egp_rates.SAR', 13.3);
        $plan = Plan::factory()->create();
        PlanPrice::create(['plan_id'=>$plan->id,'currency'=>'SAR','duration_months'=>3,'price'=>100]);
        PlanPrice::create(['plan_id'=>$plan->id,'currency'=>'SAR','duration_months'=>6,'price'=>180]);

        $response = $this->withSession(['currency' => 'SAR'])->get(route('purchase.form', $plan));
        $response->assertOk();
        $response->assertSee('رقم الهاتف');
        $response->assertSee(__('messages.purchase.egp_charge_title'));
        $response->assertDontSee('رفع إيصال الدفع');
    }

    public function test_show_form_renders_when_currency_unsupported(): void
    {
        Config::set('services.paymob.enabled', true);
        Config::set('payment.fx.egp_rates.TND', null);
        $plan = Plan::factory()->create();

        $response = $this->withSession(['currency' => 'TND'])->get(route('purchase.form', $plan));
        $response->assertOk();
        $response->assertSee(__('messages.purchase.currency_unavailable_notice'));
    }

    public function test_show_form_renders_maintenance_notice_when_disabled(): void
    {
        Config::set('services.paymob.enabled', false);
        $plan = Plan::factory()->create();

        $response = $this->withSession(['currency' => 'SAR'])->get(route('purchase.form', $plan));
        $response->assertOk();
        $response->assertSee(__('messages.purchase.maintenance_notice'));
    }
}
