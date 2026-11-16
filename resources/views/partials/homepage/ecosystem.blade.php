@php
    $badge = $section->field('badge', 'Ekosistem');
    $title = $section->field('title', 'Berbayar resmi, aplikasi bertambah, akses terpusat');
    $subtitle = $section->field('subtitle', 'Tiga pilar layanan yang menemani sekolah sepanjang langganan.');
    $items = $section->field('items', [
        ['title' => 'Pembayaran & aktivasi otomatis', 'body' => 'QRIS, virtual account bank, dan e-wallet. Sistem memverifikasi pembayaran dan mengaktifkan lisensi tanpa campur tangan admin.', 'accent' => 'brand'],
        ['title' => 'Aplikasi yang terus bertambah', 'body' => 'Aplikasi sekolah baru diunggah ke wadah dan langsung tersedia di panel pelanggan tanpa daftar ulang.', 'accent' => 'gold'],
        ['title' => 'Akses terpusat (SSO)', 'body' => 'Satu kredensial operator untuk panel sekolah dan seluruh aplikasi yang aktif dalam langganan.', 'accent' => 'sky'],
    ]);
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="ekosistem">
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    <div class="container-site relative">
        <div class="mx-auto max-w-2xl text-center">
            @if ($badge)
                <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/20 bg-white/10 text-brand-300' : 'border-brand-200 dark:border-brand-800 bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300' }} px-3.5 py-1 text-xs sm:text-sm font-semibold mb-5 sm:mb-6">
                    <span class="flex h-1.5 w-1.5 rounded-full {{ $isDark ? 'bg-brand-400' : 'bg-brand-500' }}"></span>
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

        <div class="mt-8 sm:mt-12 grid gap-4 sm:gap-5 lg:grid-cols-3 stagger-reveal-scale">
            @foreach ($items as $item)
                <x-feature-card :title="$item['title'] ?? ''" :body="$item['body'] ?? ''" :accent="$item['accent'] ?? 'brand'" :image="$item['image_url'] ?? null">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                        </svg>
                    </x-slot>
                </x-feature-card>
            @endforeach
        </div>
    </div>
</section>
