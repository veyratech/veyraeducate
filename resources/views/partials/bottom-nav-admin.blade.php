{{-- Bottom navigation mobile — dashboard admin --}}
<nav class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-200/70 bg-white/95 backdrop-blur-xl dark:border-slate-700/60 dark:bg-slate-950/95 safe-area-bottom lg:hidden" aria-label="Navigasi bawah">
    <div class="grid grid-cols-4">
        @php
            $bottomNavAdmin = [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM10 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM10 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z',
                ],
                [
                    'label' => 'Sekolah',
                    'route' => 'admin.schools.index',
                    'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-16 0H3m2-9h.01M12 12h.01M17 12h.01M8 12H7.99M12 16h.01',
                ],
                [
                    'label' => 'Pembayaran',
                    'route' => 'admin.payments.index',
                    'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                ],
                [
                    'label' => 'Pengaturan',
                    'route' => 'admin.settings.index',
                    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
                ],
            ];
        @endphp
        @foreach ($bottomNavAdmin as $item)
            @php($isActive = request()->routeIs($item['route']))
            <a
                href="{{ route($item['route']) }}"
                @click="if (window.vibrateHaptic) window.vibrateHaptic(12)"
                class="flex flex-col items-center justify-center gap-1 py-2 text-[10px] sm:text-[11px] transition-colors {{ $isActive ? 'font-bold text-brand-700 dark:text-brand-400' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}"
                @if ($isActive) aria-current="page" @endif
            >
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="truncate px-0.5 max-w-full text-center leading-tight">{{ $item['label'] }}</span>
                <span class="h-1 w-5 sm:w-6 rounded-full {{ $isActive ? 'bg-brand-600 dark:bg-brand-400' : 'bg-transparent' }}" aria-hidden="true"></span>
            </a>
        @endforeach
    </div>
</nav>
