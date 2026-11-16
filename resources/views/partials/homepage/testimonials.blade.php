@php
    $badge = $section->field('badge', 'Testimoni');
    $title = $section->field('title', 'Dipercaya oleh puluhan sekolah & yayasan di Indonesia');
    $subtitle = $section->field('subtitle', 'Pengalaman nyata kepala sekolah dan tim operator yang merasakan kemudahan sistem terpusat Veyra Educate.');
    $items = $section->field('items', $section->field('testimonials', [
        [
            'name' => 'Drs. H. Mulyadi, M.Pd.',
            'role' => 'Kepala Sekolah',
            'school' => 'SMA Bina Bangsa',
            'quote' => 'Dulu kami pusing mengelola server ujian sendiri setiap kali semesteran. Sejak memakai Veyra Educate, semuanya berjalan di cloud tanpa takut down, dan pembayarannya sangat transparan.',
            'rating' => 5,
            'avatar_url' => null,
        ],
        [
            'name' => 'Siti Rahmawati, S.Kom.',
            'role' => 'Koordinator IT & Kurikulum',
            'school' => 'SMK Prestasi Nusantara',
            'quote' => 'Integrasi akun terpusatnya sangat menghemat waktu. Guru tidak perlu menghafal banyak password untuk tiap modul, dan laporan langsung tersinkron ke dashboard pimpinan.',
            'rating' => 5,
            'avatar_url' => null,
        ],
        [
            'name' => 'Ahmad Fauzi, S.T.',
            'role' => 'Operator Sekolah & Dapodik',
            'school' => 'SMP Islam Al-Hikmah',
            'quote' => 'Aktivasi otomatis Midtrans membuat kami bisa langsung mengoperasikan sistem dalam hitungan menit setelah transfer. Layanan pendampingannya juga sangat ramah dan sigap.',
            'rating' => 5,
            'avatar_url' => null,
        ],
    ]));

    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-slate-50/60 dark:bg-slate-900/60 border-y border-slate-200/80 dark:border-slate-800/80';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 lg:py-24 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="testimoni">
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif

    <div class="container-site relative">
        {{-- Header Section --}}
        <div class="mx-auto max-w-2xl text-center">
            @if ($badge)
                <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/20 bg-white/10 text-gold-300' : 'border-gold-200 dark:border-gold-800 bg-gold-50 dark:bg-gold-950/40 text-gold-700 dark:text-gold-300' }} px-3.5 py-1 text-xs sm:text-sm font-semibold mb-4 sm:mb-5">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse"></span>
                    {{ $badge }}
                </div>
            @endif

            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight {{ $isDark ? 'text-white' : 'text-slate-900 dark:text-slate-100' }} reveal-up">
                {{ $title }}
            </h2>

            @if ($subtitle)
                <p class="mt-3 sm:mt-4 text-sm sm:text-lg leading-relaxed {{ $isDark ? 'text-slate-200' : 'text-slate-600 dark:text-slate-400' }} reveal-up" style="transition-delay: 80ms;">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        {{-- Testimonials Grid --}}
        <div class="mt-8 sm:mt-12 lg:mt-14 grid gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3 stagger-reveal-scale">
            @forelse ($items as $idx => $item)
                @php
                    $rating = (int) ($item['rating'] ?? 5);
                    $rating = max(1, min(5, $rating));
                    $name = $item['name'] ?? 'Pimpinan Sekolah';
                    $role = $item['role'] ?? 'Pengguna';
                    $school = $item['school'] ?? '';
                    $quote = $item['quote'] ?? '';
                    $avatar = $item['avatar_url'] ?? null;

                    // Extract initials for avatar fallback
                    $nameParts = preg_split('/\s+/', trim(preg_replace('/^(drs\.|dr\.|h\.|hj\.|prof\.)\s*/i', '', $name)));
                    $initials = '';
                    if (!empty($nameParts[0])) $initials .= mb_substr($nameParts[0], 0, 1);
                    if (!empty($nameParts[1])) $initials .= mb_substr($nameParts[1], 0, 1);
                    $initials = strtoupper($initials ?: 'S');

                    // Vibrant gradient colors for fallback initials avatar
                    $avatarGradients = [
                        'from-brand-600 to-indigo-700',
                        'from-amber-500 to-rose-600',
                        'from-emerald-500 to-teal-700',
                        'from-sky-500 to-blue-700',
                        'from-purple-500 to-pink-600',
                    ];
                    $avatarGradient = $avatarGradients[$idx % count($avatarGradients)];
                @endphp

                <article class="group relative flex flex-col justify-between rounded-2xl border {{ $isDark ? 'border-slate-800 bg-slate-900/90 text-white' : 'border-slate-200/90 bg-white dark:border-slate-800 dark:bg-slate-900 text-slate-900 dark:text-slate-100' }} p-5 sm:p-7 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-gold-300/80 dark:hover:border-gold-600/60">
                    <div>
                        {{-- Header: Stars on left, small subtle quote mark on right (never overlaps) --}}
                        <div class="flex items-center justify-between gap-3 mb-5">
                            {{-- Stars rating --}}
                            <div class="flex items-center gap-1" aria-label="Rating {{ $rating }} dari 5 bintang">
                                @for ($s = 1; $s <= 5; $s++)
                                    @if ($s <= $rating)
                                        <svg class="h-4 w-4 text-amber-400 fill-current shrink-0" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-slate-300 dark:text-slate-700 fill-current shrink-0" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                                <span class="ml-1.5 text-xs font-bold text-amber-500 font-mono">{{ number_format($rating, 1) }}</span>
                            </div>

                            {{-- Subtle decorative quote icon --}}
                            <div class="text-slate-300 dark:text-slate-700 group-hover:text-gold-400 dark:group-hover:text-gold-500 transition-colors shrink-0" aria-hidden="true">
                                <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Testimonial Quote text --}}
                        <blockquote class="text-sm sm:text-[15px] leading-relaxed {{ $isDark ? 'text-slate-200' : 'text-slate-700 dark:text-slate-300' }}">
                            &ldquo;{{ $quote }}&rdquo;
                        </blockquote>
                    </div>

                    {{-- Author Footer --}}
                    <div class="mt-7 pt-5 border-t {{ $isDark ? 'border-slate-800' : 'border-slate-100 dark:border-slate-800' }} flex items-center gap-3.5">
                        {{-- Avatar image or initials badge --}}
                        @if ($avatar)
                            <img
                                src="{{ $avatar }}"
                                alt="{{ $name }}"
                                class="h-11 w-11 rounded-full object-cover ring-2 ring-gold-400/30 shrink-0"
                                loading="lazy"
                            >
                        @else
                            <div class="h-11 w-11 rounded-full bg-gradient-to-br {{ $avatarGradient }} flex items-center justify-center text-white font-bold text-sm tracking-wide shadow-sm shrink-0">
                                {{ $initials }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-ink dark:text-slate-100 truncate">
                                {{ $name }}
                            </p>
                            <p class="text-xs text-muted truncate">
                                {{ $role }}
                            </p>
                            @if ($school)
                                <div class="mt-0.5 inline-flex items-center gap-1 text-[11px] font-semibold text-brand-600 dark:text-brand-400 truncate">
                                    <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="truncate">{{ $school }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-10 text-center text-sm text-muted">
                    Belum ada data ulasan sekolah.
                </div>
            @endforelse
        </div>

        {{-- Bottom Trust Bar --}}
        <div class="mt-12 text-center">
            <div class="inline-flex flex-wrap items-center justify-center gap-2 rounded-full border {{ $isDark ? 'border-slate-800 bg-slate-900/60 text-slate-300' : 'border-slate-200/80 bg-white/80 dark:border-slate-800 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300' }} px-5 py-2 text-xs backdrop-blur-sm shadow-sm">
                <span class="text-amber-500 font-bold">★★★★★</span>
                <span class="font-medium">Tingkat kepuasan <strong>99%</strong> dari institusi pendidikan pengguna cloud Veyra Educate</span>
            </div>
        </div>
    </div>
</section>
