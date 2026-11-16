{{-- Bottom navigation mobile — halaman publik (homepage, fitur, harga, demo, dll) --}}
<nav class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-200/70 bg-white/95 backdrop-blur-xl dark:border-slate-700/60 dark:bg-slate-950/95 safe-area-bottom lg:hidden" aria-label="Navigasi bawah">
    <div class="grid grid-cols-5">
        @php
            $bottomNavMarketing = [
                [
                    'label' => 'Beranda',
                    'url' => url('/'),
                    'active' => request()->routeIs('home'),
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
                [
                    'label' => 'Fitur',
                    'url' => url('fitur'),
                    'active' => request()->routeIs('fitur'),
                    'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
                ],
                [
                    'label' => 'Harga',
                    'url' => url('harga'),
                    'active' => request()->routeIs('harga'),
                    'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                ],
                [
                    'label' => 'Demo',
                    'url' => url('demo'),
                    'active' => request()->routeIs('demo'),
                    'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'label' => 'Akun',
                    'url' => auth()->check() ? route('dashboard') : route('login'),
                    'active' => request()->routeIs('dashboard', 'login', 'register', 'profile.*'),
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                ],
            ];
        @endphp
        @foreach ($bottomNavMarketing as $item)
            <a
                href="{{ $item['url'] }}"
                class="flex flex-col items-center gap-1 py-2.5 text-[11px] transition-colors {{ $item['active'] ? 'font-semibold text-brand-700 dark:text-brand-400' : 'font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}"
                @if ($item['active']) aria-current="page" @endif
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span>{{ $item['label'] }}</span>
                <span class="h-1 w-6 rounded-full {{ $item['active'] ? 'bg-brand-600 dark:bg-brand-400' : 'bg-transparent' }}" aria-hidden="true"></span>
            </a>
        @endforeach
    </div>
</nav>
