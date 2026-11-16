@props([
'variant' => 'topbar',
])

@php
$cbtUrl = config('sso.cbt_url', 'https://cbt.veyracreative.site');
$raportUrl = 'https://raport.veyracreative.site';
@endphp

<div
    x-data="{
        open: false,
        loading: false,
        lastChecked: null,
        health: {
            cbt: { status: 'online', latency_ms: 36, name: 'CBT Online' },
            raport: { status: 'online', latency_ms: 42, name: 'E-Rapor' },
            sso: { status: 'active', mode: 'JWT HS256' }
        },
        init() {
            this.checkHealth(false);
        },
        async checkHealth(showSpinner = true) {
            if (showSpinner) this.loading = true;
            try {
                const res = await fetch('{{ route('subdomain.status') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.services) {
                        data.services.forEach(s => {
                            if (s.key === 'cbt') this.health.cbt = s;
                            if (s.key === 'raport') this.health.raport = s;
                        });
                    }
                    if (data.sso) this.health.sso = data.sso;
                    this.lastChecked = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                }
            } catch (err) {
                console.debug('Health check fallback:', err);
            } finally {
                if (showSpinner) this.loading = false;
            }
        }
    }"
    @click.outside="open = false"
    class="relative">
    {{-- Trigger Button (Waffle 9-dots) --}}
    <button
        type="button"
        @click="open = !open"
        class="relative inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white transition cursor-pointer group"
        :class="open ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white ring-2 ring-brand-500/20' : ''"
        aria-label="Menu Aplikasi & Ekosistem Sekolah"
        title="App Launcher — Buka CBT, Rapor, dan Layanan Sekolah">
        {{-- 9-Dots Waffle Icon --}}
        <svg class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" viewBox="0 0 24 24" fill="currentColor">
            <circle cx="5" cy="5" r="2" />
            <circle cx="12" cy="5" r="2" />
            <circle cx="19" cy="5" r="2" />
            <circle cx="5" cy="12" r="2" />
            <circle cx="12" cy="12" r="2" />
            <circle cx="19" cy="12" r="2" />
            <circle cx="5" cy="19" r="2" />
            <circle cx="12" cy="19" r="2" />
            <circle cx="19" cy="19" r="2" />
        </svg>

        {{-- Live Status Dot Indicator --}}
        <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
    </button>

    {{-- Dropdown Modal / Popover --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        x-cloak
        class="absolute right-0 top-full mt-2.5 w-[330px] sm:w-[380px] rounded-3xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xl ring-1 ring-slate-900/10 dark:ring-white/10 z-50 overflow-hidden">
        {{-- Popover Header --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-850/60 flex items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-black text-xs text-slate-900 dark:text-white uppercase tracking-wider">
                        Ekosistem Sekolah
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Terhubung
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Akses langsung seluruh sistem tanpa login ulang (SSO)
                </p>
            </div>

            <button
                type="button"
                @click="checkHealth(true)"
                class="p-1.5 rounded-xl text-slate-400 hover:text-brand-600 hover:bg-white dark:hover:bg-slate-800 transition cursor-pointer"
                title="Periksa Ulang Status Server Subdomain">
                <svg
                    class="h-4 w-4"
                    :class="loading ? 'animate-spin text-brand-600' : ''"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        {{-- App Grid (6 Primary Modules) --}}
        <div class="p-4 grid grid-cols-2 gap-2.5 max-h-[360px] overflow-y-auto">

            {{-- 1. CBT Online (Subdomain) --}}
            <a
                href="{{ route('sso.launch', 'cbtonline') }}"
                target="_blank"
                class="group flex flex-col p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-850 hover:border-emerald-400 dark:hover:border-emerald-600 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-200 relative overflow-hidden text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider flex items-center gap-1"
                        :class="health.cbt.status === 'online' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300'">
                        <span class="h-1.5 w-1.5 rounded-full" :class="health.cbt.status === 'online' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                        <span x-text="health.cbt.latency_ms ? health.cbt.latency_ms + 'ms' : 'Live'"></span>
                    </span>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    CBT Online
                </h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">
                    Asesmen & Bank Soal
                </p>
                <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[9px] font-bold text-emerald-600 dark:text-emerald-400">
                    <span>1-Klik SSO</span>
                    <svg class="h-3 w-3 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </a>

            {{-- 2. E-Raport Kurikulum (Subdomain) --}}
            <a
                href="{{ route('sso.launch', 'raport') }}"
                target="_blank"
                class="group flex flex-col p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-850 hover:border-blue-400 dark:hover:border-blue-600 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-200 relative overflow-hidden text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider flex items-center gap-1"
                        :class="health.raport.status === 'online' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300'">
                        <span class="h-1.5 w-1.5 rounded-full" :class="health.raport.status === 'online' ? 'bg-blue-500' : 'bg-amber-500'"></span>
                        <span x-text="health.raport.latency_ms ? health.raport.latency_ms + 'ms' : 'Live'"></span>
                    </span>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    E-Raport Kurikulum
                </h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">
                    Kurikulum &amp; Nilai
                </p>
                <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[9px] font-bold text-blue-600 dark:text-blue-400">
                    <span>1-Klik SSO</span>
                    <svg class="h-3 w-3 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </a>

            {{-- 3. Card Studio (Cetak Kartu Ujian & Pelajar) --}}
            <a
                href="{{ route('school.cards.index') }}"
                @click="open = false"
                class="group flex flex-col p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-850 hover:border-amber-400 dark:hover:border-amber-600 hover:shadow-lg hover:shadow-amber-500/10 transition-all duration-200 text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white shadow-md shadow-amber-500/25 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        Studio
                    </span>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                    Card Studio
                </h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">
                    Cetak Kartu A4
                </p>
                <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[9px] font-bold text-slate-500 dark:text-slate-400">
                    <span>Template Baru</span>
                    <svg class="h-3 w-3 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            {{-- 4. WhatsApp Broadcast Center --}}
            <a
                href="{{ route('school.broadcast.index') }}"
                @click="open = false"
                class="group flex flex-col p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-850 hover:border-emerald-400 dark:hover:border-emerald-600 hover:shadow-lg hover:shadow-emerald-500/10 transition-all duration-200 text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-emerald-500 to-green-600 text-white shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                        WA Gateway
                    </span>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    Broadcast WA
                </h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">
                    Notifikasi Orang Tua
                </p>
                <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[9px] font-bold text-slate-500 dark:text-slate-400">
                    <span>Kirim Massal</span>
                    <svg class="h-3 w-3 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            {{-- 5. Pusat Panduan --}}
            <a
                href="{{ route('guides.index') }}"
                @click="open = false"
                class="group flex flex-col p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-850 hover:border-violet-400 dark:hover:border-violet-600 hover:shadow-lg hover:shadow-violet-500/10 transition-all duration-200 text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-violet-600 to-purple-500 text-white shadow-md shadow-violet-500/25 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">
                    Pusat Panduan
                </h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">
                    SOP &amp; E-Book
                </p>
                <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[9px] font-bold text-slate-500 dark:text-slate-400">
                    <span>Dokumen</span>
                    <svg class="h-3 w-3 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

            {{-- 6. Pengaturan Sekolah --}}
            <a
                href="{{ route('school.settings') }}"
                @click="open = false"
                class="group flex flex-col p-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-850 hover:border-slate-400 dark:hover:border-slate-600 hover:shadow-lg hover:shadow-slate-500/10 transition-all duration-200 text-left">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-slate-700 to-slate-900 text-white shadow-md shadow-slate-900/25 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors">
                    Pengaturan
                </h4>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 mt-0.5">
                    Logo &amp; Profil
                </p>
                <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[9px] font-bold text-slate-500 dark:text-slate-400">
                    <span>Kelola</span>
                    <svg class="h-3 w-3 -translate-x-1 group-hover:translate-x-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>

        </div>

        {{-- Subdomain Health & SSO Footer --}}
        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-850/80 flex items-center justify-between text-[10px]">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="font-bold text-slate-600 dark:text-slate-400">
                    SSO: <span class="text-emerald-600 dark:text-emerald-400 font-extrabold" x-text="health.sso.mode">JWT HS256</span>
                </span>
            </div>
            <a
                href="{{ route('apps.index') }}"
                @click="open = false"
                class="font-extrabold text-brand-600 dark:text-brand-400 hover:underline flex items-center gap-1">
                <span>Lihat App Hub</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>
</div>