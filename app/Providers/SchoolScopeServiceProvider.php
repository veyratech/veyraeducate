<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SchoolScopeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('school.scope.bypass', fn () => false);
    }

    public function boot(): void
    {
        // Nothing to boot.
    }
}
