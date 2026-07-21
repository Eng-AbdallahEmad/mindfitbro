<?php

namespace App\Providers;

use App\Models\PageContent;
use App\Observers\PageContentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PageContent::observe(PageContentObserver::class);
    }
}
