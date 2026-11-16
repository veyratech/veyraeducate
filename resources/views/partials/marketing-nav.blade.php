<header class="sticky top-0 z-50 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-xl transition-all">
    <div class="container-site flex h-16 items-center justify-between gap-4">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 shrink-0 group" aria-label="{{ config('app.name') }} beranda">
            <x-brand-logo size="sm" :with-text="false" class="sm:hidden" />
            <x-brand-logo size="sm" :with-text="true" class="hidden sm:inline-flex group-hover:opacity-90 transition-opacity" />
        </a>

        <nav class="hidden items-center gap-1 lg:flex px-2 py-1 rounded-full bg-slate-100/70 dark:bg-slate-900/70 border border-slate-200/60 dark:border-slate-800/60 backdrop-blur-md" aria-label="Navigasi utama">
            @foreach ([
                 ['fitur', 'Fitur'],
                 ['harga', 'Harga'],
                 ['demo', 'Demo'],
                 ['kabar', 'Kabar'],
                 ['brosur', 'Brosur'],
                 ['proposal', 'Proposal'],
                ['tentang', 'Tentang'],
                ['kontak', 'Kontak'],
            ] as [$slug, $label])
                @php
                    $isActive = request()->is($slug) || request()->is($slug . '/*');
                @endphp
                <a
                    href="{{ url($slug) }}"
                    class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all duration-150 {{ $isActive ? 'bg-white dark:bg-slate-800 text-brand-700 dark:text-brand-300 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-1 sm:gap-2">
            {{-- Quick Search (Ctrl+K) Trigger --}}
            <button
                type="button"
                @click="$dispatch('open-command-palette')"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/80 transition-all cursor-pointer"
                aria-label="Pencarian cepat (Ctrl+K)"
                title="Pencarian cepat (Ctrl+K)"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            {{-- Dark Mode Switcher (Clean ghost icon) --}}
            <button
                type="button"
                x-on:click="$root.theme = $root.theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', $root.theme); document.documentElement.classList.toggle('dark', $root.theme === 'dark')"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/80 transition-all cursor-pointer"
                aria-label="Ganti tema"
                title="Ganti tema"
            >
                <svg class="h-4 w-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                </svg>
                <svg class="h-4 w-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                </svg>
            </button>

            <div class="h-4 w-px bg-slate-200 dark:bg-slate-800 mx-1"></div>

            @auth
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-1.5 rounded-full bg-brand-600 hover:bg-brand-700 !text-white text-white px-3.5 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-semibold shadow-xs hover:shadow-md transition-all active:scale-95"
                >
                    <span class="!text-white text-white">Dashboard</span>
                    <svg class="h-3.5 w-3.5 hidden sm:block !text-white text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="px-2.5 py-1.5 sm:px-3 sm:py-2 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors"
                >
                    Masuk
                </a>
                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center justify-center rounded-full bg-brand-600 hover:bg-brand-700 !text-white text-white px-3.5 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-semibold shadow-xs hover:shadow-md transition-all active:scale-95"
                >
                    <span class="!text-white text-white">Daftar</span>
                </a>
            @endauth
        </div>
    </div>
</header>