@php
    $badge = $section->field('badge');
    $title = $section->field('title') ?: $section->title;
    $subtitle = $section->field('subtitle');
    $theme = $section->field('theme', 'brand');
    $btnText = $section->field('btn_text');
    $btnUrl = $section->field('btn_url');

    $themeClasses = match($theme) {
        'dark' => 'bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white border-slate-800 shadow-slate-950/20',
        'emerald' => 'bg-gradient-to-r from-emerald-900 via-teal-900 to-emerald-950 text-white border-emerald-800 shadow-emerald-950/20',
        'gold' => 'bg-gradient-to-r from-amber-900 via-yellow-950 to-amber-950 text-white border-amber-800 shadow-amber-950/20',
        default => 'bg-gradient-to-r from-brand-900 via-brand-800 to-sky-950 text-white border-brand-800 shadow-brand-950/20',
    };
    $hasCustomBg = $section->hasCustomBackground();
    $cardBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : $themeClasses;
    $cardBgStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $hasCustomBg ? $section->isDarkBackground() : true;
@endphp

<section class="py-8 sm:py-12 lg:py-16">
    <div class="container-site">
        <div class="relative overflow-hidden rounded-3xl border border-white/10 {{ $cardBgClasses }} p-6 sm:p-10 lg:p-12 shadow-2xl" @if($cardBgStyle) style="{{ $cardBgStyle }}" @endif>
            @if ($hasCustomBg && $section->getBgType() === 'custom_image')
                <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
            @endif
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-white/5 blur-3xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    @if ($badge)
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-0.5 sm:px-3.5 sm:py-1 text-[11px] sm:text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                            <span class="flex h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                            {{ $badge }}
                        </div>
                    @endif
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold leading-tight">
                        {{ $title }}
                    </h2>
                    @if ($subtitle)
                        <p class="text-sm sm:text-base text-white/80 leading-relaxed max-w-xl">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>

                @if ($imageUrl = $section->imageUrl('image_url'))
                    <div class="shrink-0 hidden md:block">
                        <img src="{{ $imageUrl }}" alt="{{ $title }}" class="h-28 w-auto max-w-[220px] rounded-2xl object-contain drop-shadow-2xl">
                    </div>
                @endif

                @if ($btnText && $btnUrl)
                    <div class="shrink-0 w-full sm:w-auto">
                        <a href="{{ $btnUrl }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-slate-900 shadow-xl transition-all duration-200 hover:-translate-y-0.5 hover:bg-slate-100 hover:shadow-2xl w-full sm:w-auto">
                            <span>{{ $btnText }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
