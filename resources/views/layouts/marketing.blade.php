<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') . ' — Wadah Aplikasi Sekolah Terpadu' }}</title>
        <meta name="description" content="{{ $description ?? 'Veyra Educate — wadah aplikasi sekolah: dashboard, akun terpusat, dan langganan transparan untuk sekolah Indonesia.' }}">
        <meta name="theme-color" content="#2563eb">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $title ?? config('app.name') }}">
        <meta property="og:description" content="{{ $description ?? 'Wadah aplikasi sekolah untuk Indonesia.' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('images/logo-veyra-educate.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        <link rel="canonical" href="{{ url()->current() }}">

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        <link rel="preload" as="image" href="{{ asset('images/logo-veyra-educate.png') }}" fetchpriority="high">
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
    <body class="min-h-screen font-sans antialiased bg-white dark:bg-slate-900">
        <div
            x-data="{ theme: localStorage.getItem('theme') || 'light' }"
            x-init="if (theme === 'dark') document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
        >
        {{-- PWA Offline / Online Connection Indicator --}}
        <x-offline-indicator />

        <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white focus:ring-2 focus:ring-gold-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            Lewati ke konten
        </a>

        @include('partials.marketing-nav')

        @if (session('status'))
            <div class="border-b border-gold-200 bg-gold-50 dark:border-gold-800/50 dark:bg-gold-950/40" role="status">
                <div class="container-site py-3">
                    <p class="text-sm font-semibold text-gold-800 dark:text-gold-300">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <main id="konten" tabindex="-1" class="pb-16 lg:pb-0">
            {{ $slot }}
        </main>

        @include('partials.marketing-footer')

        @include('partials.bottom-nav-marketing')

        <a
            href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate%2C%20saya%20ingin%20konsultasi%20untuk%20sekolah%20saya."
            target="_blank"
            rel="noopener noreferrer"
            class="fixed bottom-20 right-4 z-40 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 dark:bg-emerald-700 h-12 w-12 sm:h-auto sm:w-auto p-3 sm:px-4 sm:py-3 text-sm font-semibold !text-white text-white shadow-xl transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-500 dark:hover:bg-emerald-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 sm:bottom-6 sm:right-6 lg:bottom-5"
            aria-label="Konsultasi via WhatsApp"
        >
            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 sm:h-5 sm:w-5 !text-white text-white" fill="currentColor">
                <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.7h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.74.98 1-3.64-.24-.37a9.8 9.8 0 0 1-1.5-5.23c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.03 6.96 2.89a9.78 9.78 0 0 1 2.88 6.95c0 5.42-4.41 9.83-9.83 9.83Zm5.4-7.37c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
            </svg>
            <span class="hidden sm:inline !text-white text-white font-semibold">Konsultasi WA</span>
        </a>

        {{-- Global Command Palette (Ctrl+K) --}}
        <x-command-palette />
        </div>
    </body>
</html>
