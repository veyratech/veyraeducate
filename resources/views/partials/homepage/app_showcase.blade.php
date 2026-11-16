@php
    $badge = $section->field('badge', 'Katalog Aplikasi');
    $title = $section->field('title', 'Ragam Aplikasi Siap Tanam untuk Sekolah Anda');
    $subtitle = $section->field('subtitle', 'Satu wadah terintegrasi untuk berbagai kebutuhan digitalisasi sekolah — dari ujian daring, presensi, rapor, hingga administrasi keuangan.');
    $sourceMode = $section->field('source_mode', 'custom');

    $defaultItems = [
        [
            'name' => 'CBT & Ujian Online',
            'category' => 'Akademik',
            'description' => 'Sistem ujian berbasis komputer anti-curang, acak butir soal & opsi, timer presisi, dan penilaian instan.',
            'status_badge' => 'Siap Pakai',
            'accent' => 'brand',
            'tags' => 'Anti-Curang, Bank Soal, Koreksi Instan',
            'icon' => 'academic-cap',
        ],
        [
            'name' => 'Presensi & Absensi Siswa',
            'category' => 'Kesiswaan',
            'description' => 'Pencatatan kehadiran digital via barcode/QR code & kartu RFID, rekapitulasi kehadiran instan, dan integrasi wali murid.',
            'status_badge' => 'Siap Pakai',
            'accent' => 'emerald',
            'tags' => 'QR Code, Kartu RFID, Rekap Bulanan',
            'icon' => 'clipboard',
        ],
        [
            'name' => 'E-Rapor Kurikulum Merdeka',
            'category' => 'Akademik',
            'description' => 'Pengisian nilai capaian pembelajaran, ledger nilai kelas otomatis, dan cetak rapor format PDF resmi tanpa ribet.',
            'status_badge' => 'Siap Pakai',
            'accent' => 'gold',
            'tags' => 'Kurikulum Merdeka, Cetak PDF, Ledger Nilai',
            'icon' => 'document-text',
        ],
        [
            'name' => 'SPP & Keuangan Sekolah',
            'category' => 'Keuangan',
            'description' => 'Manajemen pos tagihan siswa, kuitansi digital otomatis, pelacakan tunggakan, dan rekap arus kas sekolah.',
            'status_badge' => 'Siap Pakai',
            'accent' => 'amber',
            'tags' => 'Tagihan SPP, Kuitansi Digital, Rekap Kas',
            'icon' => 'wallet',
        ],
        [
            'name' => 'Perpustakaan Digital (E-Perpus)',
            'category' => 'Perpustakaan',
            'description' => 'Manajemen katalog buku, sirkulasi peminjaman barcode, bebas pustaka kelulusan, dan statistik pembaca.',
            'status_badge' => 'Bisa Ditanam',
            'accent' => 'sky',
            'tags' => 'Barcode Buku, Bebas Pustaka, E-Katalog',
            'icon' => 'book-open',
        ],
        [
            'name' => 'SIM Kepegawaian & Jurnal Guru',
            'category' => 'Kepegawaian',
            'description' => 'Pencatatan agenda mengajar harian, jam tatap muka pendidik, dan arsip dokumen kepegawaian sekolah.',
            'status_badge' => 'Segera Hadir',
            'accent' => 'teal',
            'tags' => 'Agenda Mengajar, Jam Pendidik, Arsip',
            'icon' => 'user-group',
        ],
    ];

    if ($sourceMode === 'database') {
        try {
            $dbApps = \App\Models\WebApp::query()->enabled()->orderBy('sort_order')->get();
        } catch (\Throwable) {
            $dbApps = collect();
        }
        if ($dbApps->isNotEmpty()) {
            $items = $dbApps->map(function ($app) {
                return [
                    'name' => $app->name,
                    'category' => ucfirst($app->category ?? 'Umum'),
                    'description' => $app->description ?? 'Aplikasi sekolah terintegrasi dalam wadah Veyra Educate.',
                    'status_badge' => 'Siap Pakai',
                    'accent' => 'brand',
                    'tags' => 'Versi ' . ($app->version ?? '1.0'),
                    'icon' => $app->icon ?? 'app',
                ];
            })->toArray();
        } else {
            $items = $section->field('items', $defaultItems);
        }
    } else {
        $items = $section->field('items', $defaultItems);
    }

    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-white dark:bg-slate-950';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 lg:py-24 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="aplikasi">
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif

    <div class="container-site relative">
        {{-- Section Heading --}}
        <div class="mx-auto max-w-2xl text-center">
            @if ($badge)
                <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/20 bg-white/10 text-brand-300' : 'border-brand-200 dark:border-brand-800 bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300' }} px-3.5 py-1 text-xs sm:text-sm font-semibold mb-4 sm:mb-5">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                    {{ $badge }}
                </div>
            @endif

            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight {{ $isDark ? 'text-white' : 'text-slate-900 dark:text-slate-100' }}">
                {{ $title }}
            </h2>

            @if ($subtitle)
                <p class="mt-3 sm:mt-4 text-sm sm:text-lg leading-relaxed {{ $isDark ? 'text-slate-200' : 'text-slate-600 dark:text-slate-400' }}">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        {{-- Apps Grid --}}
        <div class="mt-8 sm:mt-12 lg:mt-14 grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3 stagger-reveal-scale">
            @forelse ($items as $app)
                @php
                    $appName = $app['name'] ?? 'Modul Sekolah';
                    $category = $app['category'] ?? 'Umum';
                    $desc = $app['description'] ?? '';
                    $status = $app['status_badge'] ?? 'Siap Pakai';
                    $accent = $app['accent'] ?? 'brand';
                    $tags = !empty($app['tags']) ? (is_array($app['tags']) ? $app['tags'] : array_map('trim', explode(',', (string) $app['tags']))) : [];
                    $iconType = $app['icon'] ?? 'app';
                    $imageUrl = $app['image_url'] ?? null;

                    // Color theme mappings
                    $themeConfig = match($accent) {
                        'emerald' => [
                            'iconBg' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-800/60',
                            'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                            'borderHover' => 'hover:border-emerald-300 dark:hover:border-emerald-700',
                        ],
                        'gold', 'amber' => [
                            'iconBg' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border-amber-200/60 dark:border-amber-800/60',
                            'badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                            'borderHover' => 'hover:border-amber-300 dark:hover:border-amber-700',
                        ],
                        'sky' => [
                            'iconBg' => 'bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 border-sky-200/60 dark:border-sky-800/60',
                            'badge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800',
                            'borderHover' => 'hover:border-sky-300 dark:hover:border-sky-700',
                        ],
                        'teal' => [
                            'iconBg' => 'bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border-teal-200/60 dark:border-teal-800/60',
                            'badge' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
                            'borderHover' => 'hover:border-teal-300 dark:hover:border-teal-700',
                        ],
                        default => [
                            'iconBg' => 'bg-brand-50 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 border-brand-200/60 dark:border-brand-800/60',
                            'badge' => 'bg-brand-50 text-brand-700 border-brand-200 dark:bg-brand-950/50 dark:text-brand-300 dark:border-brand-800',
                            'borderHover' => 'hover:border-brand-300 dark:hover:border-brand-700',
                        ],
                    };

                    // Status pill color
                    $statusColor = match($status) {
                        'Siap Pakai' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                        'Bisa Ditanam' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
                        'Segera Hadir' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                        default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                    };
                @endphp

                <article class="group relative flex flex-col justify-between rounded-2xl border {{ $isDark ? 'border-slate-800 bg-slate-900/90 text-white' : 'border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900 text-slate-900 dark:text-slate-100' }} p-5 sm:p-7 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl {{ $themeConfig['borderHover'] }}">
                    <div class="space-y-4">
                        {{-- Top Header Row: Icon & Status Badge --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl border {{ $themeConfig['iconBg'] }} transition-transform duration-300 group-hover:scale-110 shadow-sm shrink-0">
                                @if ($imageUrl)
                                    <img src="{{ \App\Models\HomepageSection::resolveImageUrl($imageUrl) }}" alt="{{ $appName }}" class="h-7 w-7 object-contain">
                                @elseif ($iconType === 'academic-cap')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                @elseif ($iconType === 'clipboard')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                @elseif ($iconType === 'document-text')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @elseif ($iconType === 'wallet')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @elseif ($iconType === 'book-open')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                @elseif ($iconType === 'user-group')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                @endif
                            </div>

                            <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $statusColor }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $status === 'Siap Pakai' ? 'bg-emerald-500' : ($status === 'Bisa Ditanam' ? 'bg-blue-500' : 'bg-slate-400') }}"></span>
                                {{ $status }}
                            </span>
                        </div>

                        {{-- Category & App Title --}}
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-muted">
                                {{ $category }}
                            </span>
                            <h3 class="mt-0.5 text-lg font-bold text-ink dark:text-slate-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                                {{ $appName }}
                            </h3>
                        </div>

                        {{-- Description --}}
                        <p class="text-xs sm:text-sm leading-relaxed {{ $isDark ? 'text-slate-300' : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $desc }}
                        </p>
                    </div>

                    {{-- Feature tags pill footer --}}
                    @if (!empty($tags))
                        <div class="mt-6 pt-4 border-t {{ $isDark ? 'border-slate-800' : 'border-slate-100 dark:border-slate-800' }} flex flex-wrap gap-1.5">
                            @foreach ($tags as $tag)
                                <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-medium text-slate-700 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700/50">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </article>
            @empty
                <div class="col-span-full py-12 text-center text-sm text-muted">
                    Belum ada katalog aplikasi yang ditambahkan.
                </div>
            @endforelse
        </div>

        {{-- Bottom Integration Banner --}}
        <div class="mt-10 sm:mt-14 rounded-2xl border border-brand-200/80 bg-gradient-to-r from-brand-50/80 via-white to-brand-50/40 p-5 sm:p-8 dark:border-brand-900/60 dark:from-brand-950/40 dark:via-slate-900 dark:to-brand-950/20 flex flex-col md:flex-row items-center justify-between gap-5 sm:gap-6 shadow-sm">
            <div class="space-y-1.5 text-center md:text-left">
                <h4 class="text-base sm:text-lg font-bold text-ink dark:text-slate-100 flex items-center justify-center md:justify-start gap-2">
                    <span>🔌</span> Punya aplikasi atau sistem buatan sekolah sendiri?
                </h4>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-2xl leading-relaxed">
                    Wadah Veyra Educate mendukung penanaman aplikasi kustom (*custom web app*) sekolah Anda. Tim kami siap membantu proses integrasi akun dan penempatan di dashboard sekolah.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 w-full md:w-auto shrink-0">
                <a href="{{ route('demo') }}" class="btn-minimal-secondary text-xs sm:text-sm inline-flex items-center justify-center gap-1.5 w-full sm:w-auto py-2.5 sm:py-2">
                    Lihat Demo
                </a>
                <a href="https://wa.me/6285707074778" target="_blank" class="btn-minimal-primary text-xs sm:text-sm inline-flex items-center justify-center gap-1.5 w-full sm:w-auto py-2.5 sm:py-2">
                    <span>Konsultasi Integrasi</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
