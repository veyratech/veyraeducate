<x-admin-layout title="Sekolah — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Tenant</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Kelola Sekolah</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6" x-data="npsnLookup()">
            {{-- Toolbar: pencarian + tombol tambah --}}
            <form method="GET" action="{{ route('admin.schools.index') }}" class="flex gap-2 stagger-in" style="animation-delay: 60ms;">
                <div class="relative flex-1 max-w-md">
                    <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <x-text-input class="form-input-minimal !pl-11" type="search" name="q" :value="$search" placeholder="Cari nama, NPSN, kota…" />
                </div>
                <x-primary-button class="btn-minimal-primary whitespace-nowrap">Cari</x-primary-button>
                <button
                    type="button"
                    @click="open = true"
                    class="btn-minimal-primary whitespace-nowrap"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Sekolah
                </button>
                <a href="{{ route('admin.schools.import') }}" class="btn-minimal-secondary whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2 2H5z"/></svg>
                    Import CSV
                </a>
            </form>

            {{-- Popup Modal Tambah Sekolah --}}
            <div
                x-show="open"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
                role="dialog"
                aria-modal="true"
                @keydown.escape.window="open = false"
            >
                {{-- Backdrop Blur --}}
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
                    @click="open = false"
                ></div>

                {{-- Modal Card Content --}}
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="relative w-full max-w-2xl card-minimal p-6 sm:p-7 shadow-2xl z-10 my-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto"
                    @click.stop
                >
                    <div class="flex items-start justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">Tambah Sekolah Baru</h3>
                            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Isi NPSN (8 digit) atau NSM (10-13 digit) — data sekolah terisi otomatis.</p>
                        </div>
                        <button
                            type="button"
                            @click="open = false"
                            class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            aria-label="Tutup"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.schools.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="form-group-minimal sm:col-span-2 lg:col-span-3">
                                <x-input-label for="npsn" value="NPSN / NSM (Lookup Otomatis)" />
                                <x-text-input
                                    id="npsn"
                                    class="mt-1 form-input-minimal w-full"
                                    name="npsn"
                                    :value="old('npsn')"
                                    inputmode="numeric"
                                    maxlength="13"
                                    autocomplete="off"
                                    x-model="npsn"
                                    @blur.debounce.400ms="lookup()"
                                    @keyup.enter.prevent="lookup()"
                                    placeholder="NPSN (8 digit) atau NSM (10-13 digit)"
                                />
                                <p class="mt-1 text-xs" :class="statusClass" x-text="statusText"></p>
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="name" value="Nama sekolah" />
                                <x-text-input id="name" class="mt-1 form-input-minimal w-full" name="name" :value="old('name')" required x-model="name" placeholder="Nama sekolah" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="email" value="Email sekolah" />
                                <x-text-input id="email" class="mt-1 form-input-minimal w-full" type="email" name="email" :value="old('email')" required placeholder="sekolah@domain.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="phone" value="Telepon" />
                                <x-text-input id="phone" class="mt-1 form-input-minimal w-full" name="phone" :value="old('phone')" placeholder="021-1234567" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="city" value="Kota" />
                                <x-text-input id="city" class="mt-1 form-input-minimal w-full" name="city" :value="old('city')" x-model="city" placeholder="Jakarta" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="district" value="Kecamatan" />
                                <x-text-input id="district" class="mt-1 form-input-minimal w-full" name="district" :value="old('district')" x-model="district" placeholder="Kecamatan" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="postal_code" value="Kode Pos" />
                                <x-text-input id="postal_code" class="mt-1 form-input-minimal w-full" name="postal_code" :value="old('postal_code')" inputmode="numeric" maxlength="10" placeholder="12345" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="owner_name" value="Nama operator" />
                                <x-text-input id="owner_name" class="mt-1 form-input-minimal w-full" name="owner_name" :value="old('owner_name')" required placeholder="Nama operator" />
                                <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="owner_email" value="Email operator (login)" />
                                <x-text-input id="owner_email" class="mt-1 form-input-minimal w-full" type="email" name="owner_email" :value="old('owner_email')" required placeholder="operator@email.com" />
                                <x-input-error :messages="$errors->get('owner_email')" class="mt-2" />
                            </div>
                            <div class="form-group-minimal">
                                <x-input-label for="owner_password" value="Password operator" />
                                <x-text-input id="owner_password" class="mt-1 form-input-minimal w-full" type="password" name="owner_password" required placeholder="Minimal 8 karakter" />
                                <x-input-error :messages="$errors->get('owner_password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="open = false" class="btn-minimal-secondary text-sm">
                                Batal
                            </button>
                            <x-primary-button class="btn-minimal-primary text-sm">
                                Simpan Sekolah
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Schools Table --}}
            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 120ms;">
                <div class="overflow-x-auto">
                    <table class="table-minimal table-responsive-card">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Sekolah</th>
                                <th class="px-4 py-3">NPSN</th>
                                <th class="px-4 py-3">Kota</th>
                                <th class="px-4 py-3">Operator</th>
                                <th class="px-4 py-3">Langganan</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schools as $school)
                                @php
                                    $activeSub = $school->subscriptions
                                        ->first(fn ($s) => $s->status === \App\SubscriptionStatus::Active && $s->ends_at && $s->ends_at->isFuture());
                                @endphp
                                <tr>
                                    <td data-label="Sekolah" class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="avatar-minimal-sm avatar-minimal-brand">{{ strtoupper(substr($school->name, 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-ink dark:text-slate-100">{{ $school->name }}</p>
                                                <p class="truncate text-xs text-muted dark:text-slate-500">{{ $school->email }} &bull; {{ $school->users_count }} akun</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="NPSN" class="px-4 py-3 font-mono text-xs text-muted dark:text-slate-500">{{ $school->npsn ?: '—' }}</td>
                                    <td data-label="Kota" class="px-4 py-3 text-muted dark:text-slate-500">{{ $school->city ?: '—' }}</td>
                                    <td data-label="Operator" class="px-4 py-3">
                                        <div>
                                            <p class="text-ink dark:text-slate-200">{{ $school->owner?->name ?? '—' }}</p>
                                            <p class="text-xs text-muted dark:text-slate-500">{{ $school->owner?->email }}</p>
                                        </div>
                                    </td>
                                    <td data-label="Langganan" class="px-4 py-3">
                                        @if ($activeSub)
                                            <span class="badge-minimal badge-minimal-active">{{ $activeSub->plan?->label ?? 'Aktif' }}</span>
                                            <p class="text-[11px] text-muted dark:text-slate-500 mt-0.5">s/d {{ $activeSub->ends_at->format('d M Y') }}</p>
                                        @else
                                            <span class="badge-minimal badge-minimal-inactive">Tidak aktif</span>
                                        @endif
                                    </td>
                                    <td data-label="Aksi" class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.schools.show', $school) }}" class="btn-minimal-ghost text-xs px-3 py-1.5">Detail</a>
                                            <a href="{{ route('admin.schools.edit', $school) }}" class="btn-minimal-secondary text-xs px-3 py-1.5">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="empty-minimal py-4">
                                            <div class="empty-minimal-icon">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-16 0H3m2-9h.01M12 12h.01M17 12h.01M8 12H7.99M12 16h.01"/></svg>
                                            </div>
                                            <p class="empty-minimal-title">Belum ada sekolah</p>
                                            <p class="empty-minimal-desc">Klik tombol Tambah Sekolah untuk menambahkan data pertama</p>
                                            <button
                                                type="button"
                                                @click="open = true"
                                                class="mt-3 btn-minimal-primary text-xs"
                                            >
                                                + Tambah Sekolah Sekarang
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                    {{ $schools->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function npsnLookup() {
                return {
                    open: {{ $errors->any() || old('npsn') ? 'true' : 'false' }},
                    npsn: '{{ old('npsn') }}',
                    name: '{{ old('name') }}',
                    city: '{{ old('city') }}',
                    district: '{{ old('district') }}',
                    statusText: '',
                    statusClass: 'text-muted',
                    async lookup() {
                        const code = (this.npsn || '').replace(/\D/g, '');
                        this.npsn = code;

                        if (code.length < 8 || code.length > 13) {
                            this.statusText = '';
                            return;
                        }

                        this.statusText = 'Mencari data sekolah…';
                        this.statusClass = 'text-muted';

                        try {
                            const url = '{{ route('admin.schools.lookup-sekolah', ':code') }}'.replace(':code', code);
                            const res = await fetch(url);
                            const data = await res.json();

                            if (data.found) {
                                this.name = data.name || this.name;
                                this.city = data.city || this.city;
                                if (data.district) this.district = data.district;
                                // For NSM → show the linked NPSN if available
                                if (code.length >= 10 && data.npsn) {
                                    this.npsn = data.npsn;
                                }
                                const info = [data.level, data.status, data.lembaga].filter(Boolean).join(' · ');
                                const desa = data.village ? ' · Desa ' + data.village : '';
                                const sourceTag = data.source ? ' [' + data.source.toUpperCase() + ']' : '';
                                this.statusText = '✓ ' + (info ? info + desa + ' — ' : '') + 'data ditemukan' + sourceTag;
                                this.statusClass = 'text-brand-700 font-semibold dark:text-brand-400';
                            } else {
                                this.statusText = data.message || 'Data tidak ditemukan.';
                                this.statusClass = 'text-amber-700 dark:text-amber-400';
                            }
                        } catch (e) {
                            this.statusText = 'Lookup gagal — isi manual.';
                            this.statusClass = 'text-amber-700 dark:text-amber-400';
                        }
                    },
                };
            }
        </script>
    @endpush
</x-admin-layout>
