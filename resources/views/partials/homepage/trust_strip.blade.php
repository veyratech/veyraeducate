@php
    $items = $section->field('items', [
        ['title' => 'Tanpa server lokal', 'desc' => 'Infrastruktur cloud yang kami kelola sepenuhnya.', 'color' => 'brand'],
        ['title' => 'Akun aman', 'desc' => 'Login terpusat untuk seluruh tim sekolah.', 'color' => 'gold'],
        ['title' => 'Langganan jelas', 'desc' => 'Harga transparan, aktivasi otomatis.', 'color' => 'emerald'],
    ]);
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'border-y border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif>
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    <div class="container-site py-8 sm:py-10 relative">
        <div class="grid gap-6 sm:gap-8 sm:grid-cols-3 stagger-reveal-scale">
            @foreach ($items as $idx => $item)
                @php
                    $color = $item['color'] ?? ($idx === 0 ? 'brand' : ($idx === 1 ? 'gold' : 'emerald'));
                    $grad = match($color) {
                        'gold' => 'from-gold-500 to-gold-600 shadow-gold-500/25',
                        'emerald' => 'from-emerald-500 to-emerald-600 shadow-emerald-500/25',
                        default => 'from-brand-500 to-brand-600 shadow-brand-500/25',
                    };
                @endphp
                <div class="flex items-start gap-3.5 sm:gap-4">
                    @if (!empty($item['image_url']))
                        <img src="{{ \App\Models\HomepageSection::resolveImageUrl($item['image_url']) }}" alt="{{ $item['title'] ?? '' }}" class="h-11 w-11 sm:h-12 sm:w-12 shrink-0 rounded-xl sm:rounded-2xl object-cover shadow-md">
                    @else
                        <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl bg-gradient-to-br {{ $grad }} text-white shadow-lg">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                @if($idx === 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                @elseif($idx === 1)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                @endif
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-sm sm:text-base font-bold {{ $isDark ? 'text-white' : 'text-slate-900 dark:text-slate-100' }}">{{ $item['title'] ?? '' }}</h3>
                        <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm {{ $isDark ? 'text-slate-200/90' : 'text-slate-600 dark:text-slate-400' }}">{{ $item['desc'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
