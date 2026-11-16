<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        <meta name="theme-color" content="#3ec5a2">
        <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" rel="stylesheet"></noscript>

        <script>
            (function() {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
                var serverTheme = @json(auth()->user()?->school?->meta['brand_theme'] ?? null);
                var schoolTheme = serverTheme || localStorage.getItem('school_theme') || 'teal';
                localStorage.setItem('school_theme', schoolTheme);
                // Clear any legacy layout flag so sidebar is permanently restored
                localStorage.removeItem('dashboard_layout');
            })();

            window.themeConfig = {
                teal: {
                    name: 'Vitality Mint',
                    icon: '🌿',
                    color: '#3ec5a2',
                    bg: 'bg-[#d8f2eb]',
                    sidebar: 'from-[#46c7a4] via-[#4db8c8] to-[#4fa2df]',
                    hero: 'from-[#43c5a3] via-[#48bac6] to-[#50a6e3]',
                    activeText: 'text-[#0f766e]',
                    accent: '#3ec5a2'
                },
                indigo: {
                    name: 'Ocean Indigo',
                    icon: '🌊',
                    color: '#3b82f6',
                    bg: 'bg-[#dbeafe]',
                    sidebar: 'from-[#3b82f6] via-[#4f46e5] to-[#1d4ed8]',
                    hero: 'from-[#2563eb] via-[#3b82f6] to-[#0284c7]',
                    activeText: 'text-[#1e40af]',
                    accent: '#2563eb'
                },
                rose: {
                    name: 'Sunset Rose',
                    icon: '🌸',
                    color: '#f43f5e',
                    bg: 'bg-[#fdeee9]',
                    sidebar: 'from-[#f43f5e] via-[#fb7185] to-[#f97316]',
                    hero: 'from-[#e11d48] via-[#f43f5e] to-[#ea580c]',
                    activeText: 'text-[#be123c]',
                    accent: '#f43f5e'
                },
                emerald: {
                    name: 'Emerald Academy',
                    icon: '🌲',
                    color: '#10b981',
                    bg: 'bg-[#dcfce7]',
                    sidebar: 'from-[#10b981] via-[#059669] to-[#047857]',
                    hero: 'from-[#059669] via-[#10b981] to-[#0d9488]',
                    activeText: 'text-[#065f46]',
                    accent: '#10b981'
                },
                violet: {
                    name: 'Cyber Violet',
                    icon: '🔮',
                    color: '#8b5cf6',
                    bg: 'bg-[#ede9fe]',
                    sidebar: 'from-[#8b5cf6] via-[#7c3aed] to-[#4f46e5]',
                    hero: 'from-[#7c3aed] via-[#8b5cf6] to-[#6366f1]',
                    activeText: 'text-[#5b21b6]',
                    accent: '#8b5cf6'
                },
                slate: {
                    name: 'Minimal Bento',
                    icon: '🖤',
                    color: '#475569',
                    bg: 'bg-[#e2e8f0]',
                    sidebar: 'from-[#475569] via-[#334155] to-[#1e293b]',
                    hero: 'from-[#334155] via-[#475569] to-[#64748b]',
                    activeText: 'text-[#0f172a]',
                    accent: '#475569'
                }
            };

            window.sidebarCollapsed = false;

            window.appLayout = function() {
                return {
                    sidebarOpen: false,
                    sidebarCollapsed: false,
                    theme: localStorage.getItem('theme') || 'light',
                    schoolTheme: localStorage.getItem('school_theme') || 'teal',
                    themeDropdownOpen: false,
                    themeConfig: window.themeConfig,
                    init: function() {
                        if (this.theme === 'dark') {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    },
                    toggleTheme: function() {
                        this.theme = this.theme === 'dark' ? 'light' : 'dark';
                        if (this.theme === 'dark') {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                        localStorage.setItem('theme', this.theme);
                    },
                    selectTheme: function(key) {
                        this.schoolTheme = key;
                        localStorage.setItem('school_theme', key);
                        this.themeDropdownOpen = false;
                        @auth
                        var tokenEl = document.querySelector('meta[name="csrf-token"]');
                        var token = tokenEl ? tokenEl.getAttribute('content') : '';
                        fetch('{{ route('school.theme.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ brand_theme: key })
                        }).catch(function() {});
                        @endauth
                    }
                };
            };

            document.addEventListener('alpine:init', function() {
                if (window.Alpine) {
                    window.Alpine.data('appLayout', window.appLayout);
                }
            });
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('partials.pwa-head')
    </head>
    <body
        x-data="appLayout()"
        @change-school-theme.window="selectTheme($event.detail)"
        @toggle-app-theme.window="toggleTheme()"
        :class="themeConfig[schoolTheme]?.bg || 'bg-[#d8f2eb]'"
        class="min-h-screen font-sans antialiased text-slate-800 dark:text-slate-100 p-2 sm:p-4 lg:p-6 transition-colors duration-300 dark:bg-slate-950 bg-[#d8f2eb]"
    >
        {{-- PWA Offline / Online Connection Indicator --}}
        <x-offline-indicator />

        <div class="max-w-[1600px] w-full mx-auto min-h-[calc(100vh-1rem)] sm:min-h-[calc(100vh-2rem)] lg:min-h-[calc(100vh-3rem)] flex flex-col md:flex-row gap-4 lg:gap-6">
            
            {{-- Desktop Sidebar (Always visible on desktop and tablet/laptop >= 768px) --}}
            <aside
                :class="'bg-gradient-to-b ' + (themeConfig[schoolTheme]?.sidebar || themeConfig.teal.sidebar)"
                class="hidden md:flex md:w-60 lg:w-64 md:shrink-0 md:rounded-[2.5rem] text-white shadow-xl shadow-black/10 flex-col overflow-hidden sticky top-4 lg:top-6 h-[calc(100vh-2rem)] lg:h-[calc(100vh-3rem)] transition-all duration-300 md:z-30 bg-gradient-to-b from-[#46c7a4] via-[#4db8c8] to-[#4fa2df]"
            >
                @include('layouts.navigation')
            </aside>

            {{-- Mobile Slide-over Sidebar --}}
            <aside
                :class="[('bg-gradient-to-b ' + (themeConfig[schoolTheme]?.sidebar || themeConfig.teal.sidebar)), (sidebarOpen ? 'translate-x-0' : '-translate-x-full')]"
                class="fixed inset-y-0 left-0 z-[70] flex w-72 max-w-[85vw] flex-col text-white shadow-2xl transition-transform duration-300 ease-in-out md:hidden safe-area-top safe-area-bottom bg-gradient-to-b from-[#46c7a4] via-[#4db8c8] to-[#4fa2df]"
                x-cloak
            >
                @include('layouts.navigation')
            </aside>

            {{-- Mobile overlay backdrop --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm md:hidden"
                @click="sidebarOpen = false"
                aria-hidden="true"
                x-cloak
            ></div>

            {{-- Main Content Canvas Frame --}}
            <div class="flex-1 min-w-0 bg-[#f7faf8] dark:bg-slate-900 rounded-[2rem] sm:rounded-[2.5rem] shadow-xl shadow-teal-900/5 flex flex-col overflow-hidden border border-white/60 dark:border-slate-800">
                
                {{-- Mobile top bar (Shown only on phones < 768px) --}}
                <header class="border-b border-emerald-100/60 dark:border-slate-800 bg-white/80 backdrop-blur-xl dark:bg-slate-850/80 px-3.5 sm:px-4 py-2.5 sm:py-3 md:hidden flex items-center justify-between gap-2.5 sm:gap-3 sticky top-0 z-50">
                    <button
                        type="button"
                         class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-emerald-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 dark:text-slate-400 dark:hover:bg-slate-800"
                        aria-label="Buka menu"
                        @click="sidebarOpen = true"
                    >
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @php
                        $mobileSchool = auth()->user()?->school;
                    @endphp
                    <a href="{{ route('dashboard') }}" class="min-w-0 flex items-center gap-2">
                        @if ($mobileSchool?->logo_path)
                            <img src="{{ asset('storage/' . $mobileSchool->logo_path) }}" alt="Logo" class="h-6 w-6 rounded-lg object-cover bg-white p-0.5 border border-slate-200 dark:border-slate-700 shrink-0">
                        @endif
                        <span class="truncate text-sm font-extrabold tracking-tight text-slate-800 dark:text-white">
                            {{ $mobileSchool?->name ?? 'Veyra Educate' }}
                        </span>
                    </a>

                        <div class="flex items-center gap-1">
                            {{-- Mobile Quick Search (Command Palette Trigger) --}}
                            <x-command-palette-trigger variant="mobile-icon" />

                            {{-- App Switcher (Waffle Menu) --}}
                            <x-app-switcher variant="mobile" />

                            {{-- In-App Notification Center --}}
                            <x-notification-center />

                            {{-- PWA Install Button for Mobile (Auto-hidden if already installed) --}}
                            <x-pwa-install-button variant="topbar" />

                            {{-- Dark Mode Toggle --}}
                            <button
                                type="button"
                                @click="theme = theme === 'dark' ? 'light' : 'dark'; if (theme === 'dark') { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') } localStorage.setItem('theme', theme)"
                                class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-emerald-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 dark:text-slate-400 dark:hover:bg-slate-800 transition cursor-pointer"
                                aria-label="Toggle tema"
                            >
                            <svg x-show="theme === 'light'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="theme === 'dark'" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        {{-- Mobile Profile Avatar Dropdown --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex items-center gap-1.5 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-2 dark:border-slate-700 dark:bg-slate-800 hover:bg-slate-50 transition cursor-pointer"
                                aria-haspopup="true"
                            >
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-tr from-brand-600 to-brand-400 text-white font-bold text-[10px]" aria-hidden="true">
                                    {{ collect(explode(' ', trim((string) auth()->user()?->name)))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}
                                </span>
                                <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition x-cloak class="absolute right-0 top-full mt-2 w-48 rounded-xl border border-slate-200/90 bg-white py-1.5 shadow-2xl ring-1 ring-slate-900/10 z-50 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">
                                <div class="px-3 py-1.5 border-b border-slate-100 dark:border-slate-800">
                                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ auth()->user()?->name }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()?->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" @click="open = false" class="flex w-full items-center gap-2 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">
                                    Profil
                                </a>
                                <a href="{{ route('school.settings') }}" @click="open = false" class="flex w-full items-center gap-2 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">
                                    Pengaturan
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-slate-800 pt-1">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30 cursor-pointer">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </header>

                {{-- Page header (for pages defining $header) --}}
                @isset($header)
                    <header class="relative z-10 lg:z-30 border-b border-emerald-100/60 dark:border-slate-800 bg-white/70 backdrop-blur-xl dark:bg-slate-900/70">
                        <div class="px-6 sm:px-8 py-3.5 flex items-center justify-between gap-4">
                            <div>{{ $header }}</div>
                            <div class="hidden md:flex shrink-0 items-center gap-2 sm:gap-3">
                                {{-- Quick Search Command Palette Trigger --}}
                                <x-command-palette-trigger variant="desktop-bar" />

                                {{-- App Switcher (Waffle Menu) --}}
                                <x-app-switcher variant="desktop" />

                                {{-- In-App Notification Center --}}
                                <x-notification-center />

                                {{-- User Profile Pill (avatar + name + email + dropdown) --}}
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="flex items-center gap-2.5 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-3 dark:border-slate-700 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm cursor-pointer"
                                        aria-haspopup="true"
                                        aria-expanded="{{ request()->routeIs('profile.*') ? 'true' : 'false' }}"
                                    >
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-tr from-brand-600 to-brand-400 text-white font-bold text-xs shadow-sm" aria-hidden="true">
                                            {{ collect(explode(' ', trim((string) auth()->user()?->name)))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}
                                        </span>
                                        <span class="hidden min-w-0 leading-tight sm:block">
                                            <span class="block truncate text-xs font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()?->name }}</span>
                                            <span class="block truncate text-[10px] text-slate-500 dark:text-slate-400">{{ auth()->user()?->email }}</span>
                                        </span>
                                        <svg class="h-3 w-3 text-slate-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition x-cloak class="absolute right-0 top-full mt-2 w-52 rounded-2xl border border-slate-200/90 bg-white py-2 shadow-2xl z-50 dark:border-slate-800 dark:bg-slate-900">
                                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                                            <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ auth()->user()?->name }}</p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()?->email }}</p>
                                        </div>
                                        <div class="py-1">
                                            <a
                                                href="{{ route('profile.edit') }}"
                                                @click="open = false"
                                                class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white"
                                            >
                                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Profil Akun
                                            </a>
                                            <a
                                                href="{{ route('school.settings') }}"
                                                @click="open = false"
                                                class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white"
                                            >
                                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Pengaturan Sekolah
                                            </a>
                                        </div>
                                        <div class="border-t border-slate-100 dark:border-slate-800 pt-1">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 cursor-pointer"
                                                >
                                                    <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    </svg>
                                                    Keluar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dark Mode Toggle --}}
                                <button
                                    type="button"
                                    @click="theme = theme === 'dark' ? 'light' : 'dark'; if (theme === 'dark') { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') } localStorage.setItem('theme', theme)"
                                    class="shrink-0 inline-flex items-center justify-center rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none transition dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 border border-slate-200/60 dark:border-slate-700 cursor-pointer"
                                    aria-label="Toggle tema"
                                >
                                    <svg x-show="theme === 'light'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    <svg x-show="theme === 'dark'" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </header>
                @endisset

                {{-- Status flash message --}}
                @if (session('status'))
                    <div class="mx-6 sm:mx-8 mt-4 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 dark:border-emerald-800/40 dark:bg-emerald-950/20">
                        <p class="text-xs sm:text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('status') }}</p>
                    </div>
                @endif

                {{-- Slot content --}}
                <main class="flex-1 overflow-y-auto p-3.5 sm:p-6 lg:p-8 pb-24 md:pb-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @include('partials.bottom-nav-app')

        {{-- Global Command Palette (Ctrl+K / Cmd+K) --}}
        <x-command-palette />

        {{-- Floating WhatsApp Operator Support --}}
        <x-whatsapp-float />

        @stack('scripts')
    </body>
</html>
