<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DemoController extends Controller
{
    /**
     * Tampilkan halaman demo Veyra Educate.
     */
    public function show(): View
    {
        return view('pages.demo');
    }

    /**
     * Login instan ke akun simulasi demo sekolah.
     */
    public function login(Request $request): RedirectResponse
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'demo.sekolah@veyraeducate.id'],
            [
                'name' => 'Budi Santoso, S.Pd. (Demo)',
                'password' => Hash::make('DemoVeyra2026!'),
                'role' => User::ROLE_SCHOOL,
                'email_verified_at' => now(),
                'phone' => '081234567890',
            ]
        );

        if (! $user->school_id) {
            $school = School::query()->firstOrCreate(
                ['npsn' => '69999999'],
                [
                    'name' => 'SMK Nusantara Mandiri (Sekolah Simulasi)',
                    'email' => 'info@smknusantara-demo.sch.id',
                    'phone' => '021-5558899',
                    'city' => 'Jakarta Selatan',
                    'district' => 'Kebayoran Baru',
                    'owner_user_id' => $user->id,
                    'meta' => [
                        'level' => 'SMK',
                        'status' => 'Swasta',
                        'headmaster' => 'Drs. H. Ahmad Dahlan, M.Pd.',
                        'academic_year' => '2026/2027',
                        'semester' => 'Ganjil',
                    ],
                ]
            );
            $user->forceFill(['school_id' => $school->id])->save();
        }

        $school = $user->school;
        if ($school && ! $school->hasActiveSubscription()) {
            $plan = Plan::query()->where('is_active', true)->orderByDesc('price_idr')->first()
                ?? Plan::query()->first();

            if ($plan) {
                Subscription::query()->create([
                    'school_id' => $school->id,
                    'plan_id' => $plan->id,
                    'status' => SubscriptionStatus::Active->value,
                    'starts_at' => now(),
                    'ends_at' => now()->addMonths(6),
                ]);
                $school->grantPlanAppAccess($plan);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Selamat datang di akun simulasi demo Veyra Educate! Anda dapat mencoba fitur dashboard, manajemen sekolah, dan katalog aplikasi secara langsung.');
    }
}
