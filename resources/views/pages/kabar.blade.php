@php
    use App\Models\Announcement;

    $announcements = Announcement::query()
        ->published()
        ->ordered()
        ->take(20)
        ->get();
@endphp

<x-marketing-layout
    title="Kabar Veyra — Pembaruan &amp; Pengumuman"
    description="Ikuti pembaruan terbaru Veyra Educate: peluncuran aplikasi baru, fitur CBT, peningkatan sistem, dan pengumuman resmi platform."
>

    {{-- Hero Section --}}
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 via-white to-white dark:border-slate-800 dark:from-slate-900 dark:via-slate-950 dark:to-slate-950">
        <div class="container-site section pb-12 sm:pb-16 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-3.5 py-1 text-xs font-bold mb-4 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                KABAR VEYRA
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-ink dark:text-white max-w-4xl mx-auto leading-tight">
                Pembaruan &amp; Pengumuman Terbaru
            </h1>
            <p class="mt-4 text-sm sm:text-base lg:text-lg text-muted max-w-2xl mx-auto leading-relaxed">
                Informasi resmi tentang peluncuran aplikasi, peningkatan fitur, dan pengumuman penting dari tim Veyra Educate.
            </p>
        </div>
    </section>

    {{-- Announcements Feed --}}
    <section class="section container-site">
        @if ($announcements->isNotEmpty())
            <div class="max-w-3xl mx-auto space-y-6">
                @foreach ($announcements as $index => $announcement)
                    <article class="relative flex gap-5 py-6 {{ $index > 0 ? 'border-t border-slate-200 dark:border-slate-800' : '' }}">

                        {{-- Date badge --}}
                        <div class="hidden sm:flex flex-col items-center shrink-0">
                            <div class="w-12 h-12 rounded-xl bg-brand-600 text-white flex flex-col items-center justify-center shadow-md">
                                <span class="text-base font-extrabold leading-none">{{ $announcement->published_at->day }}</span>
                                <span class="text-[9px] font-bold uppercase leading-none opacity-80 mt-0.5">{{ $announcement->published_at->format('M') }}</span>
                            </div>
                            @if (!$loop->last)
                                <div class="w-px h-full bg-brand-200 dark:bg-brand-900 mt-1"></div>
                            @endif
                        </div>

                        {{-- Card content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 sm:hidden">
                                <span class="text-xs font-semibold text-brand-600 dark:text-brand-400">
                                    {{ $announcement->published_at->format('d M Y') }}
                                </span>
                            </div>
                            <h2 class="text-lg font-bold text-ink dark:text-white leading-snug">
                                {{ $announcement->title }}
                            </h2>
                            <div class="prose prose-sm text-muted dark:text-slate-400 mt-2 leading-relaxed max-w-none">
                                {!! nl2br(e($announcement->body)) !!}
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-20">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 mb-4">
                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-ink dark:text-white">Belum ada kabar terbaru.</h3>
                <p class="mt-1 text-sm text-muted dark:text-slate-500">Pengumuman akan muncul di halaman ini saat admin mempublikasikannya.</p>
                <a href="{{ route('kontak') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm mt-5">
                    Hubungi Kami
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        @endif
    </section>

</x-marketing-layout>
