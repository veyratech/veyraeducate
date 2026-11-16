<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Redirect a school whose subscription has lapsed back to checkout
     * before it can open any school application.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $school = $request->user()?->school;

        if ($school === null || ! $school->subscriptions()->exists() || $school->hasActiveSubscription()) {
            return $next($request);
        }

        $planCode = $this->renewalPlanCode($school);

        $redirect = $planCode === null
            ? redirect()->route('home')
            : redirect()->route('checkout.show', $planCode);

        return $redirect->with(
            'status',
            'Langganan sekolah Anda telah berakhir. Perpanjang paket untuk kembali mengakses aplikasi.'
        );
    }

    /**
     * Renew the plan the school last used, falling back to the first active plan.
     */
    private function renewalPlanCode(School $school): ?string
    {
        return $school->latestSubscription()?->plan?->code
            ?? Plan::query()->where('is_active', true)->orderBy('sort_order')->value('code');
    }
}
