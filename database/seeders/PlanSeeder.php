<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanPrice;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // ── Features ────────────────────────────────────────────────
        $featuresList = [
            ['key' => 'training',        'name' => 'برنامج تدريبي'],
            ['key' => 'nutrition',        'name' => 'برنامج غذائي'],
            ['key' => 'supplements',      'name' => 'نظام مكملات'],
            ['key' => 'monthly_follow',   'name' => 'متابعة شهرية'],
            ['key' => 'weekly_follow',    'name' => 'متابعة أسبوعية'],
            ['key' => 'daily_follow',     'name' => 'متابعة يومية'],
            ['key' => 'medical_follow',   'name' => 'متابعة طبية (حسب الحاجة)'],
            ['key' => 'family_plan',      'name' => 'إتاحة الباقة العائلية'],
            ['key' => 'program_updates',  'name' => 'التعديلات في البرامج حسب التطور'],
        ];

        foreach ($featuresList as $f) {
            Feature::updateOrCreate(['key' => $f['key']], ['name' => $f['name'], 'is_active' => true]);
        }

        // ── Deactivate old plans that no longer exist in the new lineup ──
        Plan::whereIn('key', ['starter', 'pro', 'elite'])->update(['is_active' => false]);

        // ── New plans ────────────────────────────────────────────────
        $plans = [
            [
                'key'        => 'standard',
                'name'       => 'ستاندرد',
                'icon'       => 'bolt',
                'icon_bg'    => 'bg-blue-50',
                'icon_color' => 'text-primary',
                'desc'       => 'للمبتدئين اللي عايزين يبدأوا رحلتهم بثقة',
                'price'      => 999,    // SAR 3m — kept as convenience field
                'popular'    => false,
                'btn_class'  => 'border-2 border-primary text-primary hover:bg-blue-50',
                'sort_order' => 1,
                'is_active'  => true,
                'features'   => [
                    ['key' => 'training',       'check' => true],
                    ['key' => 'nutrition',       'check' => true],
                    ['key' => 'supplements',     'check' => true],
                    ['key' => 'monthly_follow',  'check' => true],
                    ['key' => 'program_updates', 'check' => true],
                    ['key' => 'family_plan',     'check' => false],
                    ['key' => 'medical_follow',  'check' => false],
                ],
                'prices' => [
                    // SAR — required
                    ['currency' => 'SAR', 'duration_months' => 3, 'price' => 999],
                    ['currency' => 'SAR', 'duration_months' => 6, 'price' => 1699],
                    // EGP
                    ['currency' => 'EGP', 'duration_months' => 3, 'price' => 4199],
                    ['currency' => 'EGP', 'duration_months' => 6, 'price' => 6199],
                    // TND
                    ['currency' => 'TND', 'duration_months' => 3, 'price' => 249],
                    ['currency' => 'TND', 'duration_months' => 6, 'price' => 449],
                    // USD
                    ['currency' => 'USD', 'duration_months' => 3, 'price' => 249],
                    ['currency' => 'USD', 'duration_months' => 6, 'price' => 449],
                ],
            ],
            [
                'key'        => 'elite',
                'name'       => 'إيليت',
                'icon'       => 'workspace_premium',
                'icon_bg'    => 'bg-primary',
                'icon_color' => 'text-accent',
                'desc'       => 'للجادين اللي عايزين نتيجة سريعة ومتابعة مكثفة',
                'price'      => 1699,   // SAR 3m — convenience field
                'popular'    => true,
                'btn_class'  => 'bg-accent text-darkBg hover:bg-yellow-300',
                'sort_order' => 2,
                'is_active'  => true,
                'features'   => [
                    ['key' => 'training',       'check' => true],
                    ['key' => 'nutrition',       'check' => true],
                    ['key' => 'supplements',     'check' => true],
                    ['key' => 'weekly_follow',   'check' => true],
                    ['key' => 'medical_follow',  'check' => true],
                    ['key' => 'family_plan',     'check' => true],
                    ['key' => 'program_updates', 'check' => true],
                ],
                'prices' => [
                    // SAR — required
                    ['currency' => 'SAR', 'duration_months' => 3, 'price' => 1699],
                    ['currency' => 'SAR', 'duration_months' => 6, 'price' => 2799],
                    // EGP
                    ['currency' => 'EGP', 'duration_months' => 3, 'price' => 6999],
                    ['currency' => 'EGP', 'duration_months' => 6, 'price' => 9999],
                    // TND
                    ['currency' => 'TND', 'duration_months' => 3, 'price' => 449],
                    ['currency' => 'TND', 'duration_months' => 6, 'price' => 799],
                    // USD
                    ['currency' => 'USD', 'duration_months' => 3, 'price' => 449],
                    ['currency' => 'USD', 'duration_months' => 6, 'price' => 799],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            $prices   = $planData['prices'];
            unset($planData['features'], $planData['prices']);

            $plan = Plan::updateOrCreate(['key' => $planData['key']], $planData);

            // Features
            $syncData = [];
            foreach ($features as $i => $feat) {
                $feature = Feature::where('key', $feat['key'])->first();
                if ($feature) {
                    $syncData[$feature->id] = ['is_included' => $feat['check'], 'sort_order' => $i + 1];
                }
            }
            $plan->features()->sync($syncData);

            // Prices — upsert all 8 rows
            foreach ($prices as $priceRow) {
                PlanPrice::updateOrCreate(
                    ['plan_id' => $plan->id, 'currency' => $priceRow['currency'], 'duration_months' => $priceRow['duration_months']],
                    ['price' => $priceRow['price']]
                );
            }
        }
    }
}
