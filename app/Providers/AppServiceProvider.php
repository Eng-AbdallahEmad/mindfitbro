<?php

namespace App\Providers;

use App\Models\PageContent;
use App\Observers\PageContentObserver;
use App\Services\Paymob\PaymobClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Config is read ONCE here, not scattered through PaymobClient's
        // methods — the constructor takes plain scalars so tests can build
        // one directly with fake values, with no config()/facade coupling.
        $this->app->singleton(PaymobClient::class, function () {
            return new PaymobClient(
                baseUrl: (string) config('services.paymob.base_url'),
                secretKey: (string) config('services.paymob.secret_key'),
                publicKey: (string) config('services.paymob.public_key'),
                hmacSecret: (string) config('services.paymob.hmac_secret'),
                integrationIdCard: config('services.paymob.integrations.card'),
                timeout: (int) config('services.paymob.http_timeout', 30),
                integrationIdWallet: config('services.paymob.integrations.wallet'),
                integrationIdApplePay: config('services.paymob.integrations.apple_pay'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PageContent::observe(PageContentObserver::class);
    }
}
