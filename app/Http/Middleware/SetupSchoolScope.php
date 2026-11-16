<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Scopes\SchoolScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetupSchoolScope
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();
        $isBypass = $user !== null && $user->isAdmin();

        if ($isBypass) {
            SchoolScope::bypass();
        } else {
            SchoolScope::restore();
        }

        try {
            return $next($request);
        } finally {
            if ($isBypass) {
                SchoolScope::restore();
            }
        }
    }
}
