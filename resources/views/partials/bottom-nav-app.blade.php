{{-- Bottom navigation mobile — dashboard user/sekolah --}}
<nav class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-200/70 bg-white/95 backdrop-blur-xl dark:border-slate-700/60 dark:bg-slate-950/95 safe-area-bottom md:hidden" aria-label="Navigasi bawah">
    <div class="grid grid-cols-5">
        @php
            $bottomNavApp = [
                [
                    'label' => 'Dashboard',
                    'url' => route('dashboard'),
                    'active' => request()->routeIs('dashboard'),
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
                [
                    'label' => 'Aplikasi',
                    'url' => route('apps.index'),
                    'active' => request()->routeIs('apps.*', 'school-app.*'),
                    'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                ],
                [
                    'label' => 'Panduan',
                    'url' => route('guides.index'),
                    'active' => request()->routeIs('guides.*'),
                    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                ],
                [
                    'label' => 'Langganan',
                    'url' => route('checkout.show', 'super'),
                    'active' => request()->routeIs('checkout.*', 'pembayaran.*'),
                    'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                ],
                [
                    'label' => 'Pengaturan',
                    'url' => route('school.settings'),
                    'active' => request()->routeIs('school.settings*', 'profile.*'),
                    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
                ],
            ];
        @endphp
        @foreach ($bottomNavApp as $item)
            <a
                href="{{ $item['url'] }}"
                @click="if (window.vibrateHaptic) window.vibrateHaptic(12)"
                class="flex flex-col items-center justify-center gap-1 py-2 text-[10px] sm:text-[11px] transition-colors {{ $item['active'] ? 'font-bold text-brand-700 dark:text-brand-400' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}"
                @if ($item['active']) aria-current="page" @endif
            >
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="truncate px-0.5 max-w-full text-center leading-tight">{{ $item['label'] }}</span>
                <span class="h-1 w-5 sm:w-6 rounded-full {{ $item['active'] ? 'bg-brand-600 dark:bg-brand-400' : 'bg-transparent' }}" aria-hidden="true"></span>
            </a>
        @endforeach
    </div>
</nav>
