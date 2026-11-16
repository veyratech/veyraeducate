<x-admin-guest-layout title="Login Admin - {{ config('app.name') }}">
    <x-auth-session-status class="mt-2" :status="session('status')" />

    @if (session('admin_login_hint'))
        <div class="mb-4 rounded-xl border border-gold-200 dark:border-gold-800/50 bg-gold-50 dark:bg-gold-950/20 px-4 py-3 text-sm text-ink dark:text-slate-200">
            {{ session('admin_login_hint') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
        @csrf

        {{-- Email field --}}
        <div>
            <x-input-label for="email" value="Email admin" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
                <x-text-input
                    id="email"
                    class="!pl-11 mt-1 form-input-minimal"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@veyraeducate.test"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password field --}}
        <div>
            <x-input-label for="password" value="Password" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <x-text-input
                    id="password"
                    class="!pl-11 mt-1 form-input-minimal"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember --}}
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-muted cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-brand-200 text-brand-700 focus:ring-brand-500" name="remember">
                <span>Ingat saya</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="pt-2">
            <x-primary-button class="btn-minimal-primary w-full">
                Masuk admin
            </x-primary-button>
        </div>
    </form>
</x-admin-guest-layout>
