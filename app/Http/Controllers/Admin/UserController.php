<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ALLOWED_ROLES = [
        User::ROLE_SCHOOL,
        User::ROLE_ADMIN,
        User::ROLE_BENDAHARA,
        User::ROLE_OPERATOR,
        User::ROLE_TEACHER,
        User::ROLE_STAFF,
        User::ROLE_SUPER_ADMIN,
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $role = trim((string) $request->query('role', ''));

        $query = User::query()
            ->with('school')
            ->latest('id');

        if ($role !== '' && in_array($role, self::ALLOWED_ROLES, true)) {
            $query->where('role', $role);
        }

        if ($search !== '') {
            $escaped = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($escaped) {
                $q->where('name', 'like', "%{$escaped}%")
                    ->orWhere('email', 'like', "%{$escaped}%")
                    ->orWhere('phone', 'like', "%{$escaped}%")
                    ->orWhereHas('school', fn ($s) => $s->where('name', 'like', "%{$escaped}%"));
            });
        }

        return view('admin.users.index', [
            'users' => $query->paginate(15)->withQueryString(),
            'schools' => School::query()->orderBy('name')->get(['id', 'name']),
            'search' => $search,
            'roleFilter' => $role,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(self::ALLOWED_ROLES)],
            'school_id' => ['nullable', 'exists:schools,id'],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'school_id' => $validated['role'] === User::ROLE_ADMIN ? null : ($validated['school_id'] ?? null),
            'phone' => $validated['phone'] ?? null,
            'email_verified_at' => now(),
        ]);

        AuditLog::record('user_created', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'school_id' => $user->school_id,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(self::ALLOWED_ROLES)],
            'school_id' => ['nullable', 'exists:schools,id'],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== User::ROLE_ADMIN) {
            return back()->with('error', 'Anda tidak dapat menurunkan peran administrator akun Anda sendiri.');
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'school_id' => $validated['role'] === User::ROLE_ADMIN ? null : ($validated['school_id'] ?? null),
            'phone' => $validated['phone'] ?: null,
        ];

        $passwordChanged = ! empty($validated['password']);
        if ($passwordChanged) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        AuditLog::record('user_updated', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'school_id' => $user->school_id,
            'password_changed' => $passwordChanged,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Data pengguna {$user->name} berhasil diperbarui".($passwordChanged ? ' beserta kata sandi baru.' : '.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;

        AuditLog::record('user_deleted', $user, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Pengguna {$name} berhasil dihapus.");
    }
}
