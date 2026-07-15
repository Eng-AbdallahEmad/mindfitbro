<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('carts:cleanup')->everyFiveMinutes();

// تحديث الاشتراكات المنتهية — يعمل يومياً الساعة 00:05
Schedule::command('subscriptions:expire')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-expire.log'));

// تذكير بانتهاء الاشتراك قبل 5 أيام — يعمل يومياً الساعة 00:10
Schedule::command('subscriptions:remind')
    ->dailyAt('00:10')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-remind.log'));

// إشعار بدء الاشتراك لمن بدأت باقتهم اليوم — يعمل يومياً الساعة 00:15
Schedule::command('subscriptions:notify-start')
    ->dailyAt('00:15')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/subscriptions-start.log'));