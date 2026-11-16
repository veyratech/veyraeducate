<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        <meta name="theme-color" content="#2563eb">
        <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" rel="stylesheet"></noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('partials.pwa-head')
    </head>
    <body class="min-h-screen lg:h-screen w-full max-w-full overflow-x-hidden bg-brand-950 font-sans text-ink antialiased">
        <x-auth-splash-screen />

        <a href="#form-auth" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white focus:ring-2 focus:ring-gold-500 focus:ring-offset-2">
            Lewati ke formulir
        </a>

        <div class="min-h-screen lg:h-screen w-full max-w-full overflow-x-hidden lg:overflow-hidden flex flex-col lg:flex-row">
            {{-- Kiri: Visual Brand --}}
            <div class="relative hidden lg:flex lg:w-[55%] h-full items-center p-12 xl:p-16 bg-brand-950 overflow-hidden">
                {{-- Background decorations --}}
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(224,167,60,0.18),transparent_50%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,rgba(13,134,143,0.3),transparent_45%)]"></div>

                {{-- Glow yang bergerak pelan di belakang teks --}}
                <div class="pointer-events-none absolute left-1/2 top-1/2 h-[26rem] w-[34rem] -translate-x-1/2 -translate-y-1/2">
                    <div class="h-full w-full rounded-full bg-brand-500/30 blur-[110px] animate-aurora"></div>
                </div>

                <div class="absolute -left-20 top-1/4 h-80 w-80 rounded-full bg-brand-700/20 blur-3xl animate-float-slow"></div>
                <div class="absolute -right-20 bottom-1/4 h-64 w-64 rounded-full bg-gold-500/15 blur-3xl animate-float-slower" style="animation-delay: 1s;"></div>

                {{-- Titik melayang --}}
                <span class="absolute right-[10%] top-[20%] hidden h-1.5 w-1.5 rounded-full bg-gold-300/60 animate-drift xl:block"></span>
                <span class="absolute left-[8%] bottom-[22%] hidden h-2 w-2 rounded-full bg-brand-300/50 animate-drift xl:block" style="animation-delay: 2.5s;"></span>
                <span class="absolute right-[24%] bottom-[16%] hidden h-1.5 w-1.5 rounded-full bg-gold-200/50 animate-drift xl:block" style="animation-delay: 5s;"></span>

                {{-- Sinar yang menyapu layar kiri --}}
                <div class="pointer-events-none absolute inset-y-0 left-0 w-64 bg-gradient-to-r from-transparent via-white/10 to-transparent blur-2xl animate-sweep"></div>

                {{-- Decorative grid --}}
                <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 30px 30px;"></div>

                <div class="relative z-10 w-full max-w-xl animate-slide-in-left">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/logo-veyra-educate.png') }}" alt="" class="h-14 w-auto object-contain" loading="eager">
                        <div>
                            <span class="block text-xl font-extrabold tracking-tight text-white">{{ config('app.name') }}</span>
                            <span class="text-xs font-medium uppercase tracking-[0.18em] text-gold-400">Admin Panel</span>
                        </div>
                    </a>

                    <h1 class="mt-12 text-4xl xl:text-5xl font-extrabold leading-[1.1] tracking-tight text-white">
                        Kelola sekolah,
                        <span class="relative text-gold-300">
                            satu platform
                            <span class="absolute -bottom-1 left-0 block h-1.5 w-full overflow-hidden rounded-full bg-gold-400/40">
                                <span class="absolute inset-y-0 left-[-100%] w-full animate-glint bg-gradient-to-r from-transparent via-gold-200 to-transparent"></span>
                            </span>
                        </span>
                        untuk semua.
                    </h1>

                    <p class="mt-5 text-lg leading-relaxed text-brand-100/80">
                        Monitoring, pembayaran, dan pengaturan konten dalam satu dashboard.
                    </p>
                </div>
            </div>

            {{-- Kanan: Form --}}
            <div class="flex flex-1 flex-col bg-surface min-w-0 w-full overflow-y-auto overflow-x-hidden">
                {{-- Header --}}
                <header class="flex items-center justify-between px-6 py-5 sm:px-10 lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                        <img src="{{ asset('images/logo-veyra-educate.png') }}" alt="" class="h-8 w-auto object-contain">
                        <span class="text-sm font-extrabold text-ink">{{ config('app.name') }}</span>
                    </a>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gold-700">Admin</span>
                </header>

                {{-- Form area --}}
                <main id="form-auth" tabindex="-1" class="auth-stage flex flex-1 items-center justify-center px-4 py-12 sm:px-8 lg:px-16">
                    <div class="w-full max-w-sm">
                        <div class="auth-card rounded-2xl border border-brand-100 bg-white p-8 shadow-card">
                            {{ $slot }}
                        </div>

                        <p class="mt-8 text-center text-xs text-muted">
                            &copy; {{ date('Y') }} {{ config('app.name') }}
                        </p>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
