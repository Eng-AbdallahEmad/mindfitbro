<?php

namespace App\Services\Web;

use App\Models\Setting;

class ContactInfo
{
    public static function isEgypt(): bool
    {
        return session('detected_country') === 'EG';
    }

    /**
     * Region-scoped contact details for the current visitor. Reads through
     * Setting::get(), so it inherits Setting's own request-level cache —
     * no separate caching layer.
     */
    public static function current(): array
    {
        $isEgypt = self::isEgypt();
        $prefix  = $isEgypt ? 'contact_eg_' : 'contact_intl_';

        $defaults = $isEgypt
            ? [
                'email' => 'info@mindfitbro.com', 'phone' => '+201141483981', 'whatsapp' => '201141483981',
                'phone_placeholder' => '+20 1xx xxx xxxx',
                'hours_ar' => 'يومياً، من 10 صباحاً – 8 مساءً (بتوقيت القاهرة)', 'hours_en' => 'Every day, 10 AM – 8 PM (Cairo time)',
                'address_ar' => 'القاهرة، مصر', 'address_en' => 'Cairo, Egypt',
            ]
            : [
                'email' => 'info@mindfitbro.com', 'phone' => '+966593035979', 'whatsapp' => '966593035979',
                'phone_placeholder' => '+966 5xx xxx xxx',
                'hours_ar' => 'يومياً، من 10 صباحاً – 8 مساءً (بتوقيت الرياض)', 'hours_en' => 'Every day, 10 AM – 8 PM (Riyadh time)',
                'address_ar' => 'الرياض، المملكة العربية السعودية', 'address_en' => 'Riyadh, Saudi Arabia',
            ];

        return [
            'email'             => Setting::get($prefix . 'email', $defaults['email']),
            'phone'             => Setting::get($prefix . 'phone', $defaults['phone']),
            'whatsapp'          => Setting::get($prefix . 'whatsapp', $defaults['whatsapp']),
            'phone_placeholder' => Setting::get($prefix . 'phone_placeholder', $defaults['phone_placeholder']),
            'hours_ar'          => Setting::get($prefix . 'hours_ar', $defaults['hours_ar']),
            'hours_en'          => Setting::get($prefix . 'hours_en', $defaults['hours_en']),
            'address_ar'        => Setting::get($prefix . 'address_ar', $defaults['address_ar']),
            'address_en'        => Setting::get($prefix . 'address_en', $defaults['address_en']),
        ];
    }
}
