<x-admin-layout title="Database &amp; Cadangan Sistem — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Pemeliharaan &amp; Keamanan</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Database &amp; Cadangan Sistem (Backup)</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    Unduh file cadangan SQL lengkap (struktur &amp; data) untuk keamanan sistem atau migrasi hosting tanpa perlu akses SSH terminal.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('admin.backup.optimize') }}" onsubmit="return confirm('Optimasi tabel database sekarang? Sistem akan merapikan indeks dan membebaskan ruang kosong.');">
                    @csrf
                    <button type="submit" class="btn-minimal-secondary text-xs sm:text-sm inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Optimasi Tabel</span>
                    </button>
                </form>

                <a
                    href="{{ route('admin.backup.download') }}"
                    class="btn-minimal-primary text-xs sm:text-sm inline-flex items-center gap-1.5 shadow-sm"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Backup SQL (.sql)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site max-w-5xl space-y-6">

            {{-- Status Alerts --}}
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300 flex items-center gap-2">
                    <svg class="h-5 w-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="card-minimal p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted dark:text-slate-400">Mesin Database</p>
                    <p class="mt-2 text-2xl font-extrabold text-ink dark:text-slate-100 flex items-center gap-2">
                        <span class="inline-block h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="uppercase">{{ $driver }}</span>
                    </p>
                    <p class="mt-1 text-xs text-muted dark:text-slate-400 truncate">Basis Data: <code class="font-mono text-brand-600 dark:text-brand-400">{{ $databaseName }}</code></p>
                </div>

                <div class="card-minimal p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted dark:text-slate-400">Total Tabel Terdaftar</p>
                    <p class="mt-2 text-2xl font-extrabold text-ink dark:text-slate-100">
                        {{ number_format($totalTables, 0, ',', '.') }} <span class="text-xs font-medium text-muted">tabel</span>
                    </p>
                    <p class="mt-1 text-xs text-muted dark:text-slate-400">{{ number_format($totalRows, 0, ',', '.') }} total baris data</p>
                </div>

                <div class="card-minimal p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted dark:text-slate-400">Kapasitas Basis Data</p>
                    <p class="mt-2 text-2xl font-extrabold text-brand-700 dark:text-brand-400">
                        {{ $totalSize }}
                    </p>
                    <p class="mt-1 text-xs text-muted dark:text-slate-400">Data &amp; Indeks Keseluruhan</p>
                </div>
            </div>

            {{-- Cara Restore Guide --}}
            <div class="rounded-2xl border border-sky-200/80 bg-gradient-to-r from-sky-50 to-white p-5 dark:border-sky-900/50 dark:from-sky-950/30 dark:to-slate-900">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-ink dark:text-slate-100">
                            Petunjuk Restore &amp; Migrasi Hosting cPanel
                        </h3>
                        <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400">
                            File <code class="font-mono text-brand-700 dark:text-brand-300 font-semibold">.sql</code> yang diunduh adalah salinan standar SQL lengkap yang kompatibel dengan <strong>phpMyAdmin</strong> cPanel, Adminer, maupun CLI terminal. Anda cukup masuk ke menu <em>phpMyAdmin &rarr; Pilih Database &rarr; Tab Import &rarr; Pilih file SQL</em> untuk memulihkan seluruh data.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Table List Card --}}
            <div class="card-minimal overflow-hidden">
                <div class="border-b border-slate-100 dark:border-slate-800 p-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-ink dark:text-slate-100">Daftar Tabel Database Aplikasi</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">Rincian tabel terdaftar dan perkiraan jumlah baris.</p>
                    </div>
                    <a
                        href="{{ route('admin.backup.download') }}"
                        class="text-xs font-semibold text-brand-700 dark:text-brand-400 hover:underline inline-flex items-center gap-1"
                    >
                        <span>Unduh Sekarang</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40 text-muted dark:text-slate-400 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="py-3 px-4">Nama Tabel</th>
                                <th class="py-3 px-4 text-center">Jumlah Baris</th>
                                <th class="py-3 px-4 text-right">Ukuran Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($tables as $table)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 px-4 font-mono font-medium text-ink dark:text-slate-200">
                                        {{ $table['name'] }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ number_format($table['rows'], 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-muted dark:text-slate-400">
                                        {{ $table['size'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-muted dark:text-slate-400">
                                        Tidak ada tabel ditemukan dalam basis data ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
