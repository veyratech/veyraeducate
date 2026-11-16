<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg sm:text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
                App Hub
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Wadah Aplikasi & Ekosistem Digital Sekolah</p>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="container-site space-y-6">
            {{-- Action & Info Bar Directly Above Grid --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-850 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100">Katalog Aplikasi</h3>
                        <span class="rounded-full bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 text-xs px-2.5 py-0.5 font-bold">
                            {{ $availableApps->count() }} Tersedia
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Pilih aplikasi sekolah atau ajukan permintaan aplikasi baru untuk ditambahkan ke wadah.
                    </p>
                </div>
                <a
                    href="{{ route('school-app.requests.index') }}"
                    class="btn-primary inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 !text-white font-bold px-4 py-2.5 text-xs shadow-md shadow-brand-600/20 transition-all active:scale-95 w-full sm:w-auto shrink-0"
                    style="color: #ffffff !important;"
                >
                    <svg class="h-4 w-4 !text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color: #ffffff !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="!text-white font-bold" style="color: #ffffff !important;">Request Aplikasi</span>
                </a>
            </div>

            {{-- Filter Kategori & Pencarian Aplikasi --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-1.5">
                    @php $activeCat = $selectedCategory ?? ''; @endphp
                    <a
                        href="{{ route('apps.index', array_filter(['q' => $search ?? null])) }}"
                        class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $activeCat === '' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}"
                    >
                        Semua
                    </a>
                    @foreach ($categories ?? [] as $cat)
                        @php $isCat = strcasecmp($activeCat, $cat) === 0; @endphp
                        <a
                            href="{{ route('apps.index', array_filter(['category' => $cat, 'q' => $search ?? null])) }}"
                            class="rounded-xl px-3 py-1.5 text-xs font-bold transition {{ $isCat ? 'bg-brand-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}"
                        >
                            {{ ucfirst($cat) }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('apps.index') }}" class="flex items-center gap-2">
                    @if (! empty($selectedCategory))
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    @endif
                    <x-text-input
                        type="search"
                        name="q"
                        :value="$search ?? ''"
                        class="text-xs w-full sm:w-56"
                        placeholder="Cari aplikasi sekolah..."
                    />
                    <x-primary-button class="text-xs py-2 px-3">Cari</x-primary-button>
                    @if (! empty($search) || ! empty($selectedCategory))
                        <a href="{{ route('apps.index') }}" class="btn-secondary text-xs py-2 px-3">Reset</a>
                    @endif
                </form>
            </div>

            @if ($availableApps->isEmpty())
                <div class="card p-8 text-center dark:bg-slate-850 dark:border-slate-700/50">
                    <p class="text-sm font-semibold text-ink dark:text-slate-100">Belum ada aplikasi</p>
                    <p class="mt-1 text-sm text-muted dark:text-slate-500">Aplikasi yang Anda tambahkan akan muncul di App Hub.</p>
                    <a href="{{ route('school-app.requests.index') }}" class="mt-4 inline-flex text-sm font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
                        Buat permintaan →
                    </a>
                </div>
            @else
                <div class="grid gap-3.5 sm:gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($availableApps as $app)
                        @php
                            $hasAccess = in_array($app->slug, $installedSlugs, true) || ($school && $school->hasAccessToApp($app->slug));
                            $isPlanRestricted = $app->plans->isNotEmpty();
                            $targetPlan = $app->plans->first();
                            $upgradeUrl = $targetPlan ? route('checkout.show', $targetPlan->code) : route('harga');
                        @endphp
                         <article class="card p-5 sm:p-6 flex flex-col gap-4 dark:bg-slate-850 dark:border-slate-700/50">
                             <div class="flex items-start gap-4">
                                 <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-11.36 0V21m0 0h12M4 9.349a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v9.652M12 9.349a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v9.652M20 12v4.5" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                     <a href="{{ route('apps.show', $app->slug) }}" class="text-sm font-bold text-ink hover:text-brand-700 truncate block dark:text-slate-100 dark:hover:text-brand-400">
                                         {{ $app->name }}
                                     </a>
                                     @if ($app->description)
                                         <p class="mt-0.5 text-xs text-muted line-clamp-2 dark:text-slate-500">{{ $app->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                 <span class="rounded-full bg-surface-sunken dark:bg-slate-700/50 px-2 py-0.5 text-[11px] font-medium text-muted dark:text-slate-400">
                                    {{ $app->category ?? 'Umum' }}
                                </span>
                                @if ($app->version)
                                     <span class="text-[11px] text-muted dark:text-slate-500">v{{ $app->version }}</span>
                                @endif
                                @if ($isPlanRestricted)
                                     <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200/80 dark:border-amber-800/60 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-300">
                                         <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                         {{ $app->plans->pluck('label')->join(', ') }}
                                     </span>
                                @endif
                            </div>

                            <div class="mt-auto space-y-2.5">
                                @if ($hasAccess)
                                    <a href="{{ $app->accessLink() }}" class="btn-primary block w-full text-center !text-white font-bold" style="color: #ffffff !important;">
                                        <span class="!text-white font-bold" style="color: #ffffff !important;">Buka Aplikasi</span>
                                    </a>
                                @elseif ($isPlanRestricted)
                                    <div class="space-y-2">
                                        <span class="flex items-center justify-center gap-1.5 w-full rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 py-2 text-center text-xs font-semibold text-amber-800 dark:text-amber-300">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Terkunci &bull; Khusus {{ $app->plans->pluck('label')->join(', ') }}</span>
                                        </span>
                                        <a href="{{ $upgradeUrl }}" class="btn-primary block w-full text-center !text-white font-bold py-2 text-xs shadow-md shadow-brand-600/20" style="color: #ffffff !important;">
                                            <span class="!text-white font-bold" style="color: #ffffff !important;">Upgrade Paket →</span>
                                        </a>
                                    </div>
                                @else
                                     <span class="block w-full rounded-lg border border-dashed border-brand-200 dark:border-slate-600 bg-brand-50/50 dark:bg-slate-700/30 py-2.5 text-center text-xs font-semibold text-muted dark:text-slate-500" aria-disabled="true">
                                        Menunggu persetujuan
                                    </span>
                                     <a href="{{ route('school-app.requests.index') }}" class="mt-2 block text-center text-xs font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
                                        Request aplikasi ini →
                                    </a>
                                @endif

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-semibold">
                                    <a href="{{ route('apps.show', $app->slug) }}" class="text-slate-600 hover:text-brand-700 dark:text-slate-400 dark:hover:text-brand-300 transition">
                                        Detail Aplikasi →
                                    </a>
                                    <a href="{{ route('guides.index', ['app' => $app->id]) }}" class="inline-flex items-center gap-1 text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 transition">
                                        <span>📖 Buku Panduan</span>
                                        @if (($app->guides_count ?? 0) > 0)
                                            <span class="rounded-full bg-brand-50 dark:bg-brand-950/60 px-1.5 py-0.2 text-[10px]">{{ $app->guides_count }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
