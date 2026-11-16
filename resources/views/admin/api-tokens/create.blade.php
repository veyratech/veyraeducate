<x-admin-layout title="Buat Token API — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Akses API</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Buat Token API</h2>
                <p class="mt-1 text-sm text-muted">Buat token pribadi untuk mengakses API secara programatik.</p>
            </div>
            <a href="{{ route('admin.api-tokens.index') }}" class="btn-minimal-secondary text-sm">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-lg space-y-6">

            @if (session('error'))
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Data belum tersimpan</p>
                    <ul class="mt-2 space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-minimal p-6 sm:p-7 stagger-in">
                <form method="POST" action="{{ route('admin.api-tokens.store') }}" class="space-y-5">
                    @csrf

                    <div class="form-group-minimal">
                        <x-input-label for="token_name" value="Nama Token" />
                        <x-text-input id="token_name" class="mt-1 form-input-minimal w-full" name="name" :value="old('name')" required autofocus placeholder="contoh: aplikasi-mobile" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label value="Abilities / Scope" />
                        <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Pilih hak akses yang diinginkan. Gunakan <code class="font-mono">*</code> untuk akses penuh.</p>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-sm font-medium text-ink dark:text-slate-200">
                                <input type="checkbox" name="abilities[]" value="read" class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400" @checked(in_array('read', old('abilities', [])))>
                                <span>read:</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-sm font-medium text-ink dark:text-slate-200">
                                <input type="checkbox" name="abilities[]" value="write" class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400" @checked(in_array('write', old('abilities', [])))>
                                <span>write:</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-sm font-medium text-ink dark:text-slate-200">
                                <input type="checkbox" name="abilities[]" value="*" class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400" @checked(in_array('*', old('abilities', [])))>
                                <span>*</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('abilities')" class="mt-1" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="expires_at" value="Tanggal Kedaluwarsa (Opsional)" />
                        <x-text-input id="expires_at" class="mt-1 form-input-minimal w-full" type="date" name="expires_at" :value="old('expires_at')" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                        <x-input-error :messages="$errors->get('expires_at')" class="mt-1" />
                        <p class="mt-1 text-[11px] text-muted dark:text-slate-500">Kosongkan jika token tidak ingin kedaluwarsa.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('admin.api-tokens.index') }}" class="btn-minimal-secondary text-sm">Batal</a>
                        <x-primary-button class="btn-minimal-primary text-sm">Buat Token</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
