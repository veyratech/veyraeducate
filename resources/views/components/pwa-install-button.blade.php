@props([
    'variant' => 'pill', // 'pill' | 'sidebar' | 'topbar' | 'banner'
])

<div
    x-data="{
        canInstall: false,
        isInstalled: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
        platform: 'desktop', // 'android', 'ios', 'desktop'
        showModal: false,
        bannerDismissed: localStorage.getItem('veyra_pwa_banner_dismissed') === 'true',
        init() {
            const ua = (navigator.userAgent || navigator.vendor || window.opera || '').toLowerCase();
            if (/android/i.test(ua)) {
                this.platform = 'android';
            } else if (/iphone|ipad|ipod/i.test(ua) && !window.MSStream) {
                this.platform = 'ios';
            } else {
                this.platform = 'desktop';
            }

            if (window.deferredPWAInstallPrompt) {
                this.canInstall = true;
            }

            window.addEventListener('pwa-installable', () => {
                this.canInstall = true;
            });

            window.addEventListener('pwa-installed', () => {
                this.canInstall = false;
                this.isInstalled = true;
                this.showModal = false;
            });

            window.addEventListener('open-pwa-install-modal', () => {
                this.installApp();
            });
        },
        async installApp() {
            if (window.deferredPWAInstallPrompt) {
                try {
                    window.deferredPWAInstallPrompt.prompt();
                    const choice = await window.deferredPWAInstallPrompt.userChoice;
                    if (choice && choice.outcome === 'accepted') {
                        this.canInstall = false;
                        this.isInstalled = true;
                        this.showModal = false;
                    }
                    window.deferredPWAInstallPrompt = null;
                } catch (e) {
                    // Prompt sudah dimakan browser (user tutup banner Chrome secara tidak sengaja)
                    // Tampilkan modal panduan manual sebagai fallback
                    window.deferredPWAInstallPrompt = null;
                    this.showModal = true;
                }
            } else {
                this.showModal = true;
            }
        },
        dismissBanner() {
            this.bannerDismissed = true;
            localStorage.setItem('veyra_pwa_banner_dismissed', 'true');
        }
    }"
    x-show="!isInstalled"
    class="{{ $variant === 'banner' ? 'w-full' : ($variant === 'sidebar' ? 'w-full' : 'inline-block') }}"
>
    {{-- VARIANT: TOPBAR (Khusus Bar Atas Mobile) --}}
    @if ($variant === 'topbar')
        <button
            type="button"
            @click="installApp()"
            class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-emerald-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 dark:text-slate-400 dark:hover:bg-slate-800 transition cursor-pointer"
            :title="platform === 'android' ? 'Pasang Aplikasi Android' : (platform === 'ios' ? 'Simpan ke Layar Utama iPhone' : 'Pasang Software Desktop')"
        >
            <template x-if="platform === 'android'">
                <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0001 0-.5517.4482-1.0001.9993-1.0001.5514 0 .9997.4484.9997 1.0001 0 .5515-.4483 1.0001-.9997 1.0001m-11.046 0c-.5514 0-.9997-.4486-.9997-1.0001 0-.5517.4483-1.0001.9997-1.0001.5511 0 .9993.4484.9993 1.0001 0 .5515-.4482 1.0001-.9993 1.0001m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.1 12 8.1c-1.8533 0-3.5902.3116-5.1378.8497L4.8399 5.4467a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                </svg>
            </template>
            <template x-if="platform !== 'android'">
                <svg class="h-5 w-5 shrink-0 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </template>
        </button>

    {{-- VARIANT: SIDEBAR (Menu Samping Navigasi) --}}
    @elseif ($variant === 'sidebar')
        <button
            type="button"
            @click="installApp()"
            class="w-full flex items-center justify-between gap-2.5 rounded-xl bg-white/10 hover:bg-white/20 !text-white font-semibold px-3.5 py-2.5 text-xs transition border border-white/10 cursor-pointer shadow-sm group active:scale-98"
            :title="platform === 'android' ? 'Pasang Veyra Educate sebagai Aplikasi Android' : (platform === 'ios' ? 'Simpan ke Layar Utama iPhone' : 'Pasang Veyra Educate sebagai Software Desktop')"
        >
            <div class="flex items-center gap-2.5 min-w-0">
                <template x-if="platform === 'android'">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500/25 border border-emerald-400/40 text-emerald-300">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0001 0-.5517.4482-1.0001.9993-1.0001.5514 0 .9997.4484.9997 1.0001 0 .5515-.4483 1.0001-.9997 1.0001m-11.046 0c-.5514 0-.9997-.4486-.9997-1.0001 0-.5517.4483-1.0001.9997-1.0001.5511 0 .9993.4484.9993 1.0001 0 .5515-.4482 1.0001-.9993 1.0001m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.1 12 8.1c-1.8533 0-3.5902.3116-5.1378.8497L4.8399 5.4467a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                        </svg>
                    </div>
                </template>
                <template x-if="platform === 'desktop'">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/15 border border-white/20 text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="12" rx="2" ry="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v4m0 0l-2-2m2 2l2-2M8 19h8m-4-4v4"/>
                        </svg>
                    </div>
                </template>
                <template x-if="platform === 'ios'">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/15 border border-white/20 text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01"/>
                        </svg>
                    </div>
                </template>
                <div class="text-left leading-tight truncate">
                    <span class="block text-xs font-bold text-white truncate" x-text="platform === 'android' ? 'Pasang App Android' : (platform === 'ios' ? 'Simpan App iPhone' : 'Pasang Software PC')"></span>
                    <span class="block text-[10px] text-white/70 truncate" x-text="platform === 'android' ? 'Aplikasi Layar Utama' : (platform === 'ios' ? 'Tambahkan ke Home' : 'Aplikasi Mandiri Windows')"></span>
                </div>
            </div>
            <svg class="h-4 w-4 shrink-0 text-white/60 group-hover:text-white group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

    {{-- VARIANT: BANNER (Smart Alert Card di Dashboard Mobile) --}}
    @elseif ($variant === 'banner')
        <div
            x-show="!bannerDismissed"
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-4 text-white shadow-lg shadow-teal-900/10 transition lg:hidden"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 backdrop-blur-md text-white shadow-inner">
                        <template x-if="platform === 'android'">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0001 0-.5517.4482-1.0001.9993-1.0001.5514 0 .9997.4484.9997 1.0001 0 .5515-.4483 1.0001-.9997 1.0001m-11.046 0c-.5514 0-.9997-.4486-.9997-1.0001 0-.5517.4483-1.0001.9997-1.0001.5511 0 .9993.4484.9993 1.0001 0 .5515-.4482 1.0001-.9993 1.0001m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.1 12 8.1c-1.8533 0-3.5902.3116-5.1378.8497L4.8399 5.4467a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                            </svg>
                        </template>
                        <template x-if="platform !== 'android'">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </template>
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-white" x-text="platform === 'android' ? 'Pasang Aplikasi di Layar HP Android' : (platform === 'ios' ? 'Simpan Aplikasi ke Home Screen iPhone' : 'Pasang Software Veyra Educate')"></h4>
                        <p class="text-[11px] text-white/90 mt-0.5 leading-snug">
                            Buka CBT &amp; Raport lebih cepat tanpa membuka browser lagi.
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="dismissBanner()"
                    class="rounded-lg p-1 text-white/70 hover:text-white hover:bg-white/10 transition cursor-pointer"
                    aria-label="Tutup notifikasi pasang aplikasi"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <button
                    type="button"
                    @click="installApp()"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-white text-slate-900 font-extrabold px-3.5 py-2 text-xs shadow-md hover:bg-white/95 transition active:scale-95 cursor-pointer"
                >
                    <span x-text="platform === 'android' ? '📲 Pasang Sekarang ke Android' : (platform === 'ios' ? '📲 Panduan Pasang iPhone' : '💻 Pasang Aplikasi PC')"></span>
                </button>
            </div>
        </div>

    {{-- VARIANT: PILL (Default Navbar / Header) --}}
    @else
        <button
            type="button"
            @click="installApp()"
            {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-xl border border-brand-200 dark:border-brand-800 bg-brand-50/80 dark:bg-brand-950/60 px-3.5 py-1.5 text-xs font-bold text-brand-700 dark:text-brand-300 transition hover:bg-brand-100 dark:hover:bg-brand-900/50 hover:border-brand-300 shadow-sm cursor-pointer active:scale-95']) }}
            :title="platform === 'android' ? 'Pasang Aplikasi Android' : (platform === 'ios' ? 'Simpan ke Layar Utama iPhone' : 'Pasang Software Desktop')"
        >
            <template x-if="platform === 'android'">
                <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0001 0-.5517.4482-1.0001.9993-1.0001.5514 0 .9997.4484.9997 1.0001 0 .5515-.4483 1.0001-.9997 1.0001m-11.046 0c-.5514 0-.9997-.4486-.9997-1.0001 0-.5517.4483-1.0001.9997-1.0001.5511 0 .9993.4484.9993 1.0001 0 .5515-.4482 1.0001-.9993 1.0001m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.1 12 8.1c-1.8533 0-3.5902.3116-5.1378.8497L4.8399 5.4467a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                </svg>
            </template>
            <template x-if="platform === 'desktop'">
                <svg class="h-4 w-4 text-brand-600 dark:text-brand-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="12" rx="2" ry="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v4m0 0l-2-2m2 2l2-2M8 19h8m-4-4v4"/>
                </svg>
            </template>
            <template x-if="platform === 'ios'">
                <svg class="h-4 w-4 text-slate-600 dark:text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01"/>
                </svg>
            </template>
            <span x-text="platform === 'android' ? 'Pasang App Android' : (platform === 'ios' ? 'Simpan App iPhone' : 'Pasang Software PC')"></span>
        </button>
    @endif

    {{-- MODAL PANDUAN INSTALASI PWA (INTERAKTIF & ADAPTIF PERANGKAT) --}}
    <template x-teleport="body">
        <div
            x-show="showModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md"
            @click.self="showModal = false"
            role="dialog"
            aria-modal="true"
        >
            <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xl p-6 text-slate-800 dark:text-slate-100 overflow-hidden"
        >
            {{-- Header Modal --}}
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl shadow-inner"
                        :class="platform === 'android' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-brand-500/15 text-brand-600 dark:text-brand-400 border border-brand-500/20'"
                    >
                        <template x-if="platform === 'android'">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-1.0001 0-.5517.4482-1.0001.9993-1.0001.5514 0 .9997.4484.9997 1.0001 0 .5515-.4483 1.0001-.9997 1.0001m-11.046 0c-.5514 0-.9997-.4486-.9997-1.0001 0-.5517.4483-1.0001.9997-1.0001.5511 0 .9993.4484.9993 1.0001 0 .5515-.4482 1.0001-.9993 1.0001m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.4116 13.8533 8.1 12 8.1c-1.8533 0-3.5902.3116-5.1378.8497L4.8399 5.4467a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589 0 18.761h24c-.3432-4.1021-2.6889-7.5743-6.1185-9.4396"/>
                            </svg>
                        </template>
                        <template x-if="platform === 'desktop'">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="12" rx="2" ry="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v4m0 0l-2-2m2 2l2-2M8 19h8m-4-4v4"/>
                            </svg>
                        </template>
                        <template x-if="platform === 'ios'">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="5" y="2" width="14" height="20" rx="3" ry="3"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01"/>
                            </svg>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold" x-text="platform === 'android' ? 'Pasang di Android' : (platform === 'ios' ? 'Pasang di iPhone' : 'Pasang di Komputer / Laptop')"></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Jadikan Veyra Educate aplikasi mandiri</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="showModal = false"
                    class="rounded-xl p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    aria-label="Tutup dialog"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- KONTEN PANDUAN: ANDROID --}}
            <template x-if="platform === 'android'">
                <div class="space-y-3.5 my-4">
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Anda dapat memasang Veyra Educate langsung ke layar HP Android tanpa melalui Google Play Store:
                    </p>
                    <ol class="space-y-2.5 text-xs text-slate-700 dark:text-slate-200">
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-[10px]">1</span>
                            <span>Buka situs ini di browser <strong>Google Chrome</strong> HP Anda.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-[10px]">2</span>
                            <span>Ketuk ikon <strong>titik tiga (⋮)</strong> di pojok kanan atas browser.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-[10px]">3</span>
                            <span>Pilih menu <strong>"Instal Aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-[10px]">✓</span>
                            <span>Selesai! Ikon aplikasi akan langsung muncul di beranda dan laci aplikasi HP Anda.</span>
                        </li>
                    </ol>
                </div>
            </template>

            {{-- KONTEN PANDUAN: DESKTOP --}}
            <template x-if="platform === 'desktop'">
                <div class="space-y-3.5 my-4">
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pasang Veyra Educate sebagai software mandiri di Windows / Mac / Linux:
                    </p>
                    <ol class="space-y-2.5 text-xs text-slate-700 dark:text-slate-200">
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white font-bold text-[10px]">1</span>
                            <span>Buka di browser <strong>Google Chrome</strong> atau <strong>Microsoft Edge</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white font-bold text-[10px]">2</span>
                            <span>Lihat di sebelah kanan bilah URL browser, klik ikon <strong>Pasang (Komputer dengan panah bawah)</strong>.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white font-bold text-[10px]">3</span>
                            <span>Klik <strong>Instal</strong>. Aplikasi akan otomatis muncul di Start Menu dan Desktop.</span>
                        </li>
                    </ol>
                </div>
            </template>

            {{-- KONTEN PANDUAN: IOS --}}
            <template x-if="platform === 'ios'">
                <div class="space-y-3.5 my-4">
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pasang Veyra Educate di iPhone / iPad (Safari):
                    </p>
                    <ol class="space-y-2.5 text-xs text-slate-700 dark:text-slate-200">
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-700 text-white font-bold text-[10px]">1</span>
                            <span>Buka situs ini di browser <strong>Safari</strong> iPhone Anda.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-700 text-white font-bold text-[10px]">2</span>
                            <span>Ketuk tombol <strong>Bagikan (Share)</strong> di bagian bawah layar.</span>
                        </li>
                        <li class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-700 text-white font-bold text-[10px]">3</span>
                            <span>Gulir dan pilih <strong>"Tambahkan ke Layar Utama" (Add to Home Screen)</strong>.</span>
                        </li>
                    </ol>
                </div>
            </template>

            {{-- Action Footer --}}
            <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                <button
                    type="button"
                    @click="showModal = false"
                    class="rounded-xl px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
</div>
