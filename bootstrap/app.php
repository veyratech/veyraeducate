<?php

use App\Http\Middleware\EnsureActiveSubscription;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\HttpsRedirect;
use App\Http\Middleware\SetupSchoolScope;
use App\Http\Middleware\TrackSchoolAppAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: dirname(__DIR__).'/routes/web.php',
        api: dirname(__DIR__).'/routes/api.php',
        commands: dirname(__DIR__).'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'super.admin' => EnsureSuperAdmin::class,
            'track.app.access' => TrackSchoolAppAccess::class,
            'school.scope' => SetupSchoolScope::class,
            'subscription.active' => EnsureActiveSubscription::class,
            'https.redirect' => HttpsRedirect::class,
        ]);

        $middleware->prepend(SetupSchoolScope::class);

        $middleware->redirectGuestsTo(function (Request $request): string {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
