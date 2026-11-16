@php
    $badge = $section->field('badge', 'Siap mulai');
    $title = $section->field('title', 'Siap menyiapkan wadah aplikasi sekolah Anda?');
    $subtitle = $section->field('subtitle', 'Pendaftaran memakan waktu kurang dari lima menit. Tim kami siap mendampingi onboarding melalui WhatsApp.');
    $btnText = $section->field('btn_text', 'Daftar sekolah');
    $btnUrl = $section->field('btn_url', '/register');
    $waText = $section->field('wa_text', 'Konsultasi WhatsApp');
    $waUrl = $section->field('wa_url', 'https://wa.me/6285707074778');
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-gradient-to-br from-brand-950 via-brand-900 to-slate-900 text-white';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
    $imageUrl = $section->imageUrl('image_url');
    $imagePosition = $section->field('image_position', 'right');
@endphp

<section class="relative overflow-hidden {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="daftar">
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    @if (! $hasCustomBg || $isDark)
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-500/15 rounded-full blur-[120px] animate-float-slow"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-gold-500/10 rounded-full blur-[100px] animate-float-slower"></div>
            <div class="absolute inset-0 opacity-[0.04]" style="background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.5) 1px,transparent 0);background-size:32px 32px;"></div>
        </div>
    @endif

    <div class="container-site py-14 sm:py-20 relative">
        @if ($imageUrl)
            <div class="grid items-center gap-10 lg:gap-12 lg:grid-cols-2">
                <div class="space-y-5 sm:space-y-6 text-center lg:text-left {{ $imagePosition === 'left' ? 'lg:order-last' : '' }} reveal-split-left">
                    @if ($badge)
                        <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/10 bg-white/5 text-brand-300' : 'border-brand-200 bg-brand-50 text-brand-700' }} px-3.5 py-1 text-xs sm:text-sm font-semibold backdrop-blur-sm">
                            <span class="flex h-1.5 w-1.5 rounded-full {{ $isDark ? 'bg-brand-400' : 'bg-brand-500' }}"></span>
                            {{ $badge }}
                        </div>
                    @endif
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold tracking-tight {{ $isDark ? 'text-white' : 'text-slate-900' }} leading-tight">
                        {{ $title }}
                    </h2>
                    @if ($subtitle)
                        <p class="text-sm sm:text-base lg:text-lg {{ $isDark ? 'text-brand-100/90' : 'text-slate-600' }}">
                            {{ $subtitle }}
                        </p>
                    @endif
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center lg:justify-start gap-3 w-full sm:w-auto pt-2">
                        <a href="{{ $btnUrl }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-base font-bold text-slate-900 shadow-xl transition-all hover:-translate-y-0.5 hover:shadow-2xl hover:bg-slate-100 w-full sm:w-auto">
                            <span>{{ $btnText }}</span>
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        @if ($waUrl)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-600 px-6 py-3.5 text-base font-semibold text-white backdrop-blur-sm transition-all hover:-translate-y-0.5 hover:bg-emerald-500 hover:border-emerald-400/40 w-full sm:w-auto">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44Z"/>
                                </svg>
                                {{ $waText }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="relative {{ $imagePosition === 'left' ? 'lg:order-first' : '' }} reveal-split-right">
                    <div class="overflow-hidden rounded-3xl border border-white/20 bg-white/5 shadow-2xl backdrop-blur-sm">
                        <img src="{{ $imageUrl }}" alt="{{ $title }}" class="h-auto w-full object-cover">
                    </div>
                </div>
            </div>
        @else
            <div class="mx-auto max-w-3xl text-center">
                @if ($badge)
                    <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/10 bg-white/5 text-brand-300' : 'border-brand-200 bg-brand-50 text-brand-700' }} px-3.5 py-1 text-xs sm:text-sm font-semibold backdrop-blur-sm mb-6 sm:mb-8">
                        <span class="flex h-1.5 w-1.5 rounded-full {{ $isDark ? 'bg-brand-400' : 'bg-brand-500' }}"></span>
                        {{ $badge }}
                    </div>
                @endif
                <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold tracking-tight {{ $isDark ? 'text-white' : 'text-slate-900' }}">
                    {{ $title }}
                </h2>
                @if ($subtitle)
                    <p class="mt-3 sm:mt-4 text-sm sm:text-base lg:text-lg {{ $isDark ? 'text-brand-100/90' : 'text-slate-600' }}">
                        {{ $subtitle }}
                    </p>
                @endif
                <div class="mt-7 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 w-full sm:w-auto">
                    <a href="{{ $btnUrl }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-base font-bold text-slate-900 shadow-xl transition-all hover:-translate-y-0.5 hover:shadow-2xl hover:bg-slate-100 w-full sm:w-auto">
                        <span>{{ $btnText }}</span>
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    @if ($waUrl)
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-500/30 bg-emerald-600 px-6 py-3.5 text-base font-semibold text-white backdrop-blur-sm transition-all hover:-translate-y-0.5 hover:bg-emerald-500 hover:border-emerald-400/40 w-full sm:w-auto">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44Z"/>
                            </svg>
                            {{ $waText }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
