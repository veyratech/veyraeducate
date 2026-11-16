@php
    $showBanner = (bool) $section->field('show_banner', true);
    $lead = $section->field('lead') ?: \App\Support\HeroTitle::get()['lead'];
    $highlight = $section->field('highlight') ?: \App\Support\HeroTitle::get()['highlight'];
    $tail = $section->field('tail') ?: \App\Support\HeroTitle::get()['tail'];
    $description = $section->field('description', 'Veyra Educate menampung aplikasi sekolah Anda dalam satu platform: akun terpusat, langganan transparan, dan dashboard yang siap diisi aplikasi.');
    $primaryBtnText = $section->field('primary_btn_text', 'Daftar sekolah');
    $primaryBtnUrl = $section->field('primary_btn_url', '/register');
    $secondaryBtnText = $section->field('secondary_btn_text', 'Lihat demo');
    $secondaryBtnUrl = $section->field('secondary_btn_url', '/demo');
    $stats = $section->field('stats', [
        ['number' => '3', 'label' => 'Paket'],
        ['number' => 'Cloud', 'label' => 'Server'],
        ['number' => 'Otomatis', 'label' => 'Aktivasi'],
    ]);
    $configuredHero = $section->imageUrl('image_url');
    $heroUrl = $configuredHero ?: \App\Support\HeroImage::url();
    $displaySize = \App\Support\HeroImage::displaySize();
    $hasCustomBg = $section->hasCustomBackground();
    $bgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-gradient-to-br from-slate-950 via-brand-950 to-slate-900';
    if ($hasCustomBg) {
        $bgStyle = $section->getBackgroundInlineStyle();
    } else {
        $bgStyle = '';
    }
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative overflow-hidden {{ $bgClasses }}" @if($bgStyle) style="{{ $bgStyle }}" @endif id="beranda">
    @if ($hasCustomBg && $section->getBgType() === 'custom_image')
        <div class="pointer-events-none absolute inset-0 {{ $section->getOverlayClasses() }}" aria-hidden="true"></div>
    @endif
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute top-0 left-1/4 w-[800px] h-[600px] bg-brand-500/20 rounded-full blur-[120px] animate-float-slow"></div>
        <div class="absolute bottom-0 right-1/4 w-[600px] h-[500px] bg-gold-500/10 rounded-full blur-[100px] animate-float-slower"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[600px] bg-brand-600/10 rounded-full blur-[140px] animate-drift"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.5) 1px,transparent 0);background-size:32px 32px;"></div>
    </div>

    @if ($showBanner)
    <div class="container-site relative pt-8 sm:pt-12 lg:pt-14 pb-0 sm:pb-0 lg:pb-0">
        <div class="grid items-end gap-8 lg:gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7 pb-10 sm:pb-14 lg:pb-16 max-w-2xl reveal-split-left">
                <div class="inline-flex items-center gap-2 rounded-full border {{ $isDark ? 'border-white/10 bg-white/5 text-brand-300' : 'border-brand-200 bg-brand-50 text-brand-700' }} px-3.5 py-1 text-xs sm:text-sm font-semibold mb-6 sm:mb-8">{{ $lead }} </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight {{ $isDark ? 'text-white' : 'text-slate-900' }}">
                    <span class="bg-gradient-to-r from-gold-400 via-amber-300 to-gold-400 bg-clip-text text-transparent bg-[length:200%_auto] animate-shimmer">{{ $highlight }} </span><span class="bg-gradient-to-r from-brand-400 via-sky-400 to-brand-300 bg-clip-text text-transparent bg-[length:200%_auto] animate-shimmer animate-float">{{ $tail }}</span>
                </h1>

                <p class="mt-4 sm:mt-6 text-base sm:text-xl leading-relaxed {{ $isDark ? 'text-white/90' : 'text-slate-700' }}">
                    {{ $description }}
                </p>

                <div class="mt-7 sm:mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ $primaryBtnUrl }}" class="group inline-flex items-center justify-center gap-2 rounded-xl {{ $isDark ? 'bg-white text-slate-900 hover:bg-slate-100 shadow-xl shadow-brand-500/10' : 'bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-600/25' }} px-6 py-3.5 text-base font-bold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-2xl w-full sm:w-auto">
                        <span>{{ $primaryBtnText }}</span>
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="{{ $secondaryBtnUrl }}" class="inline-flex items-center justify-center gap-2 rounded-xl border {{ $isDark ? 'border-white/20 bg-white/10 text-white backdrop-blur-md hover:bg-white/20 hover:border-white/30' : 'border-slate-200 bg-white text-slate-700 hover:border-brand-300 hover:bg-slate-50 shadow-sm' }} px-6 py-3.5 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 w-full sm:w-auto">
                        <svg class="h-5 w-5 {{ $isDark ? 'text-gold-400' : 'text-brand-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
                        </svg>
                        {{ $secondaryBtnText }}
                    </a>
                </div>

                @if(!empty($stats))
                    <div class="mt-8 sm:mt-10 grid grid-cols-3 gap-2 border-t {{ $isDark ? 'border-white/10' : 'border-slate-200' }} pt-5 sm:flex sm:items-center sm:gap-6 sm:pt-6">
                        @foreach ($stats as $sIdx => $st)
                            <div class="text-center sm:text-left">
                                <p class="text-xl sm:text-2xl font-extrabold {{ $isDark ? 'text-white' : 'text-slate-900' }}">{{ $st['number'] ?? '' }}</p>
                                <p class="text-xs sm:text-sm {{ $isDark ? 'text-slate-400' : 'text-slate-600' }}">{{ $st['label'] ?? '' }}</p>
                            </div>
                            @if(!$loop->last)
                                <div class="hidden sm:block h-8 w-px {{ $isDark ? 'bg-white/15' : 'bg-slate-200' }}"></div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-5 flex justify-center lg:justify-end items-end self-end reveal-split-right">
                <div class="relative w-full max-w-sm sm:max-w-md lg:max-w-none flex justify-center lg:justify-end items-end">
                    @if ($heroUrl)
                        <img src="{{ $heroUrl }}" alt="Hero homepage" class="block w-auto max-h-[380px] sm:max-h-[480px] lg:max-h-[540px] xl:max-h-[580px] object-contain object-bottom drop-shadow-[0_25px_35px_rgba(0,0,0,0.5)]" style="aspect-ratio: {{ $displaySize['width'] }} / {{ $displaySize['height'] }};">
                    @else
                        <div class="mb-10 flex h-64 w-full max-w-sm flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-white/20 bg-white/5 p-6 text-center">
                            <p class="text-sm font-bold text-white">Panel sekolah</p>
                            <p class="max-w-[200px] text-xs text-slate-400">Unggah foto hero di Pengaturan untuk menampilkan ilustrasi khusus.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</section>
