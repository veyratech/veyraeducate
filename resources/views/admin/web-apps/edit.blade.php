<x-admin-layout title="Edit {{ $webApp->name }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Wadah Aplikasi</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Edit Aplikasi</h2>
                <p class="mt-1 text-sm text-muted">Perbarui informasi aplikasi {{ $webApp->name }}</p>
            </div>
            <a href="{{ route('admin.web-apps.show', $webApp->slug) }}" class="btn-minimal-secondary">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site max-w-2xl">
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

            <div class="card-minimal p-6 stagger-in">
                <form method="POST" action="{{ route('admin.web-apps.update', $webApp->slug) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group-minimal">
                            <x-input-label for="name" value="Nama aplikasi" />
                            <x-text-input id="name" class="mt-1 form-input-minimal" name="name" :value="old('name', $webApp->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="form-group-minimal">
                            <x-input-label for="slug" value="Slug" />
                            <p class="mt-1 text-xs text-muted">Identitas unik, tidak dapat diubah setelah disimpan.</p>
                            <x-text-input id="slug" class="mt-1 form-input-minimal" name="slug" :value="old('slug', $webApp->slug)" required readonly />
                        </div>
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="access_url" value="URL Tujuan (Opsional — Subdomain / Eksternal)" />
                        <p class="mt-1 text-xs text-muted">Isi jika aplikasi diarahkan ke subdomain/URL khusus. Kosongkan jika menggunakan folder lokal <code class="font-mono">school-app/{{ $webApp->slug }}</code>.</p>
                        <x-text-input id="access_url" type="url" class="mt-1 form-input-minimal" name="access_url" :value="old('access_url', $webApp->access_url)" placeholder="https://cbt.veyracreative.site" />
                        <x-input-error :messages="$errors->get('access_url')" class="mt-2" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 form-input-minimal resize-none">{{ old('description', $webApp->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-group-minimal">
                            <x-input-label for="category" value="Kategori" />
                            <select id="category" name="category" class="mt-1 form-input-minimal appearance-none bg-white dark:bg-slate-800">
                                @foreach ($categories as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category', $webApp->category) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group-minimal">
                            <x-input-label for="icon" value="Ikon" />
                            <select id="icon" name="icon" class="mt-1 form-input-minimal appearance-none bg-white dark:bg-slate-800">
                                @foreach ($icons as $key => $label)
                                    <option value="{{ $key }}" @selected(old('icon', $webApp->icon) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="sort_order" value="Urutan tampilkan" />
                        <x-text-input id="sort_order" class="mt-1 form-input-minimal" name="sort_order" type="number" :value="old('sort_order', $webApp->sort_order ?? 0)" min="0" />
                        <p class="mt-1 text-xs text-muted">Angka lebih kecil akan ditampilkan lebih dulu.</p>
                    </div>

                    <fieldset class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                        <legend class="px-1 text-sm font-bold text-ink dark:text-slate-100">Akses Paket Langganan</legend>
                        <p class="mb-3 text-xs text-muted dark:text-slate-400">
                            Pilih paket yang mendapatkan akses ke aplikasi ini. Jika semua dikosongkan, aplikasi dapat diakses oleh semua paket aktif.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($plans as $plan)
                                <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-slate-200/80 dark:border-slate-700/60 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-xs font-medium text-ink dark:text-slate-200">
                                    <input
                                        type="checkbox"
                                        name="plan_ids[]"
                                        value="{{ $plan->id }}"
                                        class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400"
                                        @checked(in_array($plan->id, old('plan_ids', $webApp->plans->pluck('id')->toArray()), true))
                                    >
                                    <span class="font-bold">{{ $plan->label }}</span>
                                    <span class="text-[10px] text-muted ml-auto font-mono">({{ $plan->duration_label }})</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('admin.web-apps.show', $webApp->slug) }}" class="btn-minimal-secondary">Batal</a>
                        <x-primary-button class="btn-minimal-primary">Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
