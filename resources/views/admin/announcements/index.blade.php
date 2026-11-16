<x-admin-layout title="Pengumuman — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <p class="eyebrow">Pengumuman</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Daftar Pengumuman</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ modalOpen: {{ $errors->any() ? 'true' : 'false' }}, broadcastModalOpen: false }">
        <div class="container-site space-y-5">
            {{-- Filter Tabs + Search + Add Button --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto">
                    <span class="text-xs font-semibold text-muted uppercase tracking-wider shrink-0">Filter:</span>
                    <div class="inline-flex flex-wrap gap-1 p-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                        @php
                            $tabs = [
                                '' => ['label' => 'Semua', 'count' => $counts['all'] ?? count($announcements), 'color' => ''],
                                'active' => ['label' => 'Tayang', 'count' => $counts['active'] ?? 0, 'color' => 'text-emerald-600 dark:text-emerald-400'],
                                'scheduled' => ['label' => 'Terjadwal', 'count' => $counts['scheduled'] ?? 0, 'color' => 'text-amber-600 dark:text-amber-400'],
                                'draft' => ['label' => 'Draft', 'count' => $counts['draft'] ?? 0, 'color' => 'text-slate-500 dark:text-slate-400'],
                            ];
                        @endphp
                        @foreach ($tabs as $key => $tab)
                            <a href="{{ route('admin.announcements.index', array_filter(['status' => $key, 'q' => $search ?? ''])) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all
                               {{ ($status ?? '') === $key
                                    ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-sm'
                                    : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                                {{ $tab['label'] }}
                                <span class="{{ ($status ?? '') === $key ? 'text-white/70 dark:text-slate-500' : $tab['color'] }} tab-count">
                                    {{ $tab['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('admin.announcements.index') }}" class="flex items-center gap-2 flex-1 sm:flex-initial">
                        @if (!empty($status))
                            <input type="hidden" name="status" value="{{ $status }}" />
                        @endif
                        <div class="relative flex-1 sm:w-56">
                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <x-text-input class="form-input-minimal !pl-10 text-xs" type="search" name="q" :value="$search ?? ''" placeholder="Cari pengumuman…" />
                        </div>
                        <x-primary-button class="btn-minimal-secondary text-xs whitespace-nowrap">Cari</x-primary-button>
                    </form>
                    <button
                        type="button"
                        @click="modalOpen = true"
                        class="btn-minimal-primary text-xs whitespace-nowrap inline-flex items-center gap-1.5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah
                    </button>
                    <button
                        type="button"
                        @click="broadcastModalOpen = true"
                        class="btn-minimal-secondary text-xs whitespace-nowrap inline-flex items-center gap-1.5"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                        Kirim broadcast
                    </button>
                </div>
            </div>

            {{-- Popup Modal Tambah Pengumuman --}}
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
                    class="relative w-full max-w-xl card-minimal p-6 sm:p-7 shadow-2xl z-10 my-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto"
                    @click.stop
                >
                    <div class="flex items-start justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">Tambah Pengumuman Baru</h3>
                            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Publikasikan pengumuman atau informasi baru untuk tenant sekolah.</p>
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

                    <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4">
                        @csrf

                        <div class="form-group-minimal">
                            <x-input-label for="announcement_title" value="Judul Pengumuman" />
                            <x-text-input id="announcement_title" class="mt-1 form-input-minimal w-full" name="title" :value="old('title')" required maxlength="255" placeholder="Contoh: Pembaruan Sistem dan Fitur Baru" />
                            <x-input-error :messages="$errors->get('title')" class="mt-1" />
                        </div>

                        <div class="form-group-minimal">
                            <x-input-label for="announcement_body" value="Isi Pengumuman" />
                            <textarea
                                id="announcement_body"
                                name="body"
                                rows="5"
                                required
                                class="mt-1 form-input-minimal w-full resize-none"
                                placeholder="Tulis isi pengumuman secara rinci di sini..."
                            >{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1" />
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="form-group-minimal">
                                <x-input-label for="announcement_published_at" value="Tanggal Terbit" />
                                <input
                                    id="announcement_published_at"
                                    name="published_at"
                                    type="datetime-local"
                                    value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                    class="mt-1 form-input-minimal w-full"
                                >
                                <x-input-error :messages="$errors->get('published_at')" class="mt-1" />
                            </div>
                            <div class="flex items-center pt-6">
                                <label for="announcement_is_active" class="flex cursor-pointer items-center gap-2">
                                    <input
                                        id="announcement_is_active"
                                        name="is_active"
                                        type="checkbox"
                                        value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500"
                                    >
                                    <span class="text-xs font-semibold text-ink dark:text-slate-200">Publikasikan sekarang</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="modalOpen = false" class="btn-minimal-secondary text-sm">
                                Batal
                            </button>
                            <x-primary-button class="btn-minimal-primary text-sm">
                                Buat Pengumuman
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Popup Modal Broadcast ke Semua Sekolah --}}
            <div
                x-show="broadcastModalOpen"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
                role="dialog"
                aria-modal="true"
                @keydown.escape.window="broadcastModalOpen = false"
            >
                <div
                    x-show="broadcastModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
                    @click="broadcastModalOpen = false"
                ></div>

                <div
                    x-show="broadcastModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="relative w-full max-w-xl card-minimal p-6 sm:p-7 shadow-2xl z-10 my-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto"
                    @click.stop
                >
                    <div class="flex items-start justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">Kirim Pengumuman ke Semua Sekolah</h3>
                            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Pengumuman ini akan ditampilkan sebagai notifikasi kepada seluruh sekolah aktif di platform.</p>
                        </div>
                        <button
                            type="button"
                            @click="broadcastModalOpen = false"
                            class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            aria-label="Tutup"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.announcements.broadcast') }}" class="space-y-4">
                        @csrf

                        <div class="form-group-minimal">
                            <x-input-label for="broadcast_title" value="Judul Pengumuman" />
                            <x-text-input id="broadcast_title" class="mt-1 form-input-minimal w-full" name="title" :value="old('title')" required maxlength="255" placeholder="Contoh: Pembaruan Sistem dan Fitur Baru" />
                            <x-input-error :messages="$errors->get('title')" class="mt-1" />
                        </div>

                        <div class="form-group-minimal">
                            <x-input-label for="broadcast_body" value="Isi Pengumuman" />
                            <textarea
                                id="broadcast_body"
                                name="body"
                                rows="5"
                                required
                                class="mt-1 form-input-minimal w-full resize-none"
                                placeholder="Tulis isi pengumuman secara rinci di sini..."
                            >{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1" />
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="form-group-minimal">
                                <x-input-label for="broadcast_published_at" value="Tanggal Terbit" />
                                <input
                                    id="broadcast_published_at"
                                    name="published_at"
                                    type="datetime-local"
                                    value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                    class="mt-1 form-input-minimal w-full"
                                >
                                <x-input-error :messages="$errors->get('published_at')" class="mt-1" />
                            </div>
                            <div class="flex items-center pt-6">
                                <label for="broadcast_is_active" class="flex cursor-pointer items-center gap-2">
                                    <input
                                        id="broadcast_is_active"
                                        name="is_active"
                                        type="checkbox"
                                        value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500"
                                    >
                                    <span class="text-xs font-semibold text-ink dark:text-slate-200">Publikasikan sekarang</span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 px-4 py-3">
                            <p class="text-xs text-amber-800 dark:text-amber-300">
                                <strong>Perhatian:</strong> Tindakan ini akan mengirimkan notifikasi pengumuman ke seluruh sekolah yang terdaftar dan aktif di sistem. Pastikan konten sudah benar sebelum dikirim.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="broadcastModalOpen = false" class="btn-minimal-secondary text-sm">
                                Batal
                            </button>
                            <x-primary-button class="btn-minimal-primary text-sm inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                Kirim ke Semua Sekolah
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($announcements as $announcement)
                    @php
                        $isScheduled = $announcement->is_active && $announcement->published_at && $announcement->published_at->isFuture();
                        $isLive = $announcement->is_active && ! $isScheduled;
                    @endphp
                    <article class="card-minimal overflow-hidden stagger-in flex flex-col">
                        <div class="px-5 py-4 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-bold text-ink dark:text-slate-100 truncate">{{ $announcement->title }}</h3>
                                        @if ($isLive)
                                            <span class="badge-minimal shrink-0 badge-minimal-active">Tayang</span>
                                        @elseif ($isScheduled)
                                            <span class="badge-minimal shrink-0 badge-minimal-pending">Terjadwal</span>
                                        @else
                                            <span class="badge-minimal shrink-0 badge-minimal-inactive">Draft</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 truncate text-xs text-muted dark:text-slate-500">
                                        {{ optional($announcement->published_at)->format('d M Y, H:i') ?? 'Belum dijadwalkan' }}
                                    </p>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-muted dark:text-slate-400 whitespace-pre-line line-clamp-3">{{ Str::limit($announcement->body, 200) }}</p>
                        </div>
                        <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3 flex items-center justify-between gap-2">
                            <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-minimal-ghost text-xs py-1.5 px-2.5">
                                    {{ $announcement->is_active ? 'Nonaktifkan' : 'Publikasikan' }}
                                </button>
                            </form>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn-minimal-secondary text-xs py-1.5 px-3">Edit</a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}"
                                      onsubmit="return confirm('Hapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-minimal-danger text-xs py-1.5 px-3">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-minimal stagger-in col-span-full py-10">
                        <div class="empty-minimal-icon">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <p class="empty-minimal-title">Belum ada pengumuman.</p>
                        <p class="empty-minimal-desc">
                            <button type="button" @click="modalOpen = true" class="font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
                                + Buat pengumuman pertama →
                            </button>
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
