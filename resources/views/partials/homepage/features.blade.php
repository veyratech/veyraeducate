@php
    $badge = $section->field('badge', 'Fitur');
    $title = $section->field('title', 'Satu wadah untuk semua aplikasi sekolah');
    $subtitle = $section->field('subtitle', 'Platform siap pakai: akun, langganan, dan slot aplikasi yang menunggu diisi.');
    $items = $section->field('items', [
        ['title' => 'Dashboard sekolah', 'body' => 'Satu dashboard untuk operator: status langganan, ringkasan, dan akses aplikasi.', 'accent' => 'brand'],
        ['title' => 'Slot aplikasi siap tanam', 'body' => 'Aplikasi sekolah tinggal diunggah ke wadah — tampil di dashboard tanpa daftar ulang.', 'accent' => 'gold'],
        ['title' => 'Multi-sekolah (tenant)', 'body' => 'Setiap sekolah punya data terisolasi — aman untuk banyak mitra.', 'accent' => 'sky'],
        ['title' => 'Akun terpusat', 'body' => 'Satu kredensial untuk masuk panel dan seluruh aplikasi aktif dalam langganan.', 'accent' => 'teal'],
        ['title' => 'Pembayaran resmi', 'body' => 'QRIS, virtual account, dan e-wallet via Midtrans — aktivasi otomatis setelah lunas.', 'accent' => 'amber'],
        ['title' => 'Panel admin lengkap', 'body' => 'Kelola sekolah, pengguna, paket, dan pembayaran dari satu tempat.', 'accent' => 'emerald'],
    ]);
    $hasCustomBg = $section->hasCustomBackground();
    $sectionBgClasses = $hasCustomBg ? $section->getBackgroundClasses() : 'bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900';
    $sectionStyle = $hasCustomBg ? $section->getBackgroundInlineStyle() : '';
    $isDark = $section->isDarkBackground();
@endphp

<section class="relative py-14 sm:py-20 {{ $sectionBgClasses }}" @if($sectionStyle) style="{{ $sectionStyle }}" @endif id="fitur">
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

        <div class="mt-8 sm:mt-12 grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-3 stagger-reveal-scale">
            @foreach ($items as $fIdx => $item)
                <x-feature-card :title="$item['title'] ?? ''" :body="$item['body'] ?? ''" :accent="$item['accent'] ?? 'brand'" :image="$item['image_url'] ?? null">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                    </x-slot>
                </x-feature-card>
            @endforeach
        </div>
    </div>
</section>
