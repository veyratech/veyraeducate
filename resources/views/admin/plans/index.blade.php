<x-admin-layout title="Paket — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Langganan</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Paket Langganan</h2>
            </div>
        </div>
    </x-slot>

    <script>
        function planModal() {
            return {
                modalOpen: {{ $errors->any() ? 'true' : 'false' }},
                features: {!! json_encode(array_values(old('features', ['']))) !!},
                addFeature() {
                    this.features.push('');
                },
                removeFeature(index) {
                    if (this.features.length > 1) {
                        this.features.splice(index, 1);
                    }
                }
            };
        }
    </script>

    <div class="py-8" x-data="planModal()">
        <div class="container-site space-y-6">
            @if (session('status'))
                <div class="alert-success stagger-in">
                    <p class="text-xs font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            <div class="flex justify-end">
                <button
                    type="button"
                    @click="modalOpen = true"
                    class="btn-minimal-primary text-sm py-2 px-4"
                >
                    + Tambah paket
                </button>
            </div>

            {{-- Popup Modal Tambah Paket --}}
            <div
                x-show="modalOpen"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
                role="dialog"
                aria-modal="true"
                @keydown.escape.window="modalOpen = false"
            >
                {{-- Backdrop Blur --}}
                <div
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
                    @click="modalOpen = false"
                ></div>

                {{-- Modal Card Content --}}
                <div
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="relative w-full max-w-2xl card-minimal p-4 sm:p-7 shadow-2xl z-10 my-4 sm:my-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 max-h-[92vh] overflow-y-auto"
                    @click.stop
                >
                    <div class="flex items-start justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">Buat Paket Baru</h3>
                            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Atur paket langganan sekolah dan modul aplikasi yang dapat diakses.</p>
                        </div>
                        <button
                            type="button"
                            @click="modalOpen = false"
                            class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            aria-label="Tutup"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-4">
                        @csrf

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="form-group-minimal">
                                <x-input-label for="plan_code" value="Kode paket" />
                                <x-text-input id="plan_code" class="mt-1 form-input-minimal w-full" name="code" :value="old('code')" required placeholder="contoh: bulanan" pattern="[a-z0-9\-]+" title="Hanya huruf kecil, angka, dan strip" />
                                <x-input-error :messages="$errors->get('code')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="plan_sort_order" value="Urutan Tampil" />
                                <x-text-input id="plan_sort_order" class="mt-1 form-input-minimal w-full" type="number" name="sort_order" :value="old('sort_order', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('sort_order')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="form-group-minimal sm:col-span-2">
                                <x-input-label for="plan_label" value="Label / Nama Paket" />
                                <x-text-input id="plan_label" class="mt-1 form-input-minimal w-full" name="label" :value="old('label')" required placeholder="Contoh: Paket 1 Tahun" />
                                <x-input-error :messages="$errors->get('label')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal sm:col-span-2">
                                <x-input-label for="plan_description" value="Deskripsi Singkat" />
                                <x-text-input id="plan_description" class="mt-1 form-input-minimal w-full" name="description" :value="old('description')" placeholder="Deskripsi paket untuk sekolah..." />
                                <x-input-error :messages="$errors->get('description')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="plan_price_idr" value="Harga (Rp)" />
                                <x-text-input id="plan_price_idr" class="mt-1 form-input-minimal w-full" type="number" name="price_idr" :value="old('price_idr')" required min="0" placeholder="1000000" />
                                <x-input-error :messages="$errors->get('price_idr')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="plan_duration_months" value="Durasi (bulan)" />
                                <x-text-input id="plan_duration_months" class="mt-1 form-input-minimal w-full" type="number" name="duration_months" :value="old('duration_months', 12)" required min="1" />
                                <x-input-error :messages="$errors->get('duration_months')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="plan_duration_label" value="Label Durasi" />
                                <x-text-input id="plan_duration_label" class="mt-1 form-input-minimal w-full" name="duration_label" :value="old('duration_label', '1 Tahun (12 bulan)')" required />
                                <x-input-error :messages="$errors->get('duration_label')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="plan_badge" value="Badge (Opsional)" />
                                <x-text-input id="plan_badge" class="mt-1 form-input-minimal w-full" name="badge" :value="old('badge')" placeholder="Populer / Terbaik" />
                                <x-input-error :messages="$errors->get('badge')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-3 pt-1">
                            <label class="flex items-center gap-2 text-xs text-ink dark:text-slate-200 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-brand-200 dark:border-slate-600 text-brand-700 dark:text-brand-400 focus:ring-brand-500" @checked(old('is_active', true))>
                                <span>Aktif dijual</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-ink dark:text-slate-200 cursor-pointer">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1" class="rounded border-brand-200 dark:border-slate-600 text-brand-700 dark:text-brand-400 focus:ring-brand-500" @checked(old('is_featured', false))>
                                <span>Featured (highlight)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-ink dark:text-slate-200 cursor-pointer">
                                <input type="hidden" name="is_default" value="0">
                                <input type="checkbox" name="is_default" value="1" class="rounded border-brand-200 dark:border-slate-600 text-brand-700 dark:text-brand-400 focus:ring-brand-500" @checked(old('is_default', false))>
                                <span>Default</span>
                            </label>
                        </div>

                        <fieldset class="rounded-lg border border-slate-200 p-3.5 dark:border-slate-800">
                            <legend class="px-1 text-xs font-bold text-ink dark:text-slate-100">Fitur Paket (Daftar Poin)</legend>
                            <div class="space-y-2">
                                <template x-for="(feature, index) in features" :key="index">
                                    <div class="flex gap-2">
                                        <input
                                            type="text"
                                            :name="'features[' + index + ']'"
                                            :value="feature"
                                            :placeholder="'Fitur nomor ' + (index + 1)"
                                            x-model="features[index]"
                                            class="form-input-minimal w-full"
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
                                    class="mt-1 text-xs font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300"
                                >
                                    + Tambah poin fitur
                                </button>
                            </div>
                        </fieldset>

                        @if (isset($webApps) && $webApps->isNotEmpty())
                            <fieldset class="rounded-lg border border-slate-200 p-3.5 dark:border-slate-800">
                                <legend class="px-1 text-xs font-bold text-ink dark:text-slate-100">Aplikasi yang Termasuk</legend>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                    @foreach ($webApps as $app)
                                        <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200/80 dark:border-slate-700/60 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-xs font-medium text-ink dark:text-slate-200">
                                            <input
                                                type="checkbox"
                                                name="web_app_ids[]"
                                                value="{{ $app->id }}"
                                                class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400"
                                                @checked(in_array($app->id, old('web_app_ids', []), true))
                                            >
                                            <span class="font-bold truncate">{{ $app->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="modalOpen = false" class="btn-minimal-secondary text-sm">
                                Batal
                            </button>
                            <x-primary-button class="btn-minimal-primary text-sm">
                                Simpan Paket
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Plans Grid - Bento Style --}}
            <div class="bento-grid stagger-in">
                @foreach ($plans as $plan)
                    <article class="card-minimal-interactive p-5 bento-item {{ !$plan->is_active ? 'opacity-70' : '' }}">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <p class="eyebrow">{{ $plan->code }}</p>
                                <h3 class="mt-1 text-lg font-bold text-ink dark:text-slate-100 truncate">{{ $plan->label }}</h3>
                            </div>
@if ($plan->is_default)
                                <span class="badge-minimal badge-minimal-green shrink-0 whitespace-nowrap">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Default
                                </span>
                            @endif
                            @if ($plan->is_featured)
                                <span class="badge-minimal badge-minimal-gold shrink-0 whitespace-nowrap">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364 1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Unggulan
                                </span>
                            @endif
                        </div>

                        @if (!$plan->is_active)
                            <span class="badge-minimal badge-minimal-inactive mb-3 inline-block">Nonaktif</span>
                        @endif

                        <div class="mb-4">
                            <p class="text-3xl font-extrabold text-ink dark:text-slate-100">{{ $plan->formatPrice() }}</p>
                            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">{{ $plan->duration_label }}</p>
                        </div>

                        @if ($plan->description)
                            <p class="text-xs leading-relaxed text-muted dark:text-slate-400 mb-4">{{ $plan->description }}</p>
                        @endif

                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 text-xs text-muted dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-slate-100 dark:border-slate-800">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>{{ $plan->webApps->count() > 0 ? $plan->webApps->count() . ' aplikasi terhubung' : 'Akses semua aplikasi (bebas)' }}</span>
                            </span>
                            <span class="rounded-full bg-brand-100/80 dark:bg-brand-950/60 px-2 py-0.5 text-[10px] font-bold text-brand-800 dark:text-brand-300">
                                {{ $plan->active_subscriptions_count ?? 0 }} sekolah aktif
                            </span>
                        </div>

                        <ul class="space-y-2 mb-4 text-xs text-muted dark:text-slate-400">
                            @foreach (array_slice($plan->features ?? [], 0, 5) as $feature)
                                <li class="flex gap-2">
                                    <span class="text-slate-400 shrink-0 dark:text-slate-500">✓</span>
                                    <span class="truncate">{{ $feature }}</span>
                                </li>
                            @endforeach
                            @if (count($plan->features ?? []) > 5)
                                <li class="flex gap-2 text-slate-500 dark:text-slate-400">
                                    <span>+{{ count($plan->features) - 5 }} fitur lainnya</span>
                                </li>
                            @endif
                        </ul>

                        <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn-minimal-secondary flex-1 text-center text-sm py-2">Edit Paket</a>
                            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket \'{{ addslashes($plan->label) }}\'? Tindakan ini tidak dapat dibatalkan.');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 dark:hover:text-red-400 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-red-300 dark:hover:border-red-900 transition flex items-center justify-center shrink-0" title="Hapus Paket '{{ $plan->label }}'" aria-label="Hapus Paket">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($plans->isEmpty())
                <div class="empty-minimal stagger-in">
                    <div class="empty-minimal-icon">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="empty-minimal-title">Belum ada paket</p>
                    <p class="empty-minimal-desc">Buat paket langganan pertama untuk memulai</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>