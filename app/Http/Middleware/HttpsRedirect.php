<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpsRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->secure() && $request->is('https://*') === false) {
            $scheme = config('app.env') === 'production' ? 'https' : $request->getScheme();

            if ($scheme === 'https' && ! $request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }
        }

        return $next($request);
    }
}
