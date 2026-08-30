<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\FxRateNotConfiguredException;
use App\Http\Controllers\Controller;
use App\Models\FxRate;
use App\Services\FxConverter;
use Illuminate\Support\Facades\Artisan;

/**
 * Read-only visibility into the fx_rates table + a manual "refresh now"
 * button — how an admin notices a frozen scheduler (audit Risk D-7) without
 * reading logs. Deliberately reuses the real FxConverter for the
 * effective-rate/source columns rather than re-implementing the tier
 * resolution here, so this panel can never drift from what checkout
 * actually does.
 */
class FxRatesController extends Controller
{
    private const CURRENCIES = ['SAR', 'TND', 'USD'];

    public function index(FxConverter $fxConverter)
    {
        $markupPercent = (float) config('payment.fx.markup_percent', 0);
        $staleAfterHours = (int) config('payment.fx.stale_after_hours', 48);
        $maxAgeHours = (int) config('payment.fx.max_age_hours', 168);

        $rows = collect(self::CURRENCIES)->map(function (string $currency) use ($fxConverter) {
            $fxRate = FxRate::where('currency', $currency)->first();

            $row = [
                'currency' => $currency,
                'stored_rate' => $fxRate ? (float) $fxRate->rate_to_egp : null,
                'stored_source' => $fxRate?->source,
                'fetched_at' => $fxRate?->fetched_at,
                'age_hours' => $fxRate?->ageInHours(),
                'tier' => $this->tierFor($fxRate),
                'effective_rate' => null,
                'effective_source' => null,
            ];

            try {
                $conversion = $fxConverter->toEgpCents(1, $currency);
                $row['effective_rate'] = $conversion['rate'];
                $row['effective_source'] = $conversion['source'];
            } catch (FxRateNotConfiguredException) {
                // leave nulls — the view renders this as "unavailable"
            }

            return $row;
        });

        return view('app.admin.fx_rates.index', [
            'rows' => $rows,
            'markupPercent' => $markupPercent,
            'staleAfterHours' => $staleAfterHours,
            'maxAgeHours' => $maxAgeHours,
        ]);
    }

    public function refresh()
    {
        Artisan::call('fx:refresh');

        return redirect()->route('admin.fx-rates.index')
            ->with('success', 'تم تشغيل تحديث أسعار الصرف. راجع الجدول أدناه للنتيجة.');
    }

    private function tierFor(?FxRate $fxRate): string
    {
        if (!$fxRate) {
            return 'missing';
        }

        $ageHours = $fxRate->ageInHours();
        $staleAfterHours = (int) config('payment.fx.stale_after_hours', 48);
        $maxAgeHours = (int) config('payment.fx.max_age_hours', 168);

        return match (true) {
            $ageHours < $staleAfterHours => 'fresh',
            $ageHours < $maxAgeHours => 'stale',
            default => 'expired',
        };
    }
}
