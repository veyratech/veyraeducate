<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'plan' => Plan::findByCode(request()->query('paket', request()->query('plan_code', '')) ?? ''),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $planCode = $request->query('paket') ?? $request->query('plan_code');

        if ($planCode) {
            $request->merge(['plan_code' => $planCode]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $ref = $request->query('ref') ?? $request->input('ref');
        $referredBySchoolId = null;
        if ($ref) {
            if (preg_match('/^SCH-(\d+)$/i', (string) $ref, $matches)) {
                $candidateId = (int) $matches[1];
                if (School::whereKey($candidateId)->exists()) {
                    $referredBySchoolId = $candidateId;
                }
            } else {
                $found = School::where('meta->referral_code', strtoupper((string) $ref))->first();
                if ($found) {
                    $referredBySchoolId = $found->id;
                }
            }
        }

        $school = School::create([
            'owner_user_id' => $user->id,
            'name' => 'Sekolah '.$user->name,
            'email' => $user->email,
            'meta' => array_filter([
                'level' => 'SMA/SMK',
                'status' => 'Swasta',
                'academic_year' => '2026/2027',
                'semester' => 'Ganjil',
                'referred_by_school_id' => $referredBySchoolId,
                'referred_at' => $referredBySchoolId ? now()->toIso8601String() : null,
            ]),
        ]);
        $user->forceFill(['school_id' => $school->id])->save();

        event(new Registered($user));

        Auth::login($user);

        $planCode = $request->query('paket')
            ?? $request->query('plan_code')
            ?? $request->input('plan_code');

        if ($planCode && Plan::findByCode((string) $planCode)) {
            return redirect()->route('checkout.show', $planCode);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
