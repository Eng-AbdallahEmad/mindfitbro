<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    // Simple cache per request
    private static ?Collection $cache = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (static::$cache === null) {
            static::$cache = static::pluck('value', 'key');
        }

        return static::$cache->get($key, $default);
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        static::$cache = null; // invalidate cache
    }

    public static function allGrouped(): Collection
    {
        return static::all()->groupBy('group');
    }

    public static function seedDefaults(): void
    {
        $defaults = static::defaultValues();
        foreach ($defaults as $group => $items) {
            foreach ($items as $key => $value) {
                static::firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
            }
        }
    }

    public static function defaultValues(): array
    {
        return [
            'general' => [
                'site_name'         => 'MindFitBro',
                'contact_phone'     => '+966593035979',
                'contact_email'     => 'info@mindfitbro.com',
                'whatsapp_number'   => '966593035979',
                'location'          => 'المملكة العربية السعودية',
                'contact_phone_display' => '+966 593 035 979',
            ],
            'social' => [
                'instagram_url' => '#',
                'tiktok_url'    => '#',
                'youtube_url'   => '#',
            ],
            'stats' => [
                'hero_success_count'        => '500',
                'whyus_card1_count'         => '+2,500',
                'whyus_card2_count'         => '+20,000',
                'whyus_card3_count'         => '+10,000',
                'testimonials_clients'      => '500',
                'testimonials_rating'       => '5.0',
                'testimonials_satisfaction' => '100',
                'partners_certified'        => '20',
                'partners_countries'        => '8',
                'partners_years'            => '3',
            ],
            'videos' => [
                'video1_url' => 'https://drive.google.com/file/d/1_uI2GML9pVNSK-3oa1JuXqbuXRBhwf13/view?usp=sharing',
                'video2_url' => 'https://drive.google.com/file/d/1_uI2GML9pVNSK-3oa1JuXqbuXRBhwf13/view?usp=sharing',
                'video3_url' => 'https://drive.google.com/file/d/1_uI2GML9pVNSK-3oa1JuXqbuXRBhwf13/view?usp=sharing',
            ],
            'booking' => [
                'booking_available_days' => '0,1,2,3,4',
                'booking_time_slots'     => '09:00,10:00,11:00,12:00,14:00,15:00,16:00,17:00,18:00',
            ],
            'marquee' => [
                'marquee_items_ar' => "خصومات إبريل 40%\nعروض العيد لسه مخلصتش\nجاهز للجاي...؟\nيلا ننزل الكرش اللي عندك\nمش ناوي تعمل فورمة العيد؟\nالعيد خلص بس عروضنا لسه مخلصتش",
                'marquee_items_en' => "April Discounts 40%\nEid Offers Are Still Going!\nReady for What's Coming?\nLet's Work on That Body!\nDon't You Want to Get in Shape?\nEid is Over but Our Offers Continue!",
            ],
            'sections' => [
                'section_partners_visible' => '1',
            ],
            'maintenance' => [
                'maintenance_mode_enabled' => '0',
                'maintenance_message'      => '',
                'maintenance_eta'          => '',
            ],
            'family_reward' => [
                'family_reward_enabled'        => '0',
                'family_reward_plan_id'        => '',
                'family_reward_discount_mode'  => 'fixed',
                'family_reward_discount_value' => '20',
                'family_reward_discount_min'   => '10',
                'family_reward_discount_max'   => '30',
                'family_reward_max_invites'    => '5',
            ],
        ];
    }
}
