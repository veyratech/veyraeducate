<?php

namespace App\Providers;

use App\Services\SchoolAppInstaller;
use App\Services\SchoolAppViewFinder;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SchoolAppViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SchoolAppViewFinder::class, function ($app) {
            return new SchoolAppViewFinder(
                $app['files'],
                $app['config']['view.paths'] ?? [base_path('resources/views')]
            );
        });
    }

    public function boot(SchoolAppViewFinder $viewFinder, SchoolAppInstaller $installer): void
    {
        $requestsDir = $installer->appDir('school-requests');
        if (is_dir($requestsDir)) {
            $viewFinder->addAppViews('school-requests', $requestsDir);
        }

        $this->app->booted(function () use ($installer) {
            $baseDir = $installer->baseDir();
            if (is_dir($baseDir)) {
                View::addNamespace('school-app', $baseDir);
            }

            $requestsDir = $installer->appDir('school-requests');
            if (is_dir($requestsDir)) {
                View::addNamespace('school-requests', $requestsDir);
            }
        });
    }
}
