?<x-app-layout>
    <x-slot name="header">
        @php
            $hour = (int) now()->timezone('Asia/Jakarta')->format('H');
            $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
            $userName = auth()->user()?->name ? explode(' ', trim(auth()->user()->name))[0] : 'Operator';
        @endphp
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        Portal Sekolah Aktif
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500 hidden sm:inline">&bull;</span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 hidden sm:inline">
                        {{ now()->timezone('Asia/Jakarta')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white mt-1">
                    {{ $greeting }}, {{ $userName }}! ??
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Ringkasan ekosistem aplikasi, status langganan, dan operasional digital {{ $school?->name ?? 'sekolah' }}
                </p>
            </div>

            {{-- School Identity Pill --}}
            <div class="hidden sm:flex items-center gap-2.5 bg-white/80 dark:bg-slate-850/80 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                @if ($school?->logo_path)
                    <img src="{{ asset('storage/' . $school->logo_path) }}" alt="{{ $school->name }}" class="h-8 w-8 rounded-xl object-cover bg-white p-0.5 border border-slate-200 dark:border-slate-700 shrink-0">
                @else
                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-white font-extrabold text-xs shadow-sm"
                        :class="'bg-gradient-to-tr ' + (themeConfig[schoolTheme]?.sidebar || themeConfig.teal.sidebar)"
                    >
                        {{ $school ? mb_substr($school->name, 0, 1) : 'S' }}
                    </div>
                @endif
                <div class="min-w-0 text-left">
                    <p class="text-xs font-black text-slate-800 dark:text-white truncate max-w-[180px]">
                        {{ $school?->name ?? 'Veyra Educate' }}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold truncate">
                        NPSN: {{ $school?->npsn ?? '-' }} &bull; {{ $school?->city ?? 'Indonesia' }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        announcementModalOpen: false,
        activeAnnouncement: null,
        openAnnouncement(announcement) {
            this.activeAnnouncement = announcement;
            this.announcementModalOpen = true;
        }
    }">

        {{-- Smart Renewal Alert Banner (If subscription <= 14 days remaining) --}}
        @if ($active && $daysRemaining !== null && $daysRemaining <= 14)
            <div class="{{ $daysRemaining <= 3 ? 'bg-gradient-to-r from-rose-600 via-rose-500 to-red-600' : 'bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600' }} text-white rounded-[2rem] p-4 sm:p-5 shadow-xl shadow-rose-950/10 border border-white/20 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 h-32 w-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                <div class="flex items-center gap-3.5 relative">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md shadow-inner border border-white/25">
                        @if ($daysRemaining <= 3)
                            <svg class="h-6 w-6 text-white animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-black/20 text-white border border-white/20">
                                {{ $daysRemaining <= 3 ? 'Kritis Segera Habis' : 'Pengingat Perpanjangan' }}
                            </span>
                            <span class="text-xs font-black">{{ $daysRemaining }} Hari Tersisa</span>
                        </div>
                        <h4 class="text-sm font-black text-white mt-1">
                            Masa Aktif Paket {{ $active->plan?->label ?? 'Sekolah' }} Segera Berakhir!
                        </h4>
                        <p class="text-xs text-white/90 mt-0.5 max-w-2xl leading-relaxed">
                            Masa aktif paket berakhir pada <span class="font-extrabold underline">{{ optional($active->ends_at)->format('d F Y') }}</span>. Segera lakukan perpanjangan agar akses CBT Online, sinkronisasi nilai rapor, dan aplikasi sekolah tetap berjalan tanpa jeda.
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2 relative">
                    <button
                        type="button"
                        onclick="document.getElementById('planModal').showModal()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-white text-slate-900 font-black px-4 py-2.5 text-xs shadow-lg hover:bg-slate-50 transition active:scale-95 cursor-pointer"
                    >
                        <span>Perpanjang Sekarang</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Mobile PWA Install Banner (Adaptive for Android/iOS, auto-hidden on desktop & standalone) --}}
        <x-pwa-install-button variant="banner" />

        {{-- Bento Grid Row 1: High-Tech Hero Status Hub & Financial Pulse Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-7">
            
            {{-- Left Hero Card: Executive Glassmorphic Hub --}}
            <div
                :class="'bg-gradient-to-r ' + (themeConfig[schoolTheme]?.hero || themeConfig.teal.hero)"
                class="lg:col-span-8 relative overflow-hidden rounded-[2.5rem] p-6 sm:p-8 text-white shadow-2xl shadow-teal-900/10 flex flex-col justify-between min-h-[280px] transition-all duration-300 border border-white/20"
            >
                {{-- Ambient glowing blur orbs behind glass --}}
                <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/15 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute right-1/3 bottom-0 h-40 w-40 rounded-full bg-[#fecf59]/20 blur-2xl" aria-hidden="true"></div>

                {{-- Header row with status & theme palette dots --}}
                <div class="relative flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/25 backdrop-blur-md text-[10px] font-black uppercase tracking-wider text-white border border-white/20 shadow-inner">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#fecf59] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#fecf59]"></span>
                            </span>
                            {{ $active ? 'Langganan Aktif' : ($subscription?->status?->value === 'pending' ? 'Menunggu Pembayaran' : 'Belum Berlangganan') }}
                        </span>

                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-white/90 bg-white/15 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-white/20">
                            {{ $school?->name ?? 'Veyra Educate' }}
                        </span>
                    </div>

                    {{-- Quick Theme Switcher Palette --}}
                    <div class="flex items-center gap-1.5 bg-black/20 hover:bg-black/30 backdrop-blur-md px-3 py-1 rounded-full border border-white/20 transition shadow-inner">
                        <span class="text-[10px] font-extrabold text-white/90 mr-0.5">Tema:</span>
                        <template x-for="(cfg, key) in themeConfig" :key="key">
                            <button
                                type="button"
                                @click="selectTheme(key)"
                                :title="cfg.name"
                                class="h-4 w-4 rounded-full transition-all transform hover:scale-125 focus:outline-none cursor-pointer"
                                :class="schoolTheme === key ? 'ring-2 ring-white scale-110 shadow-md' : 'opacity-70 hover:opacity-100'"
                                :style="'background-color: ' + cfg.accent"
                            ></button>
                        </template>
                    </div>
                </div>

                {{-- Center: Dynamic Metric Info & Active Plan Details --}}
                <div class="relative grid grid-cols-1 md:grid-cols-12 gap-5 items-center my-4">
                    
                    {{-- Big Metric Counter --}}
                    <div class="md:col-span-7">
                        @if ($active)
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl sm:text-7xl font-black tracking-tight leading-none text-white drop-shadow-sm">
                                    {{ $daysRemaining ?? '30' }}
                                </span>
                                <div class="flex flex-col">
                                    <span class="text-lg sm:text-2xl font-black text-white/95 leading-tight">Hari Tersisa</span>
                                    <span class="text-[11px] font-bold text-white/80">Sisa masa aktif sistem</span>
                                </div>
                            </div>

                            {{-- Progress Timeline Bar --}}
                            <div class="mt-4 space-y-1.5 max-w-md">
                                <div class="flex items-center justify-between text-[11px] font-bold text-white/90">
                                    <span>Paket {{ $active->plan?->label ?? 'Sekolah' }}</span>
                                    <span>{{ $subscriptionProgress ?? ($active ? 85 : 0) }}% Periode Berjalan</span>
                                </div>
                                <div class="h-2.5 w-full bg-black/25 backdrop-blur-sm rounded-full overflow-hidden border border-white/20 p-0.5">
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-white/80 via-[#fecf59] to-white transition-all duration-1000 shadow-sm"
                                        style="width: {{ min(100, max(8, $subscriptionProgress ?? ($active ? 85 : 0))) }}%"
                                    ></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-white/80">
                                    <span>Mulai: {{ optional($active->starts_at)->format('d M Y') ?? 'Awal' }}</span>
                                    <span>Berakhir: {{ optional($active->ends_at)->format('d M Y') ?? '—' }}</span>
                                </div>
                            </div>
                        @elseif ($subscription?->status?->value === 'pending')
                            <div class="space-y-2">
                                <span class="inline-block px-3 py-1 rounded-xl bg-[#fecf59] text-slate-900 text-xs font-black shadow-md">
                                    Menunggu Pembayaran
                                </span>
                                <div class="text-2xl sm:text-3xl font-black text-white">
                                    Tagihan Menunggu Pelunasan
                                </div>
                                <p class="text-xs text-white/90 max-w-md">
                                    Segera selesaikan tagihan sebesar <span class="font-extrabold text-[#fecf59]">{{ $lastOrder?->formatAmount() }}</span> agar seluruh aplikasi dan server sekolah otomatis aktif.
                                </p>
                            </div>
                        @else
                            <div class="space-y-2">
                                <span class="inline-block px-3 py-1 rounded-xl bg-white/20 text-white text-xs font-black border border-white/25">
                                    Belum Berlangganan
                                </span>
                                <div class="text-2xl sm:text-4xl font-black text-white">
                                    Aktifkan Aplikasi Sekolah Anda
                                </div>
                                <p class="text-xs text-white/90 max-w-md">
                                    Pilih paket langganan untuk meluncurkan CBT Online, E-Raport Kurikulum, dan fasilitas terintegrasi sekolah.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Right Highlights Card inside Hero --}}
                    <div class="md:col-span-5">
                        <div class="bg-black/20 backdrop-blur-xl rounded-2xl p-4 border border-white/20 shadow-inner space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white/80">Cluster Server</span>
                                <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Cloud ID-JKT
                                </span>
                            </div>
                            <div class="pt-2 border-t border-white/15 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-white/90">
                                    <span class="text-white/70 text-[11px]">Domain SSO:</span>
                                    <span class="font-bold text-[11px] truncate max-w-[150px]">{{ parse_url(config('app.url'), PHP_URL_HOST) ?: 'veyracreative.site' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-white/90">
                                    <span class="text-white/70 text-[11px]">Enkripsi Data:</span>
                                    <span class="font-bold text-[11px] text-[#fecf59]">SSL 256-Bit TLS</span>
                                </div>
                                <div class="flex items-center justify-between text-white/90">
                                    <span class="text-white/70 text-[11px]">Backup Harian:</span>
                                    <span class="font-bold text-[11px] text-emerald-300">Otomatis Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Action buttons row --}}
                <div class="relative flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-white/20 mt-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($active)
                            <button
                                type="button"
                                onclick="document.getElementById('planModal').showModal()"
                                class="rounded-xl bg-white text-slate-900 hover:bg-white/90 font-extrabold px-4 py-2 text-xs transition shadow-md active:scale-95 cursor-pointer flex items-center gap-1.5"
                            >
                                <svg class="h-3.5 w-3.5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Ganti / Perpanjang Paket</span>
                            </button>
                            <a
                                href="{{ route('pembayaran.history') }}"
                                class="rounded-xl bg-white/20 hover:bg-white/30 text-white font-bold px-3.5 py-2 text-xs transition backdrop-blur-md border border-white/20"
                            >
                                Riwayat Bayar &amp; SPJ BOS
                            </a>
                        @elseif ($subscription?->status?->value === 'pending')
                            @if ($lastOrder)
                                <a
                                    href="{{ route('pembayaran.show', $lastOrder->order_id) }}"
                                    class="rounded-xl bg-[#fecf59] hover:bg-[#fed063]/90 text-slate-900 font-black px-4 py-2 text-xs transition shadow-lg active:scale-95 flex items-center gap-1.5"
                                >
                                    <span>Bayar Sekarang ({{ $lastOrder->formatAmount() }})</span>
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endif
                        @else
                            <button
                                type="button"
                                onclick="document.getElementById('planModal').showModal()"
                                class="rounded-xl bg-[#fecf59] hover:bg-[#fed063]/90 text-slate-900 font-black px-4 py-2 text-xs transition shadow-lg active:scale-95 flex items-center gap-1.5 cursor-pointer"
                            >
                                <span>Pilih Paket Langganan &rarr;</span>
                            </button>
                        @endif

                        <a
                            href="{{ route('school.settings') }}"
                            class="rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold px-3 py-2 text-xs transition backdrop-blur-md"
                        >
                            Pengaturan Sekolah
                        </a>
                    </div>

                    <div class="text-[11px] text-white/90 font-medium hidden sm:block">
                        Versi Sistem: <span class="font-bold text-white">{{ $systemHealth['version'] ?? 'v2.4' }}</span>
                    </div>
                </div>
            </div>

            {{-- Right Card: Status Keuangan & SPJ BOS Widget --}}
            <div class="lg:col-span-4 bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
                
                {{-- Top label --}}
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Administrasi &amp; BOS</span>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5">Status Pembayaran</h3>
                    </div>
                    <span
                        class="rounded-full px-2.5 py-1 text-[10px] font-extrabold border"
                        :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '; background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '15; border-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '40'"
                    >
                        {{ now()->format('M Y') }}
                    </span>
                </div>

                {{-- Center Amount Display --}}
                <div class="my-5 p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 text-center">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Tagihan Transaksi Terakhir</p>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">
                        {{ $lastOrder ? $lastOrder->formatAmount() : 'Rp 0' }}
                    </div>
                    
                    <div class="mt-2.5 flex items-center justify-center gap-2">
                        @if ($lastOrder)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $lastOrder->status->value === 'settlement' || $lastOrder->status->value === 'capture' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $lastOrder->status->value === 'settlement' || $lastOrder->status->value === 'capture' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></span>
                                {{ $lastOrder->status->label() }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $lastOrder->order_id }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                Belum ada transaksi
                            </span>
                        @endif
                    </div>
                </div>

                {{-- BOS SPJ Callout & Invoice Link --}}
                <div class="space-y-2 mb-2">
                    @if ($lastOrder)
                        <a
                            href="{{ route('pembayaran.show', $lastOrder->order_id) }}"
                            class="w-full flex items-center justify-center gap-2 text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-800 dark:text-slate-200 transition shadow-sm hover:-translate-y-0.5 active:translate-y-0"
                        >
                            <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Unduh Bukti Bayar &amp; SPJ BOS &rarr;</span>
                        </a>
                    @endif

                    <a
                        href="{{ route('pembayaran.history') }}"
                        class="w-full text-center block text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:underline transition py-1"
                    >
                        Lihat Semua Riwayat Pembayaran &rarr;
                    </a>
                </div>

                {{-- Bottom Metadata & Server Uptime --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Server Online</span>
                        <span class="text-slate-400 dark:text-slate-500">&bull; 99.98%</span>
                    </div>
                    <span class="text-slate-500 dark:text-slate-400 font-semibold truncate max-w-[120px]" title="Wilayah Server">
                        {{ $school?->city ?? 'Indonesia' }}
                    </span>
                </div>

            </div>

        </div>

        {{-- High-Priority Subdomain App Quick-Launch Grid (CBT, Raport, Card Studio, Broadcast) --}}
        <div
            x-data="{
                checkingSubdomains: false,
                copiedToken: null,
                cbtStats: @js($cbtStats ?? [
                    'synced' => false,
                    'students_active' => 0,
                    'student_quota' => 500,
                    'quota_percentage' => 0,
                    'classes_count' => 0,
                    'teachers_count' => 0,
                    'subjects_count' => 0,
                    'question_banks_count' => 0,
                    'questions_count' => 0,
                    'active_exams_count' => 0,
                    'ongoing_sessions_count' => 0,
                    'completed_sessions_count' => 0,
                    'active_exams' => [],
                ]),
                subdomainStatus: {
                    cbt: { status: 'online', latency_ms: null },
                    raport: { status: 'online', latency_ms: null }
                },
                checkSubdomains(forceRefresh = false) {
                    this.checkingSubdomains = true;
                    const url = '{{ route('subdomain.status') }}' + (forceRefresh ? '?refresh=1' : '');
                    fetch(url, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.ok ? r.json() : null)
                        .then(data => {
                            if (data && Array.isArray(data.services)) {
                                data.services.forEach(svc => {
                                    if (svc.key && this.subdomainStatus[svc.key]) {
                                        this.subdomainStatus[svc.key] = {
                                            status: svc.status || 'online',
                                            latency_ms: svc.latency_ms || null
                                        };
                                    }
                                });
                            }
                            if (data && data.cbt_stats) {
                                this.cbtStats = data.cbt_stats;
                            }
                        })
                        .catch(() => {})
                        .finally(() => { this.checkingSubdomains = false; });
                },
                copyExamToken(token) {
                    if (!token || token === '-') return;
                    navigator.clipboard.writeText(token);
                    this.copiedToken = token;
                    setTimeout(() => { this.copiedToken = null; }, 2000);
                }
            }"
            x-init="checkSubdomains(false)"
            class="bg-white dark:bg-slate-850 rounded-[2rem] sm:rounded-[2.5rem] p-5 sm:p-7 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-5"
        >
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white">
                            Subdomain &amp; Layanan Terintegrasi
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                            SSO Ready
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Akses instan ke aplikasi utama di subdomain sekolah tanpa perlu login ulang
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="checkSubdomains(true)"
                        :disabled="checkingSubdomains"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-50 hover:bg-slate-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 transition cursor-pointer"
                        title="Sinkronkan statistik CBT & cek konektivitas subdomain"
                    >
                        <svg class="h-3 w-3 text-emerald-500" :class="checkingSubdomains ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span x-text="checkingSubdomains ? 'Menyinkronkan...' : 'Sinkronkan CBT & Ping'">Sinkronkan CBT &amp; Ping</span>
                    </button>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700">
                        <svg class="h-3 w-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/></svg>
                        <span>Single Sign-On Aktif</span>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @php
                    $canLaunchCbt = (bool) ($active || $school?->hasAccessToApp('cbtonline'));
                    $canLaunchRaport = (bool) ($active || $school?->hasAccessToApp('raport') || $school?->hasAccessToApp('rapor'));
                @endphp

                {{-- CBT Card --}}
                @if ($canLaunchCbt)
                    <a
                        href="{{ route('sso.launch', 'cbtonline') }}"
                        target="_blank"
                        class="group relative overflow-hidden rounded-2xl p-5 border border-emerald-200/80 dark:border-emerald-900/60 bg-gradient-to-br from-emerald-50/50 via-white to-teal-50/30 dark:from-emerald-950/20 dark:via-slate-850 dark:to-slate-850 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between"
                    >
                @else
                    <button
                        type="button"
                        onclick="document.getElementById('planModal').showModal()"
                        class="group relative overflow-hidden rounded-2xl p-5 text-left border border-slate-200/80 dark:border-slate-800 bg-gradient-to-br from-slate-50/80 via-white to-slate-50/40 dark:from-slate-900/40 dark:via-slate-850 dark:to-slate-850 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between cursor-pointer"
                    >
                @endif
                    <div class="flex items-center justify-between mb-3 w-full">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        @if ($canLaunchCbt)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" :class="checkingSubdomains ? 'animate-ping' : ''"></span>
                                <span x-text="subdomainStatus.cbt.latency_ms ? ('Online • ' + subdomainStatus.cbt.latency_ms + 'ms') : 'Online'">Online</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                🔒 Paket Aktif
                            </span>
                        @endif
                    </div>
                    <div class="w-full">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            CBT Online
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Ujian Sumatif, STS/SAS &amp; Bank Soal
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100/70 dark:bg-emerald-950/70">
                                <span x-text="cbtStats.students_active ?? 0">0</span> Siswa
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-teal-100/70 dark:bg-teal-950/70">
                                <span x-text="cbtStats.active_exams_count ?? 0">0</span> Ujian Aktif
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-emerald-100 dark:border-emerald-950/60 flex items-center justify-between text-xs font-black text-emerald-700 dark:text-emerald-400 w-full">
                        <span>{{ $canLaunchCbt ? 'Buka Server CBT' : 'Aktifkan Langganan' }}</span>
                        <svg class="h-4 w-4 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                @if ($canLaunchCbt)
                    </a>
                @else
                    </button>
                @endif

                {{-- E-Raport Card --}}
                @if ($canLaunchRaport)
                    <a
                        href="{{ route('sso.launch', 'raport') }}"
                        target="_blank"
                        class="group relative overflow-hidden rounded-2xl p-5 border border-blue-200/80 dark:border-blue-900/60 bg-gradient-to-br from-blue-50/50 via-white to-indigo-50/30 dark:from-blue-950/20 dark:via-slate-850 dark:to-slate-850 hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between"
                    >
                @else
                    <button
                        type="button"
                        onclick="document.getElementById('planModal').showModal()"
                        class="group relative overflow-hidden rounded-2xl p-5 text-left border border-slate-200/80 dark:border-slate-800 bg-gradient-to-br from-slate-50/80 via-white to-slate-50/40 dark:from-slate-900/40 dark:via-slate-850 dark:to-slate-850 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between cursor-pointer"
                    >
                @endif
                    <div class="flex items-center justify-between mb-3 w-full">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        @if ($canLaunchRaport)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500" :class="checkingSubdomains ? 'animate-ping' : ''"></span>
                                <span x-text="subdomainStatus.raport.latency_ms ? ('Online • ' + subdomainStatus.raport.latency_ms + 'ms') : 'Online'">Online</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                🔒 Paket Aktif
                            </span>
                        @endif
                    </div>
                    <div class="w-full">
                        <h4 class="text-sm font-black text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            E-Raport Kurikulum
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pengolahan Nilai &amp; Cetak Rapor
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-blue-100 dark:border-blue-950/60 flex items-center justify-between text-xs font-black text-blue-700 dark:text-blue-400 w-full">
                        <span>{{ $canLaunchRaport ? 'Buka E-Raport' : 'Aktifkan Langganan' }}</span>
                        <svg class="h-4 w-4 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                @if ($canLaunchRaport)
                    </a>
                @else
                    </button>
                @endif

                {{-- Card Studio Card --}}
                <a
                    href="{{ route('school.cards.index') }}"
                    class="group relative overflow-hidden rounded-2xl p-5 border border-amber-200/80 dark:border-amber-900/60 bg-gradient-to-br from-amber-50/50 via-white to-orange-50/30 dark:from-amber-950/20 dark:via-slate-850 dark:to-slate-850 hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white shadow-md shadow-amber-500/25 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                            A4 Grid
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                            Card Studio
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Cetak Kartu Ujian &amp; Kartu Pelajar
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-amber-100 dark:border-amber-950/60 flex items-center justify-between text-xs font-black text-amber-700 dark:text-amber-400">
                        <span>Buka Studio Cetak</span>
                        <svg class="h-4 w-4 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </a>

                {{-- Broadcast WhatsApp Card --}}
                <a
                    href="{{ route('school.broadcast.index') }}"
                    class="group relative overflow-hidden rounded-2xl p-5 border border-emerald-200/80 dark:border-emerald-900/60 bg-gradient-to-br from-emerald-50/50 via-white to-green-50/30 dark:from-emerald-950/20 dark:via-slate-850 dark:to-slate-850 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-500 to-green-600 text-white shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                            Gateway
                        </span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            Broadcast WA
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pengumuman Wali Murid &amp; Guru
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-emerald-100 dark:border-emerald-950/60 flex items-center justify-between text-xs font-black text-emerald-700 dark:text-emerald-400">
                        <span>Pusat Broadcast</span>
                        <svg class="h-4 w-4 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </a>
            </div>

            {{-- Live Sinkronisasi Statistik Ujian & Siswa (CBT Online) --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Statistik Live CBT Online</span>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider"
                                    :class="cbtStats.synced ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                    x-text="cbtStats.synced ? 'Tersinkronisasi' : 'Siap Sinkron'"
                                >
                                    {{ ($cbtStats['synced'] ?? false) ? 'Tersinkronisasi' : 'Siap Sinkron' }}
                                </span>
                            </h4>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-[11px]">
                        @if ($canLaunchCbt)
                            <a
                                href="{{ route('sso.launch', 'cbtonline') }}"
                                target="_blank"
                                class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1"
                            >
                                <span>Kelola 👥 Data Siswa &amp; Ujian di CBT</span>
                                <span>&rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Stat 1: Kuota & Siswa Terdaftar --}}
                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            <span>Siswa CBT Aktif</span>
                            <span class="text-emerald-600 dark:text-emerald-400" x-text="(cbtStats.classes_count ?? 0) + ' Kelas'">{{ $cbtStats['classes_count'] ?? 0 }} Kelas</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white" x-text="cbtStats.students_active ?? 0">{{ $cbtStats['students_active'] ?? 0 }}</span>
                                <span class="text-xs font-bold text-slate-400">/ <span x-text="cbtStats.student_quota ?? 500">{{ $cbtStats['student_quota'] ?? 500 }}</span> kuota</span>
                            </div>
                            <div class="mt-2 h-1.5 w-full rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                                    :style="'width: ' + Math.min(100, cbtStats.quota_percentage ?? 0) + '%'"
                                ></div>
                            </div>
                        </div>
                    </div>

                    {{-- Stat 2: Bank Soal & Butir Soal --}}
                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            <span>Bank Soal CBT</span>
                            <span class="text-blue-600 dark:text-blue-400" x-text="(cbtStats.subjects_count ?? 0) + ' Mapel'">{{ $cbtStats['subjects_count'] ?? 0 }} Mapel</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white" x-text="cbtStats.question_banks_count ?? 0">{{ $cbtStats['question_banks_count'] ?? 0 }}</span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Bank Soal</span>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">
                                Total <span class="font-bold text-slate-700 dark:text-slate-200" x-text="cbtStats.questions_count ?? 0">{{ $cbtStats['questions_count'] ?? 0 }}</span> butir soal siap ujian
                            </p>
                        </div>
                    </div>

                    {{-- Stat 3: Jadwal Ujian Aktif --}}
                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            <span>Jadwal Ujian</span>
                            <span class="text-amber-600 dark:text-amber-400" x-text="(cbtStats.teachers_count ?? 0) + ' Guru/Proktor'">{{ $cbtStats['teachers_count'] ?? 0 }} Guru/Proktor</span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white" x-text="cbtStats.active_exams_count ?? 0">{{ $cbtStats['active_exams_count'] ?? 0 }}</span>
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Ujian Aktif</span>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">
                                Token siap dibagikan ke peserta
                            </p>
                        </div>
                    </div>

                    {{-- Stat 4: Peserta Sedang Ujian (Live) --}}
                    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                        <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            <span>Monitor Peserta</span>
                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                Live
                            </span>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white" x-text="cbtStats.ongoing_sessions_count ?? 0">{{ $cbtStats['ongoing_sessions_count'] ?? 0 }}</span>
                                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Sedang Ujian</span>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="cbtStats.completed_sessions_count ?? 0">{{ $cbtStats['completed_sessions_count'] ?? 0 }}</span> sesi selesai dikumpulkan
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Daftar Ujian Aktif & Token (Muncul otomatis jika ada ujian aktif di CBT) --}}
                <template x-if="Array.isArray(cbtStats.active_exams) && cbtStats.active_exams.length > 0">
                    <div class="rounded-2xl border border-emerald-200/70 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-emerald-950/20 p-3.5 sm:p-4 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-300">
                                Jadwal Ujian Aktif &amp; Token Rilis
                            </span>
                            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                                Klik token untuk menyalin
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                            <template x-for="exam in cbtStats.active_exams" :key="exam.id">
                                <div class="rounded-xl bg-white dark:bg-slate-850 border border-slate-200/80 dark:border-slate-800 p-3 flex items-center justify-between gap-3 shadow-xs">
                                    <div class="min-w-0">
                                        <h5 class="text-xs font-black text-slate-900 dark:text-white truncate" x-text="exam.title"></h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                            <span x-text="exam.subject_name"></span> &bull; <span x-text="exam.class_name"></span> &bull; <span x-text="(exam.duration_minutes || 60) + ' Menit'"></span>
                                        </p>
                                        <div class="mt-1.5 flex items-center gap-2 text-[10px] font-bold">
                                            <span class="text-amber-600 dark:text-amber-400" x-text="(exam.in_progress_count || 0) + ' Mengerjakan'"></span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-emerald-600 dark:text-emerald-400" x-text="(exam.completed_count || 0) + ' Selesai'"></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                                        <button
                                            type="button"
                                            @click="copyExamToken(exam.token)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-mono text-xs font-black tracking-wider hover:opacity-90 transition cursor-pointer"
                                            title="Salin Token Ujian"
                                        >
                                            <span x-text="copiedToken === exam.token ? 'TERSALIN!' : (exam.token || '-')"></span>
                                        </button>
                                        @if ($canLaunchCbt)
                                            <a
                                                href="{{ route('sso.launch', 'cbtonline') }}"
                                                target="_blank"
                                                class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                                            >
                                                Monitor Live &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Bento Grid Row 2: 4 Modern Metric Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 transition-all duration-300">
            
            {{-- Card 1: Aplikasi Aktif --}}
            <a
                href="{{ route('apps.index') }}"
                class="group bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex flex-col justify-between"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] sm:text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate">Modul Aktif</span>
                    <div
                        class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl transition-colors"
                        :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '20; color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                    >
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 sm:gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['accessible_apps_count'] ?? 0 }}</span>
                        <span class="text-xs text-slate-400 font-semibold">/ {{ $stats['available_apps_count'] ?? 0 }} katalog</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold group-hover:underline flex items-center justify-between">
                        <span>Buka App Hub</span>
                        <span>&rarr;</span>
                    </p>
                </div>
            </a>

            {{-- Card 2: Permintaan Modul --}}
            <a
                href="{{ route('school-app.requests.index') }}"
                class="group bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex flex-col justify-between"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] sm:text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate">Permintaan App</span>
                    <div class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 sm:gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['pending_requests_count'] ?? 0 }}</span>
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-bold">
                            {{ ($stats['pending_requests_count'] ?? 0) > 0 ? 'Sedang Ditinjau' : 'Antrean Bersih' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold group-hover:underline flex items-center justify-between">
                        <span>Pantau Pengajuan</span>
                        <span>&rarr;</span>
                    </p>
                </div>
            </a>

            {{-- Card 3: Buku Panduan & SOP --}}
            <a
                href="{{ route('guides.index') }}"
                class="group bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex flex-col justify-between"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] sm:text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate">Buku Panduan</span>
                    <div class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 sm:gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total_guides_count'] ?? 0 }}</span>
                        <span class="text-xs text-blue-600 dark:text-blue-400 font-bold">Tutorial Resmi</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold group-hover:underline flex items-center justify-between">
                        <span>Unduh Panduan</span>
                        <span>&rarr;</span>
                    </p>
                </div>
            </a>

            {{-- Card 4: WhatsApp CS Standby --}}
            <a
                href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate,%20saya%20butuh%20bantuan%20terkait%20sekolah%20{{ urlencode($school?->name ?? 'saya') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="group bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 flex flex-col justify-between"
            >
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] sm:text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider truncate">Helpdesk Sekolah</span>
                    <div class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="flex items-baseline gap-1.5 sm:gap-2">
                        <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">CS Standby</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-semibold group-hover:underline flex items-center justify-between">
                        <span>Chat WhatsApp</span>
                        <span>&rarr;</span>
                    </p>
                </div>
            </a>

        </div>

        {{-- Bento Grid Row 3: Left Main Applications & Operational Hub (Col 8) + Right Feeds & Support (Col 4) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-7 transition-all duration-300">
            
            {{-- Left Column: 8 Columns --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- 1. Launchpad Aplikasi Sekolah (Wadah Aplikasi Utama) --}}
                <div class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-slate-100 dark:border-slate-800" x-data="{ activeAppCategory: 'all' }">
                    <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Wadah Aplikasi Digital</span>
                            <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white mt-0.5">Aplikasi Terpasang Sekolah Anda</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('apps.index') }}"
                                class="text-xs font-bold hover:underline"
                                :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                            >
                                Jelajahi Katalog Lengkap &rarr;
                            </a>
                        </div>
                    </div>

                    @if ($myApps->isNotEmpty())
                        {{-- Category Filter Pills --}}
                        @if (!empty($appCategories))
                            <div class="flex items-center gap-1.5 mb-5 overflow-x-auto pb-1 scrollbar-none">
                                <button
                                    type="button"
                                    @click="activeAppCategory = 'all'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0"
                                    :class="activeAppCategory === 'all'
                                        ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm'
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                >
                                    Semua Aplikasi ({{ $myApps->count() }})
                                </button>
                                @foreach ($appCategories as $cat)
                                    <button
                                        type="button"
                                        @click="activeAppCategory = '{{ strtolower($cat) }}'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0 capitalize"
                                        :class="activeAppCategory === '{{ strtolower($cat) }}'
                                            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm'
                                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                                    >
                                        {{ $cat }} ({{ $myApps->filter(fn($a) => strtolower($a->category ?? '') === strtolower($cat))->count() }})
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- Apps Launch Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($myApps as $app)
                                <div
                                    x-show="activeAppCategory === 'all' || activeAppCategory === '{{ strtolower($app->category ?? 'umum') }}'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="group relative flex flex-col justify-between rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 sm:p-5 transition-all duration-300 hover:shadow-lg hover:border-emerald-300 dark:hover:border-slate-700 hover:-translate-y-1"
                                >
                                    <div>
                                        <div class="flex items-start justify-between gap-4 mb-4">
                                                <div class="flex items-center gap-4 min-w-0">
                                                <div
                                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-white font-black text-lg shadow-md transition-transform duration-300 group-hover:scale-105"
                                                    :class="'bg-gradient-to-tr ' + (themeConfig[schoolTheme]?.sidebar || themeConfig.teal.sidebar)"
                                                >
                                                    {{ mb_substr($app->name, 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                        {{ $app->name }}
                                                    </h4>
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 dark:text-slate-400 capitalize mt-0.5">
                                                        {{ $app->category ?? 'Aplikasi Digital' }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- SSO Badge --}}
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                SSO 1-Klik
                                            </span>
                                        </div>

                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mt-1">
                                            {{ $app->description ?? 'Modul aplikasi terintegrasi untuk mempercepat efisiensi manajemen sekolah.' }}
                                        </p>
                                    </div>

                                    <div class="mt-4 pt-3.5 border-t border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                                            Status: <span class="text-emerald-600 dark:text-emerald-400 font-black">Siap Pakai</span>
                                        </span>
                                        <a
                                            href="{{ $app->accessLink() }}"
                                            class="inline-flex items-center gap-1.5 rounded-xl font-black px-4 py-2 text-xs text-white shadow-md transition-all active:scale-95 cursor-pointer hover:opacity-95"
                                            :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                                        >
                                            <span>Buka Aplikasi</span>
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State with CTA --}}
                        <div class="rounded-3xl border border-dashed border-slate-200 dark:border-slate-700/80 p-8 text-center bg-slate-50/50 dark:bg-slate-900/30">
                            <div
                                class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl mb-3 shadow-inner"
                                :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '15; color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                            >
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-black text-slate-900 dark:text-white">Belum Ada Modul Aplikasi Aktif</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto leading-relaxed">
                                Aplikasi akan otomatis terbuka begitu langganan sekolah aktif, atau Anda dapat mengajukan permintaan modul kustom melalui tim Veyra.
                            </p>
                            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                                <a
                                    href="{{ route('apps.index') }}"
                                    class="rounded-xl px-4 py-2 text-xs font-black text-white shadow-md transition"
                                    :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                                >
                                    Jelajahi Katalog Aplikasi
                                </a>
                                <a
                                    href="{{ route('school-app.requests.index') }}"
                                    class="rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-bold hover:bg-slate-50 transition"
                                >
                                    Ajukan Modul Kustom
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 2. Pintasan Layanan & 4 Fitur Unggulan Operasional (Bento 2x2 Grid) --}}
                <div class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Pintasan Layanan Unggulan</span>
                            <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white mt-0.5">Fitur Terintegrasi Sekolah</h3>
                        </div>
                        <span class="text-xs font-bold text-slate-400">4 Modul Terbuka</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- Fitur 1: Generator Kartu Ujian & Pelajar --}}
                        <a
                            href="{{ route('school.cards.index') }}"
                            class="group p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition-all duration-300 hover:shadow-lg hover:border-rose-300 flex items-start gap-4"
                        >
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white group-hover:text-rose-600 transition truncate">Cetak Kartu Ujian &amp; Siswa</h4>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    Generator kartu peserta ber-barcode (desain Terracotta), siap cetak massal lembar A4.
                                </p>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 mt-2">
                                    Buka Generator &rarr;
                                </span>
                            </div>
                        </a>

                        {{-- Fitur 2: WhatsApp Broadcast --}}
                        <a
                            href="{{ route('school.broadcast.index') }}"
                            class="group p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition-all duration-300 hover:shadow-lg hover:border-emerald-300 flex items-start gap-4"
                        >
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white group-hover:text-emerald-600 transition truncate">WhatsApp Broadcast</h4>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    Kirim jadwal ujian, pengumuman sekolah, dan info tagihan langsung ke nomor wali murid.
                                </p>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-2">
                                    Kirim Broadcast &rarr;
                                </span>
                            </div>
                        </a>

                        {{-- Fitur 3: Referral & Komisi Saldo --}}
                        <a
                            href="{{ route('school.referral.index') }}"
                            class="group p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition-all duration-300 hover:shadow-lg hover:border-amber-300 flex items-start gap-4"
                        >
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white group-hover:text-amber-600 transition truncate">Program Afiliasi 10%</h4>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    Ajak sekolah mitra bergabung dan nikmati komisi saldo 10% per transaksi berhasil.
                                </p>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-2">
                                    Lihat Saldo &amp; Link &rarr;
                                </span>
                            </div>
                        </a>

                        {{-- Fitur 4: Kalkulator & Proposal BOS --}}
                        <a
                            href="{{ url('proposal') }}"
                            target="_blank"
                            class="group p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition-all duration-300 hover:shadow-lg hover:border-blue-300 flex items-start gap-4"
                        >
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white group-hover:text-blue-600 transition truncate">Surat Penawaran BOS</h4>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    Format surat penawaran resmi kepala sekolah dengan kalkulator penghematan anggaran BOS.
                                </p>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 mt-2">
                                    Buka Proposal BOS &rarr;
                                </span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- 3. Panduan Cepat Proktor Ujian (3 Langkah Praktis) --}}
                <div class="rounded-[2.5rem] bg-white dark:bg-slate-850 p-6 sm:p-7 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider mb-1">
                                Alur Kerja Proktor
                            </span>
                            <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white">
                                3 Langkah Praktis Pelaksanaan Ujian CBT
                            </h3>
                        </div>
                        <a href="{{ route('guides.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            Semua Panduan &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                        {{-- Step 1 --}}
                        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-600 text-white text-xs font-black">1</span>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Unduh Template Data</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Siapkan butir soal ujian (PG/Esai) dan data siswa menggunakan template Excel standar.
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 dark:border-slate-800 flex items-center gap-2">
                                <a href="{{ route('school.settings.template-dapodik', 'soal') }}" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    📝 Template Soal
                                </a>
                                <span class="text-slate-300">&bull;</span>
                                <a href="{{ route('school.settings.template-dapodik', 'siswa') }}" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    👥 Data Siswa
                                </a>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-black">2</span>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Buka CBT Online (SSO)</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Masuk ke panel manajemen ujian CBT secara otomatis dengan 1 klik tanpa login ulang.
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 dark:border-slate-800">
                                <a href="{{ route('sso.launch', 'cbtonline') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                    <span>Buka Panel CBT</span>
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-600 text-white text-xs font-black">3</span>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Cetak Kartu &amp; Mulai</h4>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Cetak kartu peserta ber-barcode, bagikan sandi ujian, dan rilis token ujian serentak.
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 dark:border-slate-800">
                                <a href="{{ route('school.cards.index') }}" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    📇 Cetak Kartu Massal &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Profil & Kelengkapan Sekolah --}}
                <div class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 sm:p-7 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Identitas Lembaga</span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5">Kelengkapan Data Profil</h3>
                        </div>
                        <a
                            href="{{ route('school.settings') }}"
                            class="text-xs font-bold hover:underline"
                            :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                        >
                            Pengaturan Profil &rarr;
                        </a>
                    </div>

                    <div class="flex items-center gap-4">
                        @if ($school && $school->logo_path)
                            <img
                                src="{{ asset('storage/' . $school->logo_path) }}"
                                alt="{{ $school->name }}"
                                class="h-14 w-14 shrink-0 rounded-2xl object-cover border border-slate-200/80 dark:border-slate-750 shadow-md bg-white p-0.5"
                            />
                        @else
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-white font-black text-xl shadow-md transition-all duration-300"
                                :class="'bg-gradient-to-tr ' + (themeConfig[schoolTheme]?.sidebar || themeConfig.teal.sidebar)"
                            >
                                {{ $school ? mb_substr($school->name, 0, 1) : 'S' }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-black text-slate-900 dark:text-white truncate">
                                {{ $school?->name ?? 'Data Sekolah Belum Lengkap' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                                {{ $school?->city ?? 'Kota belum diisi' }}
                                @if($school?->npsn) &bull; NPSN {{ $school->npsn }} @endif
                            </p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $active ? 'Langganan Aktif' : ($subscription?->status?->label() ?? 'Reguler') }}
                        </span>
                    </div>

                    {{-- Profile Completion Indicator --}}
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Skor Kelengkapan Dokumen
                            </span>
                            <span
                                class="font-extrabold px-2.5 py-0.5 rounded-full text-[11px]"
                                :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '20; color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                            >
                                {{ $profileCompletion['percentage'] ?? 0 }}% Lengkap
                            </span>
                        </div>

                        <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all duration-700 ease-out"
                                :style="'width: {{ $profileCompletion['percentage'] ?? 0 }}%; background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                            ></div>
                        </div>

                        @if (($profileCompletion['percentage'] ?? 0) >= 100)
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Seluruh data profil sekolah sudah lengkap &amp; siap cetak dokumen BOS.
                            </p>
                        @elseif (!empty($profileCompletion['missing']))
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                <span class="text-[10px] text-slate-400 font-semibold">Lengkapi:</span>
                                @foreach (array_slice($profileCompletion['missing'], 0, 3) as $miss)
                                    <a
                                        href="{{ $miss['route'] }}"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 hover:bg-amber-100 transition border border-amber-200/60"
                                    >
                                        <span>+ {{ $miss['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Right Column: 4 Columns (Feeds, Agenda, App Requests, Guides, Support) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- 1. Papan Pengumuman Resmi --}}
                <div class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Kabar &amp; Informasi</span>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5">Pengumuman Resmi</h3>
                            </div>
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>

                        <div class="space-y-3">
                            @forelse ($announcements as $announcement)
                                <div
                                    data-announcement="{{ json_encode([
                                        'id' => $announcement->id,
                                        'title' => $announcement->title,
                                        'body' => $announcement->body,
                                        'date' => optional($announcement->published_at)->format('d M Y') ?? optional($announcement->created_at)->format('d M Y'),
                                    ]) }}"
                                    @click="openAnnouncement(JSON.parse($el.dataset.announcement))"
                                    class="group flex items-center justify-between gap-3 p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/70 border border-transparent hover:border-slate-200/60 dark:hover:border-slate-700 transition cursor-pointer"
                                >
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-white font-black text-xs shadow-sm transition-all duration-300 group-hover:scale-105"
                                            :class="'bg-gradient-to-tr ' + (themeConfig[schoolTheme]?.sidebar || themeConfig.teal.sidebar)"
                                        >
                                            {{ mb_substr($announcement->title, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition truncate">
                                                {{ $announcement->title }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                                {{ Str::limit($announcement->body, 40) }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg class="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            @empty
                                <div class="py-6 text-center">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Belum ada pengumuman</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Semua info dan jadwal rilis aplikasi normal.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-3.5 mt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                        <a
                            href="{{ route('apps.index') }}"
                            class="text-xs font-bold hover:underline"
                            :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                        >
                            Jelajahi Wadah Aplikasi &rarr;
                        </a>
                    </div>
                </div>

                {{-- 2. Agenda Digital & Kalender Akademik --}}
                <div x-data="{ showAgendaForm: false }" class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Timeline Kegiatan</span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5">Agenda Digital Sekolah</h3>
                        </div>
                        <button
                            type="button"
                            @click="showAgendaForm = !showAgendaForm"
                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-extrabold transition cursor-pointer"
                            :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '; background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '15'"
                        >
                            <span x-text="showAgendaForm ? 'Tutup' : '+ Agenda'">+ Agenda</span>
                        </button>
                    </div>

                    {{-- Inline Add Agenda Form --}}
                    <form
                        x-show="showAgendaForm"
                        x-transition
                        x-cloak
                        method="POST"
                        action="{{ route('school.agenda.store') }}"
                        class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-700/80 space-y-2.5"
                    >
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Kegiatan / Agenda</label>
                            <input
                                type="text"
                                name="title"
                                required
                                maxlength="120"
                                placeholder="Mis. Simulasi Ujian CBT Kelas XII"
                                class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 px-3 py-1.5 focus:ring-2 focus:ring-emerald-500"
                            >
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-300 mb-1">Tanggal</label>
                                <input
                                    type="date"
                                    name="date"
                                    required
                                    min="{{ now()->toDateString() }}"
                                    value="{{ now()->addDays(3)->toDateString() }}"
                                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 px-2.5 py-1.5"
                                >
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 dark:text-slate-300 mb-1">Kategori</label>
                                <select
                                    name="category"
                                    class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-100 px-2.5 py-1.5"
                                >
                                    <option value="CBT & Ujian">CBT &amp; Ujian</option>
                                    <option value="Akademik">Akademik</option>
                                    <option value="Rapat & Dinas">Rapat &amp; Dinas</option>
                                    <option value="Pemeliharaan">Pemeliharaan</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end pt-1">
                            <button
                                type="submit"
                                class="rounded-xl px-3.5 py-1.5 text-[11px] font-extrabold text-white shadow-sm transition hover:opacity-95 cursor-pointer"
                                :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                            >
                                Simpan Agenda
                            </button>
                        </div>
                    </form>

                    <div class="space-y-3">
                        @foreach ($upcomingAgendas as $agenda)
                            <div class="p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-750 flex items-center justify-between gap-3 hover:bg-slate-100/70 dark:hover:bg-slate-800 transition">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-700 dark:text-slate-200 shadow-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $agenda['title'] }}</h4>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                                            {{ $agenda['date'] }} &bull; <span class="capitalize">{{ $agenda['category'] }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $agenda['days_left'] <= 3 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $agenda['days_left'] === 0 ? 'Hari ini' : $agenda['days_left'] . ' hari lagi' }}
                                    </span>
                                    @if (!empty($agenda['is_custom']) && !empty($agenda['id']))
                                        <form method="POST" action="{{ route('school.agenda.destroy', $agenda['id']) }}" onsubmit="return confirm('Hapus agenda ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer" title="Hapus agenda">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Status Permintaan Aplikasi Sekolah --}}
                <div class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Tiket Kustom</span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5">Status Permintaan App</h3>
                        </div>
                        <a
                            href="{{ route('school-app.requests.index') }}"
                            class="text-xs font-bold hover:underline"
                            :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                        >
                            Kelola &rarr;
                        </a>
                    </div>

                    @if ($recentAppRequests->isNotEmpty())
                        <div class="space-y-2.5">
                            @foreach ($recentAppRequests as $req)
                                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-750 flex flex-col gap-2 transition-all">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $req->name }}</h4>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $req->category ?? 'Umum' }} &bull; {{ $req->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                        
                                        @if ($req->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 shrink-0 border border-blue-200/50">
                                                Disetujui
                                            </span>
                                        @elseif ($req->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 shrink-0 border border-rose-200/50">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 shrink-0 border border-amber-200/50">
                                                Menunggu
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-4 text-center bg-slate-50/50 dark:bg-slate-900/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700/80 p-3">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Belum ada pengajuan modul</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Butuh aplikasi kustom? Ajukan melalui menu permintaan.</p>
                            <div class="mt-2.5">
                                <a
                                    href="{{ route('school-app.requests.index') }}"
                                    class="inline-flex items-center gap-1 rounded-xl px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition"
                                    :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                                >
                                    + Ajukan Permintaan
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 4. Tutorial & Panduan Terpopuler --}}
                <div class="bg-white dark:bg-slate-850 rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Berkas Unduhan</span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5">Panduan Terpopuler</h3>
                        </div>
                        <a
                            href="{{ route('guides.index') }}"
                            class="text-xs font-bold hover:underline"
                            :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                        >
                            Semua ({{ $stats['total_guides_count'] ?? 0 }}) &rarr;
                        </a>
                    </div>

                    @if ($topGuides->isNotEmpty())
                        <div class="space-y-2.5">
                            @foreach ($topGuides as $guide)
                                <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-750 flex items-center justify-between gap-3 hover:bg-slate-100/70 dark:hover:bg-slate-800 transition">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl text-[9px] font-black uppercase tracking-wider shrink-0 {{ in_array($guide->file_type, ['pdf']) ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400' }}">
                                            {{ strtoupper($guide->file_type) }}
                                        </span>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $guide->title }}</h4>
                                            <p class="text-[10px] text-slate-400 mt-0.5 truncate">
                                                {{ $guide->download_count }}x diunduh
                                            </p>
                                        </div>
                                    </div>
                                    <a
                                        href="{{ route('guides.download', $guide) }}"
                                        class="shrink-0 inline-flex items-center gap-1 rounded-xl px-2.5 py-1.5 text-[11px] font-bold text-white shadow-sm transition active:scale-95"
                                        :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                                        title="Unduh Berkas {{ $guide->file_name }}"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Unduh</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-3 text-center bg-slate-50/50 dark:bg-slate-900/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700/80 p-3">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum ada berkas panduan</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Panduan resmi akan otomatis tampil di sini saat dirilis admin.</p>
                        </div>
                    @endif
                </div>

                {{-- 5. Dedicated Technical Support Helpdesk --}}
                <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white rounded-[2.5rem] p-6 shadow-2xl shadow-black/20 flex flex-col justify-between border border-slate-800">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-950/80 px-2.5 py-1 rounded-full border border-emerald-800/60">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                Tim Teknis Standby
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">08.00 - 17.00 WIB</span>
                        </div>

                        <div>
                            <h4 class="text-sm font-black text-white">Butuh Bantuan Instalasi &amp; Domain?</h4>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                                Tim teknis Veyra siap mendampingi integrasi domain sekolah, setup akun ujian, dan pelatihan proktor.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-800 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">WhatsApp CS Resmi</p>
                            <p class="text-[10px] text-slate-400 font-mono">0857-0707-4778</p>
                        </div>
                        <a
                            href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate,%20saya%20membutuhkan%20bantuan%20teknis%20sekolah%20{{ urlencode($school?->name ?? 'saya') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black px-4 py-2 text-xs transition shadow-lg active:scale-95 shrink-0 flex items-center gap-1.5"
                        >
                            <span>Chat Sekarang</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

        </div>

        {{-- Alpine.js Announcement Reader Popup Modal --}}
        <div
            x-show="announcementModalOpen"
            x-cloak
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-sm safe-area-bottom"
            @click.self="announcementModalOpen = false"
            @keydown.escape.window="announcementModalOpen = false"
        >
            <div
                x-show="announcementModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-2xl border border-slate-200/80 dark:border-slate-800 space-y-4 max-h-[85vh] flex flex-col"
            >
                {{-- Header with Close Button --}}
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <span
                            class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider mb-1"
                            :style="'color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '; background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '20'"
                        >
                            Pengumuman Resmi
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-snug" x-text="activeAnnouncement?.title"></h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-medium" x-text="'Dipublikasikan: ' + (activeAnnouncement?.date || '-')"></p>
                    </div>
                    <button
                        type="button"
                        @click="announcementModalOpen = false"
                        class="rounded-xl p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                        aria-label="Tutup"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Full Announcement Body --}}
                <div class="py-2 text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed max-h-72 overflow-y-auto whitespace-pre-line" x-text="activeAnnouncement?.body">
                </div>

                {{-- Footer Action --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                    <button
                        type="button"
                        @click="announcementModalOpen = false"
                        class="rounded-xl px-4 py-2 text-xs font-bold text-white shadow-sm transition active:scale-95 cursor-pointer"
                        :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Plan change modal --}}
    <x-plan-modal :plans="$plans" :current-plan="$active?->plan" title="Ganti paket" />
</x-app-layout>
