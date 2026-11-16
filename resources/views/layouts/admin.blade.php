<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ?? 'Admin — '.config('app.name') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        <meta name="theme-color" content="#2563eb">
        <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" rel="stylesheet"></noscript>

        <script>
            (function() {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('partials.pwa-head')
    </head>
    <body class="min-h-screen font-sans antialiased bg-[#f8fafc] dark:bg-slate-900">
        {{-- PWA Offline / Online Connection Indicator --}}
        <x-offline-indicator />

        <div
            x-data="{
                theme: localStorage.getItem('theme') || 'light',
                sidebarOpen: false,
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                }
            }"
            x-init="
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            "
            class="admin-scope min-h-screen bg-[#f8fafc] dark:bg-slate-900 flex"
        >
            {{-- Mobile overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-xs lg:hidden"
                aria-hidden="true"
                x-cloak
            ></div>

            {{-- Sidebar --}}
            <aside
                class="fixed inset-y-0 left-0 z-[70] flex max-w-[85vw] flex-col transition-all duration-300 ease-out bg-[#0f172a] dark:bg-[#020617] border-r border-white/5 lg:sticky lg:inset-y-auto lg:top-0 lg:h-screen lg:z-30 safe-area-top safe-area-bottom shrink-0"
                :class="[
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                    sidebarCollapsed ? 'sidebar-is-collapsed lg:w-16 w-64' : 'w-64'
                ]"
                aria-label="Sidebar navigasi"
            >
                <div class="flex h-14 items-center justify-between gap-2.5 border-b border-white/5 px-4 shrink-0 sidebar-header">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img src="{{ asset('images/logo-veyra-educate.png') }}" alt="" width="28" height="28" class="h-7 w-auto object-contain shrink-0 sidebar-brand" />
                        <div class="flex flex-col leading-tight min-w-0 sidebar-text">
                            <span class="text-[13px] font-bold tracking-tight text-white truncate">Veyra Educate</span>
                            <span class="text-[10px] font-medium text-slate-400 truncate">Admin Panel</span>
                        </div>
                    </div>
                    {{-- Desktop Collapse/Expand Toggle --}}
                    <button
                        type="button"
                        @click="toggleSidebar()"
                        class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition cursor-pointer"
                        :title="sidebarCollapsed ? 'Perlebar Sidebar' : 'Kecilkan Sidebar'"
                        aria-label="Toggle collapse sidebar"
                    >
                        <svg class="h-4 w-4 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                    {{-- Mobile Close Button --}}
                    <button
                        type="button"
                        @click="sidebarOpen = false"
                        class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition cursor-pointer"
                        aria-label="Tutup menu admin"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Command Palette Quick Search Trigger --}}
                <x-command-palette-trigger variant="sidebar-button" />

                <nav class="flex-1 space-y-0.5 px-3 py-3 overflow-y-auto scrollbar-minimal" aria-label="Navigasi admin">
                    @php
                        $navItems = [
                            ['type' => 'link', 'label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM10 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM10 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z'],
                            ['type' => 'link', 'label' => 'Katalog Aplikasi', 'route' => 'admin.link-access.index', 'pattern' => 'admin.link-access.*', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                            ['type' => 'link', 'label' => 'Sekolah', 'route' => 'admin.schools.index', 'pattern' => 'admin.schools.*', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-16 0H3m2-9h.01M12 12h.01M17 12h.01M8 12H7.99M12 16h.01'],
                            ['type' => 'link', 'label' => 'Pengguna', 'route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 01-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['type' => 'link', 'label' => 'Paket', 'route' => 'admin.plans.index', 'pattern' => 'admin.plans.*', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                            ['type' => 'link', 'label' => 'Pembayaran', 'route' => 'admin.payments.index', 'pattern' => 'admin.payments.*', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                            ['type' => 'link', 'label' => 'API Tokens', 'route' => 'admin.api-tokens.index', 'pattern' => 'admin.api-tokens.*', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z'],
                            ['type' => 'link', 'label' => 'Referral & Komisi', 'route' => 'admin.referrals.index', 'pattern' => 'admin.referrals.*', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['type' => 'link', 'label' => 'Pengumuman', 'route' => 'admin.announcements.index', 'pattern' => 'admin.announcements.*', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                            ['type' => 'link', 'label' => 'Buku Panduan', 'route' => 'admin.guides.index', 'pattern' => 'admin.guides.*', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                            ['type' => 'link', 'label' => 'Permintaan', 'route' => 'admin.app-requests.index', 'pattern' => 'admin.app-requests.*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            ['type' => 'link', 'label' => 'Kelola Homepage', 'route' => 'admin.homepage.index', 'pattern' => 'admin.homepage.*', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 11a1 1 0 011-1h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8z'],
                            ['type' => 'link', 'label' => 'Pengaturan', 'route' => 'admin.settings.index', 'pattern' => 'admin.settings.*', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
['type' => 'link', 'label' => 'Database & Backup', 'route' => 'admin.backup.index', 'pattern' => 'admin.backup.*', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'],
                                 ['type' => 'link', 'label' => 'Diagnosa Sistem', 'route' => 'admin.system.diagnose', 'pattern' => 'admin.system.diagnose*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                                 ['type' => 'link', 'label' => 'Kesehatan Sistem', 'route' => 'admin.system.health', 'pattern' => 'admin.system.health*', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                               ['type' => 'link', 'label' => 'Audit Log', 'route' => 'admin.audit-logs.index', 'pattern' => 'admin.audit-logs.*', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                ['type' => 'link', 'label' => 'Jadwal Sistem', 'route' => 'admin.schedule.index', 'pattern' => 'admin.schedule.*', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['type' => 'link', 'label' => 'Deployment', 'route' => 'admin.deployments.index', 'pattern' => 'admin.deployments.*', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 6l-1 2H8L7 6l-1 2M8 6v10a1 1 0 001 1h6a1 1 0 001-1V6'],
                         ];
                        if (! function_exists('isActive')) {
                            function isActive($patterns) {
                                foreach ((array) $patterns as $p) {
                                    if (request()->routeIs($p)) return true;
                                }
                                return false;
                            }
                        }
                        if (! function_exists('activeClass')) {
                            function activeClass($patterns) {
                                return isActive($patterns) ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white';
                            }
                        }
                    @endphp
                    @foreach($navItems as $item)
                        @if($item['type'] === 'link')
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150 {{ activeClass($item['pattern']) }}"
                               aria-current="{{ isActive($item['pattern']) ? 'page' : null }}"
                               @click="sidebarOpen = false"
                               :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                               :title="sidebarCollapsed ? '{{ $item['label'] }}' : null"
                            >
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                </svg>
                                <span class="sidebar-text truncate">{{ $item['label'] }}</span>
                            </a>
                        @elseif($item['type'] === 'submenu')
                            @php
                                $subPatterns = array_merge(
                                    ['admin.web-apps.*', 'admin.deployments.*'],
                                    ...array_map(fn ($child) => (array) $child['pattern'], $item['children'])
                                );
                                $subActive = isActive($subPatterns);
                            @endphp
                            <div x-data="{ open: {{ $subActive ? 'true' : 'false' }} }" class="space-y-1">
                                <button @click="if (sidebarCollapsed) { sidebarCollapsed = false; localStorage.setItem('sidebarCollapsed', false); open = true; } else { open = !open; }"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150 {{ $subActive ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                    aria-expanded="{{ $subActive ? 'true' : 'false' }}"
                                    :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                                    :title="sidebarCollapsed ? '{{ $item['label'] }}' : null"
                                >
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                    </svg>
                                    <span class="sidebar-text flex-1 truncate text-left">{{ $item['label'] }}</span>
                                    <svg class="sidebar-text h-3 w-3 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div x-show="open && !sidebarCollapsed" x-transition:enter="transition-all ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition-all ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="pl-7 space-y-0.5">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ route($child['route']).($child['hash'] ?? '') }}"
                                           class="block rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ isActive($child['pattern']) ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                                           aria-current="{{ isActive($child['pattern']) ? 'page' : null }}"
                                           @click="sidebarOpen = false"
                                        >
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </nav>

                <div class="border-t border-white/5 px-3 py-3 space-y-1 shrink-0">
                    <a href="{{ url('/') }}" @click="sidebarOpen = false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-xs text-slate-500 hover:text-slate-300 hover:bg-white/5 transition-colors"
                       :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                       :title="sidebarCollapsed ? 'Situs publik' : null">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span class="sidebar-text">Situs publik</span>
                    </a>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex min-w-0 flex-1 flex-col min-h-screen w-full max-w-full overflow-x-hidden">
                {{-- Mobile top bar --}}
                <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 lg:hidden sticky top-0 z-50">
                    <div class="flex items-center justify-between px-4 py-3 gap-3">
                        <button
                            type="button"
                            @click="sidebarOpen = true"
                             class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 transition-colors dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                             aria-label="Buka menu"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                            <img src="{{ asset('images/logo-veyra-educate.png') }}" alt="" width="24" height="24" class="h-6 w-auto object-contain">
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Veyra Admin</span>
                        </a>
                        <div class="flex items-center gap-2">
                            <x-command-palette-trigger variant="mobile-icon" />
                            <x-notification-center />
                            <x-pwa-install-button variant="topbar" />
                            <button
                                type="button"
                                @click="theme = theme === 'dark' ? 'light' : 'dark'; if (theme === 'dark') { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') } localStorage.setItem('theme', theme)"
                                 class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 transition-colors dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                                aria-label="Toggle tema"
                            >
                                <svg x-show="theme === 'light'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                <svg x-show="theme === 'dark'" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </button>
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex items-center gap-2 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-2.5 dark:border-slate-700 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                    aria-haspopup="true"
                                >
                                        <span class="avatar-minimal avatar-minimal-sm avatar-minimal-brand" aria-hidden="true">{{ collect(explode(' ', trim((string) auth()->user()?->name)))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}</span>
                                    <svg class="h-3 w-3 text-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition x-cloak class="absolute right-0 top-full mt-2 w-44 rounded-xl border border-slate-200/90 bg-white py-1.5 shadow-2xl ring-1 ring-slate-900/10 z-50 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">
                                    <a href="{{ route('profile.edit') }}" @click="open = false" class="flex w-full items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Profil
                                    </a>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/30 dark:hover:text-red-300">
                                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Desktop page header --}}
                @isset($header)
                    <header class="border-b border-slate-200 bg-white/80 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/80 relative lg:sticky lg:top-0 z-10 lg:z-20">
                        <div class="container-site py-4 flex items-center justify-between gap-4">
                            <div>{{ $header }}</div>
                            <div class="hidden lg:flex shrink-0 items-center gap-2">
                                <x-command-palette-trigger variant="desktop-bar" />
                                <x-notification-center />
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex items-center gap-2.5 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-3 dark:border-slate-700 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                        aria-haspopup="true"
                                    >
                                    <span class="avatar-minimal avatar-minimal-sm avatar-minimal-brand" aria-hidden="true">{{ collect(explode(' ', trim((string) auth()->user()?->name)))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}</span>
                                        <span class="hidden min-w-0 leading-tight sm:block">
                                            <span class="block truncate text-xs font-semibold text-ink dark:text-slate-100">{{ auth()->user()?->name }}</span>
                                            <span class="block truncate text-[10px] text-muted dark:text-slate-400">{{ auth()->user()?->email }}</span>
                                        </span>
                                        <svg class="h-3 w-3 text-muted shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-transition x-cloak class="absolute right-0 top-full mt-2 w-48 rounded-lg border border-slate-200 bg-white py-1 shadow-dropdown z-50 dark:border-slate-700 dark:bg-slate-900">
                                        <a href="{{ route('profile.edit') }}" @click="open = false" class="flex w-full items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Profil
                                        </a>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/30 dark:hover:text-red-300">
                                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                Keluar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="theme = theme === 'dark' ? 'light' : 'dark'; if (theme === 'dark') { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') } localStorage.setItem('theme', theme)"
                                    class="shrink-0 inline-flex items-center justify-center rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 transition-colors dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                                    aria-label="Toggle tema"
                                >
                                    <svg x-show="theme === 'light'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                    <svg x-show="theme === 'dark'" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </button>
                            </div>
                        </div>
                    </header>
                @endif

                @if (session('status'))
                    <div class="border-b border-emerald-200 bg-emerald-50 dark:border-emerald-800/40 dark:bg-emerald-950/20">
                        <div class="container-site py-3">
                            <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <main class="flex-1 pb-24 lg:pb-0">
                    {{ $slot }}
                </main>
            </div>

            @include('partials.bottom-nav-admin')
        </div>

        {{-- Global Command Palette (Ctrl+K / Cmd+K) --}}
        <x-command-palette />

        @stack('scripts')
    </body>
</html>
