@php
    $badge = $section->field('badge', 'Harga');
    $title = $section->field('title', 'Paket langganan transparan');
    $subtitle = $section->field('subtitle', 'Harga yang tercantum di sini sama dengan yang ditagihkan saat checkout. Tanpa biaya tersembunyi.');
    try {
        $previewPlans = \App\Models\Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    } catch (\Throwable) {
        $previewPlans = collect();
    }
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-white dark:bg-slate-950';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="harga">
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    <div class="container-site relative">
        <div class="mx-auto max-w-2xl text-center">
            @if ($badge)
                <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/20 bg-white/10 text-gold-300' : 'border-gold-200 dark:border-gold-800 bg-gold-50 dark:bg-gold-950/30 text-gold-700 dark:text-gold-300' }} px-3.5 py-1 text-xs sm:text-sm font-semibold mb-5 sm:mb-6">
                    <span class="flex h-1.5 w-1.5 rounded-full bg-gold-500"></span>
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

        <div class="mt-8 sm:mt-12 grid gap-5 lg:grid-cols-3 stagger-reveal-scale">
            @forelse ($previewPlans as $plan)
                <article class="relative flex flex-col rounded-2xl border {{ $plan->is_featured ? 'border-brand-300 dark:border-brand-800 bg-gradient-to-b from-brand-50/80 dark:from-brand-950/40 to-white dark:to-slate-900 shadow-xl shadow-brand-100 dark:shadow-none' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm' }} p-5 sm:p-7 transition-all duration-200 hover:-translate-y-1 {{ $plan->is_featured ? 'hover:shadow-xl hover:shadow-brand-200 dark:hover:shadow-none' : 'hover:shadow-md hover:border-brand-200 dark:hover:border-brand-800' }}">
                    @if ($plan->is_featured)
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-1 text-xs font-bold text-white shadow-lg shadow-brand-500/25">
                            Paling dipilih sekolah
                        </span>
                    @endif
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $plan->is_featured ? 'bg-brand-100 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100">{{ $plan->label }}</h3>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">{{ $plan->description }}</p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-5">
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100">{{ $plan->formatPrice() }}</p>
                        <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-500">{{ $plan->badge }}</p>
                    </div>
                    <ul class="mt-6 flex-1 space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                        @foreach ($plan->features ?? [] as $feature)
                            <li class="flex items-start gap-2">
                                <span class="{{ $plan->is_featured ? 'text-brand-500 dark:text-brand-400' : 'text-slate-400 dark:text-slate-500' }} mt-0.5 shrink-0">✓</span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ auth()->check() ? route('checkout.show', $plan->code) : route('register', ['paket' => $plan->code]) }}" class="mt-7 inline-flex items-center justify-center rounded-xl border {{ $plan->is_featured ? 'border-brand-600 dark:border-brand-700 bg-brand-600 dark:bg-brand-700 text-white hover:bg-brand-700 dark:hover:bg-brand-800 shadow-lg shadow-brand-500/25' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-brand-200 dark:hover:border-brand-800 hover:bg-brand-50 dark:hover:bg-slate-700' }} px-4 py-2.5 text-sm font-semibold transition-all">
                        Pilih {{ $plan->label }}
                    </a>
                </article>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-500 lg:col-span-3">Paket belum tersedia.</p>
            @endforelse
        </div>
    </div>
</section>
