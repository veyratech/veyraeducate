@php
    $badge = $section->field('badge', 'Cara kerja');
    $title = $section->field('title', 'Dari daftar hingga panel aktif dalam empat langkah');
    $subtitle = $section->field('subtitle', 'Alur pembayaran resmi Midtrans — QRIS, virtual account, dan e-wallet — lalu aktivasi otomatis.');
    $steps = $section->field('steps', [
        ['title' => 'Pilih paket', 'body' => 'Tentukan durasi langganan — Semesteran, Tahunan, atau Super — sesuai kebutuhan sekolah.'],
        ['title' => 'Bayar resmi', 'body' => 'Selesaikan pembayaran melalui QRIS, virtual account, atau e-wallet pada halaman checkout.'],
        ['title' => 'Otomatis aktif', 'body' => 'Sistem memverifikasi pembayaran dan mengaktifkan panel sekolah beserta masa berlakunya.'],
        ['title' => 'Buka aplikasi', 'body' => 'Masuk ke dashboard, buka aplikasi sekolah Anda — siap dipakai tim di lapangan.'],
    ]);
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-white dark:bg-slate-950';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="cara-kerja">
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

        <ol class="mt-8 sm:mt-12 lg:mt-16 grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4 stagger-reveal-scale">
            @foreach ($steps as $index => $step)
                <li class="relative">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 sm:p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-brand-200 dark:hover:border-brand-800 group h-full">
                        @if (!empty($step['image_url']))
                            <div class="mb-4 overflow-hidden rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 aspect-video">
                                <img src="{{ \App\Models\HomepageSection::resolveImageUrl($step['image_url']) }}" alt="{{ $step['title'] ?? '' }}" class="h-full w-full object-cover transition-transform duration-300 hover:scale-105">
                            </div>
                        @endif
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-sm font-bold text-white shadow-lg shadow-brand-500/25">
                                {{ $index + 1 }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $step['title'] ?? '' }}</h3>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $step['body'] ?? '' }}</p>
                        @if ($index < count($steps) - 1)
                            <div class="absolute right-0 top-1/2 hidden -translate-y-1/2 translate-x-1/2 lg:block">
                                <svg class="h-5 w-5 text-slate-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
