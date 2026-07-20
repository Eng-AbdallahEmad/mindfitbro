<?php

namespace App\Services\Web;

use App\Models\Setting;

class PlatformService
{
    public static function isOnlineMode(): bool
    {
        return Setting::get('platform_mode', 'in_person') === 'online';
    }
}
