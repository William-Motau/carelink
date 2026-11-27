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
        // Enforce HTTPS (and therefore Secure cookies + HSTS) in production.
        if ($this->app->environment('production')) {
            $this->app['url']->forceScheme('https');
        }

        \App\Models\Booking::observe(\App\Observers\BookingObserver::class);
    }
}
