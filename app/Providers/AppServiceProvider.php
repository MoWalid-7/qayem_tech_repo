<?php

namespace App\Providers;

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
        \Laravel\Cashier\Cashier::useSubscriptionModel(\App\Models\Subscription::class);

        if (request()->header('X-Forwarded-Proto') === 'https' || request()->header('X-Forwarded-For')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
