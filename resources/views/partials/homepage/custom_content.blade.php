@php
    $badge = $section->field('badge');
    $title = $section->field('title');
    $subtitle = $section->field('subtitle');
    $bodyHtml = $section->field('body_html');
    $imageUrl = $section->imageUrl('image_url');
    $btnText = $section->field('btn_text');
    $btnUrl = $section->field('btn_url');
    $theme = $section->field('theme', 'light');

    $sectionBg = match($theme) {
        'slate' => 'bg-slate-50 dark:bg-slate-900 border-y border-slate-200 dark:border-slate-800',
        'dark' => 'bg-slate-950 text-white border-y border-slate-800',
        default => 'bg-white dark:bg-slate-950',
    };
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : $sectionBg;
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $hasCustomBg ? $section->isDarkBackground() : ($theme === 'dark');
@endphp

<section class="relative py-14 sm:py-20 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif>
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    <div class="container-site relative">
        <div class="grid items-center gap-8 sm:gap-12 {{ $imageUrl ? 'lg:grid-cols-2' : 'max-w-3xl mx-auto text-center' }}">
            <div class="space-y-4 sm:space-y-5">
                @if ($badge)
                    <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/20 bg-white/10 text-brand-300' : 'border-brand-200 dark:border-brand-800 bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300' }} px-3.5 py-1 text-xs sm:text-sm font-semibold">
                        <span class="flex h-1.5 w-1.5 rounded-full {{ $isDark ? 'bg-brand-400' : 'bg-brand-500' }}"></span>
                        {{ $badge }}
                    </div>
                @endif

                @if ($title)
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight {{ $isDark ? 'text-white' : 'text-slate-900 dark:text-slate-100' }}">
                        {{ $title }}
                    </h2>
                @endif

                @if ($subtitle)
                    <p class="text-sm sm:text-lg leading-relaxed {{ $isDark ? 'text-slate-200' : 'text-slate-600 dark:text-slate-400' }}">
                        {{ $subtitle }}
                    </p>
                @endif

                @if ($bodyHtml)
                    <div class="prose {{ $isDark ? 'prose-invert text-slate-200' : 'dark:prose-invert text-slate-600 dark:text-slate-400' }} max-w-none leading-relaxed text-sm sm:text-base">
                        {!! nl2br(e($bodyHtml)) !!}
                    </div>
                @endif

                @if ($btnText && $btnUrl)
                    <div class="pt-2 {{ $imageUrl ? '' : 'flex justify-center' }}">
                        <a href="{{ $btnUrl }}" class="btn-minimal-primary inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                            <span>{{ $btnText }}</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            @if ($imageUrl)
                @php
                    $imagePosition = $section->field('image_position', 'right');
                @endphp
                <div class="relative {{ $imagePosition === 'left' ? 'lg:order-first' : '' }}">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 shadow-xl">
                        <img src="{{ $imageUrl }}" alt="{{ $title ?? 'Container Image' }}" class="h-auto w-full object-cover">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
