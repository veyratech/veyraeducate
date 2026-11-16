<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\SchoolIdentityUpdateRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'school' => $request->user()->school ?? new School,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the school identity information.
     */
    public function updateSchoolIdentity(SchoolIdentityUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school;

        if (! $school) {
            $school = new School(['owner_user_id' => $user->id]);
            $user->forceFill(['school_id' => $school->id])->save();
        }

        $school->fill([
            'name' => $request->school_name,
            'npsn' => $request->school_npsn,
            'email' => $request->school_email,
            'phone' => $request->school_phone,
            'city' => $request->school_city,
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'school-identity-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
