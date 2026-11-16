@php
    $isAdmin = auth()->check() && auth()->user()?->isAdmin();
    $school = auth()->user()?->school;

    // Fetch dynamic web apps (school-installed apps for schools, all deployed for admin)
    $dynamicApps = [];
    try {
        if ($isAdmin) {
            $installer = app(\App\Services\SchoolAppInstaller::class);
            $dynamicApps = \App\Models\WebApp::enabled()
                ->orderBy('sort_order')
                ->get()
                ->filter(fn (\App\Models\WebApp $app) => $app->access_url !== null || $installer->getAppPath($app->slug, $app) !== null)
                ->values();
        } elseif ($school) {
            $dynamicApps = $school->webApps()->where('enabled', true)->get();
        }
    } catch (\Throwable $e) {
        $dynamicApps = collect();
    }
@endphp

<div
    x-data="commandPalette()"
    x-init="initPalette()"
    @keydown.window="handleGlobalKey($event)"
    @open-command-palette.window="openPalette()"
    x-cloak
>
    {{-- Modal Backdrop & Dialog Container --}}
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] overflow-y-auto p-3 sm:p-6 md:p-12 lg:p-20 bg-slate-900/60 backdrop-blur-md flex items-start justify-center"
        @click.self="closePalette()"
        role="dialog"
        aria-modal="true"
        aria-label="Pencarian Cepat dan Perintah"
    >
        {{-- Palette Box --}}
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
            class="w-full max-w-2xl transform overflow-hidden rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 shadow-2xl ring-1 ring-black/10 dark:ring-white/10 border border-slate-200/80 dark:border-slate-800 transition-all flex flex-col max-h-[85vh] sm:max-h-[80vh]"
            @keydown.escape.prevent="closePalette()"
            @keydown.down.prevent="selectNext()"
            @keydown.up.prevent="selectPrev()"
            @keydown.enter.prevent="executeCurrent()"
        >
            {{-- Search Bar Header --}}
            <div class="relative flex items-center border-b border-slate-100 dark:border-slate-800/80 px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/60 dark:text-brand-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <input
                    x-ref="searchInput"
                    x-model="search"
                    type="text"
                    placeholder="Cari modul (CBT, Rapor, SPP), menu, ganti tema, atau aksi..."
                    class="w-full border-0 bg-transparent px-3 sm:px-4 py-1.5 text-sm sm:text-base text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-0"
                    autocomplete="off"
                    spellcheck="false"
                />

                <div class="flex items-center gap-1.5 shrink-0">
                    {{-- Clear Query Button --}}
                    <button
                        x-show="search.length > 0"
                        @click="search = ''; $refs.searchInput.focus()"
                        type="button"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"
                        title="Hapus pencarian"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <kbd class="hidden sm:inline-flex items-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-0.5 text-[11px] font-semibold text-slate-400 dark:text-slate-400 shadow-xs">
                        ESC
                    </kbd>
                </div>
            </div>

            {{-- Category Filter Chips --}}
            <div class="flex items-center gap-1.5 px-4 sm:px-6 py-2.5 border-b border-slate-100 dark:border-slate-800 overflow-x-auto [scrollbar-width:none] shrink-0 text-xs font-semibold bg-white dark:bg-slate-900">
                <button
                    type="button"
                    @click="activeFilter = 'all'; selectedIndex = 0"
                    :class="activeFilter === 'all' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="rounded-xl px-3 py-1 transition cursor-pointer shrink-0"
                >
                    Semua
                </button>
                <button
                    type="button"
                    @click="activeFilter = 'apps'; selectedIndex = 0"
                    :class="activeFilter === 'apps' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="rounded-xl px-3 py-1 transition cursor-pointer shrink-0 flex items-center gap-1.5"
                >
                    <span>📱 Modul Aplikasi</span>
                </button>
                <button
                    type="button"
                    @click="activeFilter = 'pages'; selectedIndex = 0"
                    :class="activeFilter === 'pages' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="rounded-xl px-3 py-1 transition cursor-pointer shrink-0 flex items-center gap-1.5"
                >
                    <span>🧭 Halaman</span>
                </button>
                <button
                    type="button"
                    @click="activeFilter = 'actions'; selectedIndex = 0"
                    :class="activeFilter === 'actions' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="rounded-xl px-3 py-1 transition cursor-pointer shrink-0 flex items-center gap-1.5"
                >
                    <span>⚡ Aksi & Tema</span>
                </button>
                @if ($isAdmin)
                    <button
                        type="button"
                        @click="activeFilter = 'admin'; selectedIndex = 0"
                        :class="activeFilter === 'admin' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="rounded-xl px-3 py-1 transition cursor-pointer shrink-0 flex items-center gap-1.5"
                    >
                        <span>🛡️ Admin Panel</span>
                    </button>
                @endif
            </div>

            {{-- Results List Container --}}
            <div
                x-ref="resultsList"
                class="flex-1 overflow-y-auto px-2 sm:px-3 py-2 space-y-1 min-h-[160px] max-h-[50vh] divide-y divide-slate-50 dark:divide-slate-800/40"
            >
                {{-- If Results Exist --}}
                <template x-for="(item, index) in filteredItems" :key="item.id">
                    <div
                        @click="executeItem(item)"
                        @mouseenter="selectedIndex = index"
                        :class="selectedIndex === index ? 'bg-brand-50/80 dark:bg-brand-950/40 border-brand-500 text-brand-900 dark:text-brand-100 shadow-sm' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-transparent'"
                        class="group flex items-center justify-between gap-3 px-3 sm:px-4 py-2.5 sm:py-3 rounded-2xl border-l-4 transition-all cursor-pointer"
                        role="option"
                        :aria-selected="selectedIndex === index"
                    >
                        {{-- Left Item Details --}}
                        <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                            {{-- Icon / Badge Box --}}
                            <div
                                :class="item.iconBg || 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl text-base sm:text-lg shadow-xs group-hover:scale-105 transition-transform"
                                x-html="item.iconHtml"
                            ></div>

                            <div class="min-w-0 flex-1 leading-tight">
                                <div class="flex items-center gap-2">
                                    <span class="truncate text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-brand-700 dark:group-hover:text-brand-300" x-text="item.title"></span>
                                    <span
                                        x-show="item.badge"
                                        x-text="item.badge"
                                        :class="item.badgeClass || 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'"
                                        class="rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider shrink-0"
                                    ></span>
                                </div>
                                <p class="truncate text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="item.subtitle"></p>
                            </div>
                        </div>

                        {{-- Right Category Label & Jump Icon --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="hidden md:inline-block text-[11px] font-semibold text-slate-400 dark:text-slate-500" x-text="item.categoryLabel"></span>
                            <div
                                :class="selectedIndex === index ? 'opacity-100 translate-x-0' : 'opacity-40 -translate-x-1'"
                                class="h-6 w-6 rounded-lg flex items-center justify-center text-slate-400 group-hover:text-brand-600 transition-all"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State (No matches found) --}}
                <div x-show="filteredItems.length === 0" class="py-12 px-4 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 shadow-sm mb-3">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100">
                        Tidak ada hasil untuk "<span class="text-brand-600 dark:text-brand-400" x-text="search"></span>"
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Coba gunakan kata kunci umum seperti <span class="font-semibold text-slate-700 dark:text-slate-300">CBT, Rapor, Presensi, Tema, WhatsApp</span>, atau reset filter kategori.
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-1.5">
                        <button type="button" @click="search = 'CBT'; activeFilter = 'all'" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">CBT</button>
                        <button type="button" @click="search = 'Rapor'; activeFilter = 'all'" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">Rapor</button>
                        <button type="button" @click="search = 'Tema'; activeFilter = 'all'" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">Tema</button>
                        <button type="button" @click="search = 'Panduan'; activeFilter = 'all'" class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">Panduan</button>
                    </div>
                </div>
            </div>

            {{-- Footer Keyboard Guide & Result Counter --}}
            <div class="border-t border-slate-100 dark:border-slate-800 px-4 sm:px-6 py-3 bg-slate-50/70 dark:bg-slate-900/70 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-4">
                    <span class="hidden sm:inline-flex items-center gap-1.5">
                        <kbd class="rounded bg-white dark:bg-slate-800 px-1.5 py-0.5 font-bold shadow-xs border border-slate-200 dark:border-slate-700">↑↓</kbd> Navigasi
                    </span>
                    <span class="hidden sm:inline-flex items-center gap-1.5">
                        <kbd class="rounded bg-white dark:bg-slate-800 px-1.5 py-0.5 font-bold shadow-xs border border-slate-200 dark:border-slate-700">↵</kbd> Buka
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <kbd class="rounded bg-white dark:bg-slate-800 px-1.5 py-0.5 font-bold shadow-xs border border-slate-200 dark:border-slate-700">ESC</kbd> Tutup
                    </span>
                </div>

                <div>
                    <span x-text="filteredItems.length + ' hasil ditemukan'"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function commandPalette() {
        return {
            isOpen: false,
            search: '',
            selectedIndex: 0,
            activeFilter: 'all',
            items: [
                {{-- 1. Modul Aplikasi Sekolah --}}
                {
                    id: 'app-cbt',
                    title: 'CBT & Ujian Berbasis Komputer',
                    subtitle: 'Bank soal, jadwal ujian online, pengacakan nomor, & analisis butir soal',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '📝',
                    iconBg: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
                    badge: 'Siap Pakai',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['cbt', 'ujian', 'soal', 'exam', 'tes', 'asesmen', 'evaluasi']
                },
                {
                    id: 'app-rapor',
                    title: 'E-Rapor Kurikulum Merdeka',
                    subtitle: 'Penginputan TP/CP, nilai sumatif & formatif, cetak rapor resmi',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '📊',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'Populer',
                    badgeClass: 'bg-brand-100 text-brand-800 dark:bg-brand-950/60 dark:text-brand-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['rapor', 'raport', 'nilai', 'kurikulum', 'merdeka', 'ijazah', 'ledger']
                },
                {
                    id: 'app-presensi',
                    title: 'Presensi Digital Siswa & Guru',
                    subtitle: 'Absensi QR Code harian, integrasi WhatsApp ortu, & rekap bulanan',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '📅',
                    iconBg: 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                    badge: 'Online',
                    badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['presensi', 'absen', 'kehadiran', 'qr', 'barcode', 'dispensasi', 'izin']
                },
                {
                    id: 'app-spp',
                    title: 'SPP & Keuangan Sekolah',
                    subtitle: 'Tagihan iuran bulanan, buku kas digital, kwitansi & laporan keuangan',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '💳',
                    iconBg: 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                    badge: 'Kas',
                    badgeClass: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['spp', 'keuangan', 'bayar', 'tagihan', 'kas', 'kwitansi', 'infaq', 'uang']
                },
                {
                    id: 'app-perpus',
                    title: 'Perpustakaan Digital (E-Perpus)',
                    subtitle: 'Katalog buku, pencatatan peminjaman, cetak barcode kartu pustaka',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '📚',
                    iconBg: 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300',
                    badge: 'Modul',
                    badgeClass: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['perpus', 'perpustakaan', 'buku', 'pinjam', 'katalog', 'literasi']
                },
                {
                    id: 'app-kepegawaian',
                    title: 'SIM Kepegawaian & Guru (SIMPEG)',
                    subtitle: 'Database GTK, riwayat kenaikan pangkat, sertifikasi, & jadwal mengajar',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '👥',
                    iconBg: 'bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300',
                    badge: 'GTK',
                    badgeClass: 'bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['simpeg', 'kepegawaian', 'guru', 'gtk', 'pegawai', 'sk', 'sertifikasi']
                },
                {
                    id: 'app-request',
                    title: 'Ajukan Modul / Aplikasi Kustom',
                    subtitle: 'Minta integrasi sistem atau modul khusus untuk kebutuhan sekolah Anda',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '💡',
                    iconBg: 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                    badge: 'Kustom',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('school-app.requests.index') }}',
                    keywords: ['request', 'minta', 'kustom', 'aplikasi baru', 'fitur', 'usulan']
                },

                @foreach ($dynamicApps as $dApp)
                {
                    id: 'dynamic-app-{{ $dApp->slug }}',
                    title: '{{ addslashes($dApp->name) }}',
                    subtitle: '{{ addslashes(Str::limit($dApp->description ?? 'Modul aplikasi sekolah terintegrasi', 70)) }}',
                    category: 'apps',
                    categoryLabel: 'Modul Aplikasi',
                    iconHtml: '{{ $dApp->icon ? addslashes($dApp->icon) : '📦' }}',
                    iconBg: 'bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300',
                    badge: '{{ addslashes($dApp->category ?? 'Modul') }}',
                    badgeClass: 'bg-brand-100 text-brand-800 dark:bg-brand-950/60 dark:text-brand-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['{{ strtolower(addslashes($dApp->name)) }}', '{{ strtolower(addslashes($dApp->slug)) }}', '{{ strtolower(addslashes($dApp->category ?? '')) }}']
                },
                @endforeach

                {{-- 2. Halaman & Menu Utama --}}
                {
                    id: 'nav-dashboard',
                    title: 'Dashboard Utama',
                    subtitle: 'Ringkasan aktivitas sekolah, langganan aktif, & widget progres',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '🏠',
                    iconBg: 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300',
                    badge: 'Beranda',
                    badgeClass: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                    url: '{{ route('dashboard') }}',
                    keywords: ['dashboard', 'home', 'beranda', 'utama', 'aktivitas']
                },
                {
                    id: 'nav-apps',
                    title: 'Katalog Semua Aplikasi & Modul',
                    subtitle: 'Daftar seluruh modul sekolah, peluncuran cepat, dan panduan akses',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '🗂️',
                    iconBg: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
                    badge: 'Hub',
                    badgeClass: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300',
                    url: '{{ route('apps.index') }}',
                    keywords: ['katalog', 'daftar aplikasi', 'hub', 'modul']
                },
                {
                    id: 'nav-guides',
                    title: 'Buku Panduan & Dokumentasi',
                    subtitle: 'Tutorial langkah demi langkah penggunaan sistem untuk operator & guru',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '📖',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'Bantuan',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    url: '{{ route('guides.index') }}',
                    keywords: ['panduan', 'buku', 'guide', 'tutorial', 'manual', 'dokumentasi', 'cara']
                },
                {
                    id: 'nav-subscription',
                    title: 'Paket Langganan & Upgrade',
                    subtitle: 'Status paket aktif, masa berlaku, dan perpanjangan langganan',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '👑',
                    iconBg: 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                    badge: 'Paket',
                    badgeClass: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                    url: '{{ route('checkout.show', 'super') }}',
                    keywords: ['langganan', 'paket', 'upgrade', 'tagihan', 'perpanjang', 'harga']
                },
                {
                    id: 'nav-billing-history',
                    title: 'Riwayat Tagihan & Kwitansi Resmi',
                    subtitle: 'Arsip bukti bayar sah ber-KOP PT. Veyra Creative Tech untuk SPJ BOS & Yayasan',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '🧾',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'Kwitansi',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    url: '{{ route('pembayaran.history') }}',
                    keywords: ['tagihan', 'kwitansi', 'invoice', 'bos', 'spj', 'lpj', 'arsip', 'bukti bayar', 'pembayaran']
                },
                {
                    id: 'nav-settings',
                    title: 'Pengaturan Sekolah',
                    subtitle: 'Identitas lembaga, upload logo, kop surat, NPSN, & alamat sekolah',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '⚙️',
                    iconBg: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                    badge: 'Profil',
                    badgeClass: 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
                    url: '{{ route('school.settings') }}',
                    keywords: ['pengaturan', 'sekolah', 'setting', 'npsn', 'logo', 'lembaga']
                },
                {
                    id: 'nav-profile',
                    title: 'Profil Akun & Keamanan',
                    subtitle: 'Ubah nama pengguna, email, dan kata sandi login',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '👤',
                    iconBg: 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                    badge: 'Akun',
                    badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                    url: '{{ route('profile.edit') }}',
                    keywords: ['profil', 'profile', 'akun', 'password', 'kata sandi', 'email']
                },
                {
                    id: 'nav-cards',
                    title: 'Cetak Kartu Siswa & Peserta Ujian Massal',
                    subtitle: 'Generator kartu pelajar & kartu ujian A4 (8 kartu/lembar) barcode NISN otomatis',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '🪪',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'Baru',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    url: '{{ route('school.cards.index') }}',
                    keywords: ['cetak kartu', 'kartu pelajar', 'kartu ujian', 'barcode', 'nisn', 'print', 'a4', 'peserta ujian']
                },
                {
                    id: 'nav-broadcast',
                    title: 'WhatsApp Broadcast & Notifikasi Sekolah',
                    subtitle: 'Kirim jadwal CBT, info gladi bersih, SPP, & rapor via WhatsApp sekali klik',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '💬',
                    iconBg: 'bg-green-100 text-green-700 dark:bg-green-950/60 dark:text-green-300',
                    badge: 'Baru',
                    badgeClass: 'bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300',
                    url: '{{ route('school.broadcast.index') }}',
                    keywords: ['broadcast', 'whatsapp', 'notifikasi', 'wa', 'pengumuman', 'jadwal cbt', 'spp']
                },
                {
                    id: 'nav-referral',
                    title: 'Program Afiliasi & Referral Operator',
                    subtitle: 'Bagikan link unik sekolah, raih komisi 10% atau voucher diskon langganan',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '🎁',
                    iconBg: 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                    badge: 'Komisi',
                    badgeClass: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
                    url: '{{ route('school.referral.index') }}',
                    keywords: ['referral', 'afiliasi', 'komisi', 'reward', 'operator', 'saldo', 'mitra']
                },
                {
                    id: 'nav-proposal',
                    title: 'Generator Surat Penawaran & Proposal BOS',
                    subtitle: 'Surat resmi berstempel digital & kalkulator efisiensi penghematan dana BOS',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '📑',
                    iconBg: 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                    badge: 'Proposal',
                    badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                    url: '{{ url('/proposal') }}',
                    keywords: ['proposal', 'penawaran', 'bos', 'bosp', 'anggaran', 'surat resmi', 'kalkulator']
                },
                {
                    id: 'nav-public',
                    title: 'Halaman Beranda Publik',
                    subtitle: 'Buka landing page promosi dan informasi publik Veyra Educate',
                    category: 'pages',
                    categoryLabel: 'Halaman',
                    iconHtml: '🌐',
                    iconBg: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-300',
                    badge: 'Web',
                    badgeClass: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300',
                    url: '{{ url('/') }}',
                    keywords: ['publik', 'landing', 'depan', 'website', 'beranda luar']
                },

                {{-- 3. Aksi Cepat & Kustomisasi --}}
                {
                    id: 'act-dark-mode',
                    title: 'Ubah Mode Tampilan (Gelap / Terang)',
                    subtitle: 'Beralih antara tema gelap (Dark Mode) dan terang (Light Mode)',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🌓',
                    iconBg: 'bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-100',
                    badge: 'Tampilan',
                    badgeClass: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                    action: 'toggle-dark',
                    keywords: ['dark', 'mode', 'gelap', 'terang', 'light', 'tema', 'warna layar']
                },
                {
                    id: 'act-install-pwa',
                    title: 'Pasang Aplikasi ke Layar HP / PC',
                    subtitle: 'Jalankan fitur Progressive Web App untuk pasang pintasan instan',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '📲',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'PWA App',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    action: 'install-pwa',
                    keywords: ['pasang', 'install', 'pwa', 'android', 'apk', 'pc', 'layar utama', 'home screen']
                },
                {
                    id: 'act-theme-teal',
                    title: 'Ganti Tema: Vitality Mint 🌿',
                    subtitle: 'Nuansa hijau toska segar & modern untuk dashboard sekolah',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🌿',
                    iconBg: 'bg-[#d8f2eb] text-[#0f766e]',
                    badge: 'Tema',
                    badgeClass: 'bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300',
                    action: 'set-theme:teal',
                    keywords: ['tema', 'teal', 'mint', 'hijau', 'vitality']
                },
                {
                    id: 'act-theme-indigo',
                    title: 'Ganti Tema: Ocean Indigo 🌊',
                    subtitle: 'Kombinasi biru laut yang elegan & profesional',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🌊',
                    iconBg: 'bg-[#dbeafe] text-[#1e40af]',
                    badge: 'Tema',
                    badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
                    action: 'set-theme:indigo',
                    keywords: ['tema', 'indigo', 'ocean', 'biru', 'laut']
                },
                {
                    id: 'act-theme-rose',
                    title: 'Ganti Tema: Sunset Rose 🌸',
                    subtitle: 'Gradasi hangat sunset mawar bernuansa premium',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🌸',
                    iconBg: 'bg-[#fdeee9] text-[#be123c]',
                    badge: 'Tema',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    action: 'set-theme:rose',
                    keywords: ['tema', 'rose', 'sunset', 'merah', 'pink']
                },
                {
                    id: 'act-theme-emerald',
                    title: 'Ganti Tema: Emerald Academy 🌲',
                    subtitle: 'Warna hijau zamrud khas institusi pendidikan formal',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🌲',
                    iconBg: 'bg-[#dcfce7] text-[#065f46]',
                    badge: 'Tema',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    action: 'set-theme:emerald',
                    keywords: ['tema', 'emerald', 'academy', 'zamrud', 'hijau']
                },
                {
                    id: 'act-theme-violet',
                    title: 'Ganti Tema: Cyber Violet 🔮',
                    subtitle: 'Warna ungu futuristik berteknologi tinggi',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🔮',
                    iconBg: 'bg-[#ede9fe] text-[#5b21b6]',
                    badge: 'Tema',
                    badgeClass: 'bg-violet-100 text-violet-800 dark:bg-violet-950/60 dark:text-violet-300',
                    action: 'set-theme:violet',
                    keywords: ['tema', 'violet', 'cyber', 'ungu', 'futuristik']
                },
                {
                    id: 'act-theme-slate',
                    title: 'Ganti Tema: Minimal Bento 🖤',
                    subtitle: 'Tampilan monokrom abu-abu minimalis & clean',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '🖤',
                    iconBg: 'bg-[#e2e8f0] text-[#0f172a]',
                    badge: 'Tema',
                    badgeClass: 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
                    action: 'set-theme:slate',
                    keywords: ['tema', 'slate', 'minimal', 'bento', 'hitam', 'abu']
                },
                {
                    id: 'act-whatsapp',
                    title: 'Hubungi Hotline Bantuan WhatsApp 💬',
                    subtitle: 'Chat langsung dengan tim teknis & customer support Veyra Educate',
                    category: 'actions',
                    categoryLabel: 'Aksi & Tema',
                    iconHtml: '💬',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'Support',
                    badgeClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300',
                    action: 'whatsapp',
                    keywords: ['wa', 'whatsapp', 'bantuan', 'support', 'help', 'cs', 'chat', 'hubungi']
                },

                @if ($isAdmin)
                {{-- 4. Admin Panel Section --}}
                {
                    id: 'admin-dashboard',
                    title: 'Dashboard Administrator',
                    subtitle: 'Statistik keseluruhan sekolah, omzet, server, dan analitik',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '🛡️',
                    iconBg: 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.dashboard') }}',
                    keywords: ['admin', 'dashboard admin', 'statistik', 'analitik']
                },
                {
                    id: 'admin-homepage',
                    title: 'Kelola Homepage & Banner',
                    subtitle: 'Atur Hero, Testimoni, CTA, App Showcase, dan tata letak landing page',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '🎨',
                    iconBg: 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.homepage.index') }}',
                    keywords: ['kelola homepage', 'banner', 'hero', 'landing page', 'testimoni', 'showcase']
                },
                {
                    id: 'admin-plans',
                    title: 'Kelola Paket & Harga Langganan',
                    subtitle: 'Buat paket baru, ubah harga bulanan/tahunan, dan fitur inklusif',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '💰',
                    iconBg: 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.plans.index') }}',
                    keywords: ['paket', 'pricing', 'harga', 'kelola paket']
                },
                {
                    id: 'admin-schools',
                    title: 'Kelola Sekolah Mitra',
                    subtitle: 'Daftar sekolah terdaftar, verifikasi NPSN/NSM, kuota siswa & status',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '🏫',
                    iconBg: 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.schools.index') }}',
                    keywords: ['sekolah', 'mitra', 'sekolah terdaftar', 'npsn', 'kelola sekolah']
                },
                {
                    id: 'admin-users',
                    title: 'Kelola Pengguna & Akun',
                    subtitle: 'Manajemen akun operator sekolah, hak akses, dan status verifikasi',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '👥',
                    iconBg: 'bg-teal-100 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.users.index') }}',
                    keywords: ['pengguna', 'users', 'operator', 'akun']
                },
                {
                    id: 'admin-payments',
                    title: 'Riwayat Pembayaran & Invoice',
                    subtitle: 'Daftar transaksi pembayaran sekolah, approval manual & status Midtrans',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '🧾',
                    iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.payments.index') }}',
                    keywords: ['transaksi', 'pembayaran', 'invoice', 'order', 'lunas']
                },
                {
                    id: 'admin-app-requests',
                    title: 'Permintaan Modul Aplikasi Sekolah',
                    subtitle: 'Lihat daftar usulan dan kebutuhan aplikasi kustom dari sekolah',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '📥',
                    iconBg: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.app-requests.index') }}',
                    keywords: ['permintaan aplikasi', 'request', 'modul baru']
                },
                {
                    id: 'admin-announcements',
                    title: 'Pengumuman & Broadcast Sistem',
                    subtitle: 'Kirim notifikasi broadcast ke dashboard seluruh sekolah mitra',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '📢',
                    iconBg: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.announcements.index') }}',
                    keywords: ['pengumuman', 'broadcast', 'notifikasi', 'pesan']
                },
                {
                    id: 'admin-settings',
                    title: 'Pengaturan Sistem Global',
                    subtitle: 'Konfigurasi server, Midtrans API, nama aplikasi, & kontak',
                    category: 'admin',
                    categoryLabel: 'Admin Panel',
                    iconHtml: '🔧',
                    iconBg: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                    badge: 'Admin',
                    badgeClass: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300',
                    url: '{{ route('admin.settings.index') }}',
                    keywords: ['pengaturan sistem', 'konfigurasi', 'midtrans', 'env']
                },
                @endif
            ],

            initPalette() {
                // Initial check
            },

            openPalette() {
                this.isOpen = true;
                this.search = '';
                this.selectedIndex = 0;
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            },

            closePalette() {
                this.isOpen = false;
            },

            togglePalette() {
                if (this.isOpen) {
                    this.closePalette();
                } else {
                    this.openPalette();
                }
            },

            handleGlobalKey(e) {
                // Ctrl + K or Cmd + K
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    this.togglePalette();
                    return;
                }

                // Forward slash '/' when not inside an input
                if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName) && !this.isOpen) {
                    e.preventDefault();
                    this.openPalette();
                }
            },

            get filteredItems() {
                const q = this.search.trim().toLowerCase();
                let list = this.items;

                // Category filter
                if (this.activeFilter !== 'all') {
                    list = list.filter(item => item.category === this.activeFilter);
                }

                // Keyword search
                if (!q) {
                    return list;
                }

                return list.filter(item => {
                    const matchTitle = item.title.toLowerCase().includes(q);
                    const matchSub = item.subtitle.toLowerCase().includes(q);
                    const matchCat = item.categoryLabel.toLowerCase().includes(q);
                    const matchKeys = item.keywords && item.keywords.some(k => k.toLowerCase().includes(q));
                    return matchTitle || matchSub || matchCat || matchKeys;
                });
            },

            selectNext() {
                const count = this.filteredItems.length;
                if (count === 0) return;
                this.selectedIndex = (this.selectedIndex + 1) % count;
                this.scrollToActive();
            },

            selectPrev() {
                const count = this.filteredItems.length;
                if (count === 0) return;
                this.selectedIndex = (this.selectedIndex - 1 + count) % count;
                this.scrollToActive();
            },

            scrollToActive() {
                this.$nextTick(() => {
                    const listEl = this.$refs.resultsList;
                    if (!listEl) return;
                    const activeEl = listEl.children[this.selectedIndex];
                    if (activeEl) {
                        activeEl.scrollIntoView({ block: 'nearest' });
                    }
                });
            },

            executeCurrent() {
                const items = this.filteredItems;
                if (items.length > 0 && items[this.selectedIndex]) {
                    this.executeItem(items[this.selectedIndex]);
                }
            },

            executeItem(item) {
                this.closePalette();

                if (item.action) {
                    this.performAction(item.action);
                    return;
                }

                if (item.url) {
                    window.location.href = item.url;
                }
            },

            performAction(action) {
                if (action === 'toggle-dark') {
                    window.dispatchEvent(new CustomEvent('toggle-app-theme'));
                    // Also dispatch native class toggle if needed
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    return;
                }

                if (action.startsWith('set-theme:')) {
                    const themeKey = action.split(':')[1];
                    localStorage.setItem('school_theme', themeKey);
                    window.dispatchEvent(new CustomEvent('change-school-theme', { detail: themeKey }));
                    return;
                }

                if (action === 'install-pwa') {
                    window.dispatchEvent(new CustomEvent('open-pwa-install-modal'));
                    return;
                }

                if (action === 'whatsapp') {
                    window.open('https://wa.me/6285707074778?text=Halo%20Veyra%20Educate%2C%20saya%20butuh%20bantuan', '_blank');
                    return;
                }
            }
        };
    }
</script>
