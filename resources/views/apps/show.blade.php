<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Aplikasi</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-ink">{{ $webApp->name }}</h2>
            </div>
            <a href="{{ route('apps.index') }}" class="btn-secondary">← Daftar Aplikasi</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-3xl space-y-6">

            @if (session('status'))
                <div class="rounded-xl border border-brand-200 bg-brand-50 px-5 py-4 dark:border-slate-700/50 dark:bg-slate-700/30">
                    <p class="text-sm font-semibold text-brand-800 dark:text-brand-300">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Main Card --}}
            <div class="card p-6 dark:bg-slate-850 dark:border-slate-700/50">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-11.36 0V21m0 0h12M4 9.349a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v9.652M12 9.349a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v9.652M20 12v4.5" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-bold text-ink dark:text-slate-100">{{ $webApp->name }}</h3>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold
                                {{ $deployed && $webApp->enabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-400' }}">
                                {{ ! $deployed ? 'Belum tersedia' : ($webApp->enabled ? 'Tersedia' : 'Nonaktif') }}
                            </span>
                        </div>
                        <p class="text-xs text-muted font-mono dark:text-slate-500 mt-1">/{{ $webApp->slug }}</p>
                    </div>
                </div>

                @if ($webApp->description)
                    <p class="mt-4 text-sm text-muted leading-relaxed">{{ $webApp->description }}</p>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="rounded-full bg-surface-sunken dark:bg-slate-700/50 px-3 py-1 text-[11px] font-medium text-muted dark:text-slate-400">
                        {{ $webApp->category ?? 'Umum' }}
                    </span>
                    @if ($webApp->version)
                        <span class="text-[11px] text-muted dark:text-slate-500">v{{ $webApp->version }}</span>
                    @endif
                </div>
            </div>

            {{-- Access Card --}}
            <div class="card p-6 dark:bg-slate-850 dark:border-slate-700/50">
                <p class="text-sm font-bold text-ink dark:text-slate-100 mb-3">Akses Aplikasi</p>
                
                @if (! $deployed)
                    <div class="rounded-lg border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-4 text-center">
                        <p class="text-sm text-muted dark:text-slate-400">Aplikasi ini belum tersedia</p>
                        <p class="text-xs text-muted mt-1 dark:text-slate-500">Aplikasi sedang disiapkan dan belum bisa dibuka.</p>
                    </div>
                @elseif (! $webApp->enabled)
                    <div class="rounded-lg border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-4 text-center">
                        <p class="text-sm text-muted dark:text-slate-400">Aplikasi ini sedang dalam pemeliharaan</p>
                        <p class="text-xs text-muted mt-1 dark:text-slate-500">Silakan coba lagi nanti</p>
                    </div>
                @elseif ($webApp->access_url || $onDisk)
                    <a href="{{ $webApp->accessLink() }}" target="_blank" rel="noopener noreferrer" class="btn-primary block w-full text-center">
                        <svg class="h-4 w-4 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Buka Aplikasi
                    </a>
                @else
                    <div class="rounded-lg border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-4 text-center">
                        <p class="text-sm text-muted dark:text-slate-400">Aplikasi ini sedang dalam pemeliharaan</p>
                        <p class="text-xs text-muted mt-1 dark:text-slate-500">Silakan coba lagi nanti</p>
                    </div>
                @endif

                <p class="mt-3 text-xs text-muted dark:text-slate-400">
                    <svg class="h-3 w-3 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aplikasi ini tersedia untuk sekolah dengan langganan aktif.
                </p>
            </div>

            {{-- Info Card --}}
            <div class="card p-6 dark:bg-slate-850 dark:border-slate-700/50">
                <p class="text-sm font-bold text-ink dark:text-slate-100 mb-3">Informasi Aplikasi</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-slate-400">Slug</span>
                        <span class="font-mono text-ink dark:text-slate-100">{{ $webApp->slug }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-slate-400">Kategori</span>
                        <span class="text-ink dark:text-slate-100">{{ $webApp->category ?? 'Umum' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted dark:text-slate-400">Terakhir diperbarui</span>
                        <span class="text-ink dark:text-slate-100">{{ $webApp->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Buku Panduan Terkait --}}
            <div class="card p-6 dark:bg-slate-850 dark:border-slate-700/50">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-sm font-bold text-ink dark:text-slate-100">📖 Buku Panduan Penggunaan</p>
                    <a href="{{ route('guides.index', ['app' => $webApp->id]) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400">
                        Lihat Semua Panduan →
                    </a>
                </div>
                @if (isset($guides) && $guides->isNotEmpty())
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($guides as $guide)
                            <div class="py-3 flex flex-wrap items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-ink dark:text-slate-100">{{ $guide->title }}</p>
                                    @if ($guide->description)
                                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">{{ $guide->description }}</p>
                                    @endif
                                    <p class="text-[11px] text-muted dark:text-slate-500 mt-0.5">
                                        {{ strtoupper($guide->file_type ?? 'PDF') }} · {{ $guide->formatFileSize() }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if (in_array(strtolower((string) $guide->file_type), ['pdf', 'txt'], true))
                                        <a href="{{ route('guides.preview', $guide) }}" target="_blank" rel="noopener" class="btn-secondary text-xs py-1.5 px-3">
                                            Baca
                                        </a>
                                    @endif
                                    <a href="{{ route('guides.download', $guide) }}" class="btn-primary text-xs py-1.5 px-3 !text-white" style="color: #ffffff !important;">
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-muted dark:text-slate-400">
                        Belum ada dokumen panduan khusus untuk aplikasi ini. Anda dapat melihat panduan umum di halaman <a href="{{ route('guides.index') }}" class="font-semibold text-brand-700 dark:text-brand-400 underline">Buku Panduan</a>.
                    </p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
