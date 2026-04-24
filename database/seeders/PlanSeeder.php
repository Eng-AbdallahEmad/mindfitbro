<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Features
        $featuresList = [
            ['key' => 'training', 'name' => 'برنامج تدريبي'],
            ['key' => 'nutrition', 'name' => 'برنامج غذائي'],
            ['key' => 'supplements', 'name' => 'نظام مكملات'],
            ['key' => 'monthly_follow', 'name' => 'متابعة شهرية'],
            ['key' => 'weekly_follow', 'name' => 'متابعة أسبوعية'],
            ['key' => 'daily_follow', 'name' => 'متابعة يومية'],
            ['key' => 'medical_follow', 'name' => 'متابعة طبية (حسب الحاجة)'],
            ['key' => 'family_plan', 'name' => 'إتاحة الباقة العائلية'],
            ['key' => 'program_updates', 'name' => 'التعديلات في البرامج حسب التطور'],
        ];

        foreach ($featuresList as $featureData) {
            Feature::updateOrCreate(
                ['key' => $featureData['key']],
                [
                    'name' => $featureData['name'],
                    'is_active' => true,
                ]
            );
        }

        // 🔹 Plans
        $plans = [
            [
                'key'                  => 'starter',
                'name'                 => 'الأساسي',
                'icon'                 => 'bolt',
                'icon_bg'              => 'bg-blue-50',
                'icon_color'           => 'text-primary',
                'desc'                 => 'للمبتدئين اللي عايزين يبدأوا رحلتهم بثقة',
                'price'                => 299,
                'yearly_discount_rate' => 0.10,
                'popular'              => false,
                'btn_class'            => 'border-2 border-primary text-primary hover:bg-blue-50',
                'features'             => [
                    ['key' => 'training', 'check' => true],
                    ['key' => 'nutrition', 'check' => true],
                    ['key' => 'supplements', 'check' => true],
                    ['key' => 'monthly_follow', 'check' => true],
                    ['key' => 'program_updates', 'check' => true],
                    ['key' => 'family_plan', 'check' => false],
                    ['key' => 'medical_follow', 'check' => false],
                ],
            ],
            [
                'key'                  => 'pro',
                'name'                 => 'النخبة',
                'icon'                 => 'star',
                'icon_bg'              => 'bg-primary',
                'icon_color'           => 'text-accent',
                'desc'                 => 'للجادين اللي عايزين نتيجة سريعة ومتابعة مكثفة',
                'price'                => 599,
                'yearly_discount_rate' => 0.15,
                'popular'              => true,
                'btn_class'            => 'bg-accent text-darkBg hover:bg-yellow-300',
                'features'             => [
                    ['key' => 'training', 'check' => true],
                    ['key' => 'nutrition', 'check' => true],
                    ['key' => 'supplements', 'check' => true],
                    ['key' => 'weekly_follow', 'check' => true],
                    ['key' => 'medical_follow', 'check' => true],
                    ['key' => 'family_plan', 'check' => true],
                    ['key' => 'program_updates', 'check' => true],
                ],
            ],
            [
                'key'                  => 'elite',
                'name'                 => 'إيليت',
                'icon'                 => 'emoji_events',
                'icon_bg'              => 'bg-amber-50',
                'icon_color'           => 'text-amber-500',
                'desc'                 => 'تجربة VIP كاملة مع تحليلات متقدمة وكوتش خاص',
                'price'                => 999,
                'yearly_discount_rate' => 0.20,
                'popular'              => false,
                'btn_class'            => 'bg-primary text-white hover:bg-blue-800',
                'features'             => [
                    ['key' => 'training', 'check' => true],
                    ['key' => 'nutrition', 'check' => true],
                    ['key' => 'supplements', 'check' => true],
                    ['key' => 'daily_follow', 'check' => true],
                    ['key' => 'medical_follow', 'check' => true],
                    ['key' => 'family_plan', 'check' => true],
                    ['key' => 'program_updates', 'check' => true],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::updateOrCreate(
                ['key' => $planData['key']],
                $planData
            );

            $syncData = [];

            foreach ($features as $index => $feat) {
                $feature = Feature::where('key', $feat['key'])->first();

                if ($feature) {
                    $syncData[$feature->id] = [
                        'is_included' => $feat['check'],
                        'sort_order'  => $index + 1,
                    ];
                }
            }

            $plan->features()->sync($syncData);
        }
    }
}