@php
    $badge = $section->field('badge', 'FAQ');
    $title = $section->field('title', 'Pertanyaan yang sering diajukan');
    $subtitle = $section->field('subtitle', 'Ringkasan jawaban — versi lengkap ada di halaman FAQ.');
    $items = $section->field('items', [
        ['q' => 'Bagaimana cara kerja sistem setelah pembayaran?', 'a' => 'Setelah pembayaran terverifikasi otomatis, status akun berubah menjadi Aktif dan masa berlaku dihitung seketika.'],
        ['q' => 'Apakah sekolah perlu menyiapkan server sendiri?', 'a' => 'Tidak. Seluruh infrastruktur, hosting, dan pemeliharaan server ditangani oleh tim Veyra Educate.'],
        ['q' => 'Bagaimana jika pembayaran pending atau gagal?', 'a' => 'Akun tetap dapat masuk. Panel menampilkan tombol bayar ulang dengan nomor order baru.'],
        ['q' => 'Bisakah berganti paket setelah mendaftar?', 'a' => 'Bisa, selama lisensi belum aktif. Total tagihan menyesuaikan otomatis sebelum pembayaran.'],
    ]);
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif>
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    <div class="container-site relative">
        <div class="mx-auto max-w-2xl text-center">
            @if ($badge)
                <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/20 bg-white/10 text-brand-300' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300' }} px-3.5 py-1 text-xs sm:text-sm font-semibold mb-5 sm:mb-6">
                    <span class="flex h-1.5 w-1.5 rounded-full {{ $isDark ? 'bg-brand-400' : 'bg-slate-500' }}"></span>
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

        <div class="mt-8 sm:mt-12 grid gap-3.5 sm:gap-4 lg:grid-cols-2">
            @foreach ($items as $item)
                <details class="group rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 sm:p-5 shadow-sm transition-all hover:shadow-md hover:border-brand-200 dark:hover:border-brand-800 open:ring-2 open:ring-brand-100 dark:open:ring-brand-900">
                    <summary class="cursor-pointer list-none text-sm sm:text-base font-semibold text-slate-900 dark:text-slate-100 marker:hidden flex items-center justify-between gap-3">
                        <span>{{ $item['q'] ?? '' }}</span>
                        <svg class="h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <p class="mt-2.5 sm:mt-3 text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $item['a'] ?? '' }}</p>
                </details>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="/faq" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition-all hover:-translate-y-0.5 hover:border-brand-200 dark:hover:border-brand-800 hover:bg-brand-50/50 dark:hover:bg-slate-800">
                Buka FAQ lengkap
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</section>
