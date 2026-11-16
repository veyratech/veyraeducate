<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackSchoolAppAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        /** @var User $user */
        $user = Auth::user();

        if ($user && $user->school_id) {
            $throttleKey = "school_last_app_access_{$user->school_id}";
            // Only update database at most once every 5 minutes to avoid MySQL row locks
            if (Cache::add($throttleKey, true, now()->addMinutes(5))) {
                $user->school()->update(['last_app_access_at' => now()]);
            }
        }

        return $response;
    }
}
