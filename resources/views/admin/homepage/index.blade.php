<x-admin-layout title="Kelola Homepage — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Tampilan & Konten</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Kelola Container Homepage</h2>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ url('/') }}"
                    target="_blank"
                    class="btn-minimal-secondary text-sm inline-flex items-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Lihat Web Depan
                </a>
                <a
                    href="{{ route('admin.homepage.create') }}"
                    class="btn-minimal-primary text-sm inline-flex items-center gap-1.5"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Container Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">
            {{-- Flash status notification --}}
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            {{-- Guide banner --}}
            <div class="rounded-2xl border border-brand-200/70 bg-gradient-to-r from-brand-50 to-white p-5 dark:border-brand-900/50 dark:from-brand-950/30 dark:to-slate-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-ink dark:text-slate-100 flex items-center gap-2">
                        <span>💡</span> Pengaturan Fleksibel Halaman Utama
                    </h3>
                    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 max-w-2xl">
                        Semua blok konten di bawah dapat diedit teks dan gambarnya, diubah urutan letaknya menggunakan tombol panah, atau dinonaktifkan sementara tanpa harus dihapus.
                    </p>
                </div>
                <form action="{{ route('admin.homepage.reset-default') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan semua container ke pengaturan bawaan awal?');">
                    @csrf
                    <button type="submit" class="btn-minimal-ghost text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 shrink-0">
                        Pulihkan ke Bawaan
                    </button>
                </form>
            </div>

            {{-- Daftar Containers --}}
            <div class="divide-y divide-slate-200 dark:divide-slate-800 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                    <p class="text-sm font-bold text-ink dark:text-slate-100">Susunan Container Homepage</p>
                    <span class="text-xs text-muted">{{ $sections->count() }} container terdaftar</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($sections as $index => $section)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/30 {{ ! $section->is_active ? 'opacity-60 bg-slate-50/30 dark:bg-slate-900/30' : '' }}">
                            {{-- Sisi Kiri: Urutan & Info --}}
                            <div class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                                {{-- Tombol Up & Down --}}
                                <div class="flex flex-col items-center gap-1 shrink-0">
                                    <form action="{{ route('admin.homepage.move', $section) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button
                                            type="submit"
                                            class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200 transition-colors {{ $loop->first ? 'opacity-30 cursor-not-allowed' : '' }}"
                                            title="Pindahkan ke atas"
                                            {{ $loop->first ? 'disabled' : '' }}
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>
                                    </form>
                                    <span class="text-[11px] font-bold text-slate-500 font-mono">{{ $section->order_position }}</span>
                                    <form action="{{ route('admin.homepage.move', $section) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button
                                            type="submit"
                                            class="rounded p-1 text-slate-400 hover:bg-slate-200 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200 transition-colors {{ $loop->last ? 'opacity-30 cursor-not-allowed' : '' }}"
                                            title="Pindahkan ke bawah"
                                            {{ $loop->last ? 'disabled' : '' }}
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Detail Section --}}
                                <div class="min-w-0 flex-1 space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-bold text-ink dark:text-slate-100 truncate">
                                            {{ $section->title }}
                                        </span>

                                        {{-- Badge Type --}}
                                        @php
                                            $typeBadgeColor = match($section->section_type) {
                                                'hero' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300',
                                                'features' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                                                'workflow' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-300',
                                                'ecosystem' => 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300',
                                                'pricing' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                                'faq' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
                                                'cta' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                                'custom_banner' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                                                'custom_content' => 'bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300',
                                                'testimonials' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                                'app_showcase' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
                                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold {{ $typeBadgeColor }}">
                                            {{ $section->section_type_label }}
                                        </span>

                                        @if ($section->hasCustomBackground())
                                            <span class="inline-flex items-center rounded-md bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 px-2 py-0.5 text-[10px] font-medium border border-slate-200 dark:border-slate-700">
                                                {{ $section->getBgLabel() }}
                                            </span>
                                        @endif

                                        @if (! $section->is_active)
                                            <span class="inline-flex items-center rounded-md bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 text-[10px] font-medium text-slate-700 dark:text-slate-300">
                                                Nonaktif (Disembunyikan)
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-muted truncate">
                                        @if ($section->section_type === 'hero')
                                            {{ $section->field('lead') }} — {{ Str::limit($section->field('description'), 50) }}
                                        @elseif ($section->section_type === 'custom_banner')
                                            {{ $section->field('title') }} (Tema: {{ $section->field('theme', 'brand') }})
                                        @elseif ($section->section_type === 'custom_content')
                                            {{ $section->field('title') }} — {{ Str::limit(strip_tags($section->field('body_html')), 60) }}
                                        @elseif ($section->section_type === 'testimonials')
                                            {{ count($section->field('items', $section->field('testimonials', []))) }} ulasan sekolah terdaftar — &ldquo;{{ Str::limit($section->field('items.0.quote', 'Ulasan sekolah mitra'), 50) }}&rdquo;
                                        @elseif ($section->section_type === 'app_showcase')
                                            {{ count($section->field('items', [])) }} aplikasi terdaftar (Mode: {{ $section->field('source_mode', 'custom') === 'database' ? 'Otomatis DB' : 'Katalog Khusus' }}) — {{ Str::limit($section->field('title'), 50) }}
                                        @else
                                            {{ $section->field('title') ?? 'Pengaturan konten terpasang' }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Action Buttons --}}
                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                {{-- Quick Toggle Active --}}
                                <form action="{{ route('admin.homepage.toggle', $section) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition-colors {{ $section->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400' }}"
                                        title="{{ $section->is_active ? 'Klik untuk sembunyikan' : 'Klik untuk aktifkan' }}"
                                    >
                                        <span class="h-2 w-2 rounded-full {{ $section->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $section->is_active ? 'Aktif' : 'Mati' }}
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <a
                                    href="{{ route('admin.homepage.edit', $section) }}"
                                    class="btn-minimal-secondary text-xs py-1.5 px-3"
                                >
                                    Edit Isi
                                </a>

                                {{-- Delete Button (for custom or any section with prompt) --}}
                                @if (str_starts_with($section->key ?? '', 'custom_'))
                                    <form action="{{ route('admin.homepage.destroy', $section) }}" method="POST" onsubmit="return confirm('Hapus container ini secara permanen dari homepage?');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn-minimal-ghost text-xs p-1.5 text-red-600 hover:text-red-700 dark:text-red-400"
                                            title="Hapus container"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-sm text-muted">Belum ada container yang tersimpan.</p>
                            <form action="{{ route('admin.homepage.reset-default') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="btn-minimal-primary text-xs">
                                    Muat Container Bawaan
                                </button>
                            </form>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
