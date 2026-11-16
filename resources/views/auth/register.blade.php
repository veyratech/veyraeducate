<x-guest-layout>
    <h1 class="text-xl font-extrabold tracking-tight text-ink">Daftar sekolah</h1>
    <p class="mt-1 text-sm text-muted">Aktivasi otomatis setelah pembayaran terverifikasi.</p>

    <form method="POST" action="{{ route('register') . (request()->query('paket') ? '?paket='.urlencode(request()->query('paket')) : '') }}" class="mt-6 space-y-4">
        @csrf

        @if (request()->query('paket'))
            <input type="hidden" name="plan_code" value="{{ request()->query('paket') }}">
            <p class="rounded-xl border border-brand-100 bg-brand-50 px-3 py-2 text-sm text-brand-900">
                Pilihan paket dikunci sampai checkout — ganti bisa dilakukan di halaman Harga.
            </p>
        @endif

        @if (request()->query('ref'))
            <input type="hidden" name="ref" value="{{ request()->query('ref') }}">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-3 text-xs text-emerald-900 flex items-center gap-2.5">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-500 text-white font-bold text-xs">🎁</span>
                <div>
                    <p class="font-bold text-emerald-900">Kode Referral Terpasang: <span class="font-mono text-emerald-700">{{ request()->query('ref') }}</span></p>
                    <p class="text-[11px] text-emerald-700">Dapatkan prioritas setup dan pendampingan migrasi gratis dari mitra operator sekolah.</p>
                </div>
            </div>
        @endif

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="mt-1 block w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi password" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a class="text-sm font-medium text-muted hover:text-brand-700" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button>
                Daftar{{ request()->query('paket') ? ' & lanjut checkout' : '' }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
