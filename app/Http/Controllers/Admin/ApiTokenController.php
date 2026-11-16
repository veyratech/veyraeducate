<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(): View
    {
        $userClass = User::class;
        $tokens = PersonalAccessToken::query()
            ->where('tokenable_type', $userClass)
            ->whereIn('tokenable_id', User::query()->where('role', 'admin')->pluck('id'))
            ->orWhere(function ($query) use ($userClass) {
                $query->where('tokenable_type', $userClass)
                    ->where('tokenable_id', auth()->id());
            })
            ->latest('created_at')
            ->paginate(15);

        return view('admin.api-tokens.index', compact('tokens'));
    }

    public function create(): View
    {
        return view('admin.api-tokens.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['required', 'array'],
            'abilities.*' => ['string', 'in:read,write,*'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $abilities = $validated['abilities'];

        $result = $request->user()->createToken(
            $validated['name'],
            $abilities,
            $validated['expires_at'] ?? null
        );

        session()->flash('status', "Token \"{$validated['name']}\" berhasil dibuat. Simpan token berikut — tidak akan ditampilkan lagi:");
        session()->flash('plain_token', $result->plainTextToken);

        return redirect()->route('admin.api-tokens.index');
    }

    public function destroy(PersonalAccessToken $token): RedirectResponse
    {
        $token->delete();

        return redirect()
            ->route('admin.api-tokens.index')
            ->with('status', 'Token berhasil dicabut.');
    }
}
