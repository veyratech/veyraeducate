<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Support\InvoiceConfig;
use App\Support\MidtransConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
        MidtransConfig::apply();
        InvoiceConfig::apply();

        View::composer('layouts.navigation', function ($view) {
            $view->with('recentAnnouncements', Announcement::query()
                ->published()
                ->ordered()
                ->limit(3)
                ->get());
        });

        if ($this->app->environment('testing')) {
            RateLimiter::for('global', fn () => Limit::none());
            RateLimiter::for('auth', fn () => Limit::none());
            RateLimiter::for('checkout', fn () => Limit::none());
            RateLimiter::for('webhook', fn () => Limit::none());
            RateLimiter::for('admin', fn () => Limit::none());
            RateLimiter::for('school-app', fn () => Limit::none());
            return;
        }

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('webhook', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id);
        });

        RateLimiter::for('school-app', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}
