<x-admin-layout title="Buat paket baru — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div>
            <p class="eyebrow">Tambah paket</p>
            <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Buat Paket Baru</h2>
            <p class="mt-1 text-sm text-muted dark:text-slate-400"><a href="{{ route('admin.plans.index') }}" class="link-muted">← Semua paket</a></p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-3xl">
            <form method="POST" action="{{ route('admin.plans.store') }}" x-data="{ isFeatured: true }" :class="{ 'ring-2 ring-gold-400/60 bg-amber-50 dark:bg-amber-950/20 dark:ring-gold-500/40': isFeatured }" class="card-minimal space-y-5 p-6 stagger-in">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="code" value="Kode paket" />
                         <x-text-input id="code" class="mt-1 form-input-minimal" name="code" :value="old('code')" required placeholder="contoh: bulanan" pattern="[a-z0-9\-]+" title="Hanya huruf kecil, angka, dan strip" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-1">
                        <x-input-label for="sort_order" value="Urutan" />
                         <x-text-input id="sort_order" class="mt-1 form-input-minimal" type="number" name="sort_order" :value="old('sort_order', 0)" required min="0" />
                        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="label" value="Label" />
                         <x-text-input id="label" class="mt-1 form-input-minimal" name="label" :value="old('label')" required />
                        <x-input-error :messages="$errors->get('label')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="description" value="Deskripsi" />
                         <x-text-input id="description" class="mt-1 form-input-minimal" name="description" :value="old('description')" />
                    </div>

                    <div>
                        <x-input-label for="price_idr" value="Harga (Rp)" />
                         <x-text-input id="price_idr" class="mt-1 form-input-minimal" type="number" name="price_idr" :value="old('price_idr')" required min="0" />
                        <x-input-error :messages="$errors->get('price_idr')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="duration_months" value="Durasi (bulan)" />
                         <x-text-input id="duration_months" class="mt-1 form-input-minimal" type="number" name="duration_months" :value="old('duration_months')" required min="1" />
                        <x-input-error :messages="$errors->get('duration_months')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="duration_label" value="Label durasi" />
                         <x-text-input id="duration_label" class="mt-1 form-input-minimal" name="duration_label" :value="old('duration_label')" required />
                    </div>

                    <div>
                        <x-input-label for="badge" value="Badge" />
                         <x-text-input id="badge" class="mt-1 form-input-minimal" name="badge" :value="old('badge')" />
                    </div>
                </div>

                <div class="grid gap-2 sm:grid-cols-2">
                     <label class="flex items-center gap-2 text-sm text-ink dark:text-slate-200">
                         <input type="hidden" name="is_active" value="0">
                         <input type="checkbox" name="is_active" value="1" class="rounded border-brand-200 dark:border-slate-600 text-brand-700 dark:text-brand-400 focus:ring-brand-500" @checked(old('is_active', true))>
                         Aktif dijual
                     </label>
                     <label class="flex items-center gap-2 text-sm text-ink dark:text-slate-200">
                         <input type="hidden" name="is_featured" value="0">
                         <input type="checkbox" name="is_featured" value="1" class="rounded border-brand-200 dark:border-slate-600 text-brand-700 dark:text-brand-400 focus:ring-brand-500" @checked(old('is_featured', true)) x-model="isFeatured">
                         Featured (highlight)
                     </label>
                     <label class="flex items-center gap-2 text-sm text-ink dark:text-slate-200">
                         <input type="hidden" name="is_default" value="0">
                         <input type="checkbox" name="is_default" value="1" class="rounded border-brand-200 dark:border-slate-600 text-brand-700 dark:text-brand-400 focus:ring-brand-500" @checked(old('is_default', true))>
                         Default (paket utama)
                     </label>
                </div>

                     <fieldset class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                         <legend class="px-1 text-sm font-bold text-ink dark:text-slate-100">Fitur (list)</legend>
                    <div x-data="{{ json_encode(['features' => array_values(old('features', ['']))]) }}" class="space-y-2">
                        <template x-for="(feature, index) in features" :key="index">
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    :name="'features[' + index + ']'"
                                    :value="feature"
                                    :placeholder="'Fitur nomor ' + (index + 1)"
                                    x-model="features[index]"
                                     class="form-input-minimal"
                                >
                                <button
                                    type="button"
                                    x-show="features.length > 1"
                                    @click="features.splice(index, 1)"
                                     class="btn-minimal-ghost mt-0 shrink-0 p-2 text-red-600 hover:text-red-700 dark:text-red-400"
                                    aria-label="Hapus fitur"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <button
                            type="button"
                            @click="features.push('')"
                             class="mt-1 text-sm font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300"
                        >
                            + Tambah fitur
                        </button>
                    </div>
                </fieldset>

                <fieldset class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                    <legend class="px-1 text-sm font-bold text-ink dark:text-slate-100">Aplikasi yang Termasuk dalam Paket Ini</legend>
                    <p class="mb-3 text-xs text-muted dark:text-slate-400">
                        Centang aplikasi yang otomatis dapat diakses oleh sekolah yang berlangganan paket ini.
                    </p>
                    @if ($webApps->isEmpty())
                        <p class="text-xs text-muted italic">Belum ada aplikasi yang terdaftar.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($webApps as $app)
                                <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-slate-200/80 dark:border-slate-700/60 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-xs font-medium text-ink dark:text-slate-200">
                                    <input
                                        type="checkbox"
                                        name="web_app_ids[]"
                                        value="{{ $app->id }}"
                                        class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400"
                                        @checked(in_array($app->id, old('web_app_ids', []), true))
                                    >
                                    <span class="font-bold">{{ $app->name }}</span>
                                    <span class="text-[10px] text-muted ml-auto font-mono">/{{ $app->slug }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </fieldset>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.plans.index') }}" class="btn-minimal-secondary">Batal</a>
                    <x-primary-button class="btn-minimal-primary">Simpan paket</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
