<div class="flex flex-1 flex-col h-full overflow-hidden">
    {{-- Brand / School Header --}}
    @php
        $sidebarUser = auth()->user();
        $sidebarSchool = $sidebarUser?->school;
        $sidebarRole = $sidebarUser?->roleLabel() ?? ($sidebarSchool ? 'Operator Sekolah' : 'Wadah Aplikasi');
    @endphp
    <div class="shrink-0 px-5 py-4 border-b border-white/10 flex items-center justify-between gap-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0 flex-1" @click="sidebarOpen = false" title="{{ $sidebarSchool?->name ?? 'Veyra Educate' }}">
            {{-- Logo or Initial Avatar --}}
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md shadow-inner text-white font-extrabold text-base overflow-hidden border border-white/20 group-hover:scale-105 transition-transform">
                @if ($sidebarSchool?->logo_path)
                    <img
                        src="{{ asset('storage/' . $sidebarSchool->logo_path) }}"
                        alt="Logo {{ $sidebarSchool->name }}"
                        class="h-full w-full object-cover rounded-xl bg-white p-0.5"
                    />
                @elseif ($sidebarSchool)
                    <span class="text-white font-black text-sm tracking-wider">
                        {{ collect(explode(' ', trim((string) $sidebarSchool->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'SK' }}
                    </span>
                @else
                    <span class="text-[#fecf59] font-black text-lg">V</span>
                @endif
            </div>

            {{-- School Name and Position --}}
            <div class="flex min-w-0 flex-col leading-tight">
                @if ($sidebarSchool)
                    <span class="truncate text-sm font-extrabold tracking-tight !text-white" title="{{ $sidebarSchool->name }}">
                        {{ $sidebarSchool->name }}
                    </span>
                    <span class="text-[10px] font-semibold tracking-wider !text-white/80 truncate mt-0.5 flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#fecf59] shrink-0"></span>
                        <span>{{ $sidebarRole }}</span>
                    </span>
                @else
                    <span class="truncate text-base font-extrabold tracking-tight !text-white flex items-center gap-1.5">
                        <span class="text-[#fecf59]">Veyra</span>Educate
                    </span>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.16em] !text-white/80">
                        {{ $sidebarRole }}
                    </span>
                @endif
            </div>
        </a>

        {{-- Mobile Close Button --}}
        <button
            type="button"
            @click="sidebarOpen = false"
            class="md:hidden p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition shrink-0 cursor-pointer"
            aria-label="Tutup menu navigasi"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Quick Search (Command Palette Trigger) --}}
    <x-command-palette-trigger variant="sidebar-button" />

    {{-- Nav Items (scrollable if screen height is compact) --}}
    <nav class="flex-1 overflow-y-auto space-y-2 px-4 py-4 min-h-0 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" aria-label="Navigasi utama">
        {{-- Dashboard --}}
        <a
            href="{{ route('dashboard') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('dashboard') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('dashboard') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 ring-0 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('dashboard') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('dashboard') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('dashboard') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('dashboard') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Dashboard</span>
            @if(isset($recentAnnouncements) && $recentAnnouncements?->isNotEmpty())
                <span class="ml-auto flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[#fecf59] px-1.5 text-[10px] font-black !text-white shadow-sm">
                    {{ $recentAnnouncements->count() }}
                </span>
            @endif
        </a>

        {{-- Aplikasi --}}
        <a
            href="{{ route('apps.index') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('apps.*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('apps.*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('apps.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('apps.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('apps.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('apps.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Aplikasi</span>
        </a>

        {{-- Buku Panduan --}}
        <a
            href="{{ route('guides.index') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('guides.*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('guides.*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('guides.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('guides.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('guides.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('guides.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Buku Panduan</span>
        </a>

        {{-- Cetak Kartu Siswa & Ujian --}}
        <a
            href="{{ route('school.cards.index') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('school.cards.*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('school.cards.*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('school.cards.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.cards.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('school.cards.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.cards.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Cetak Kartu</span>
        </a>

        {{-- WhatsApp Broadcast --}}
        <a
            href="{{ route('school.broadcast.index') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('school.broadcast.*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('school.broadcast.*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('school.broadcast.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.broadcast.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('school.broadcast.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.broadcast.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">WhatsApp Notif</span>
        </a>

        {{-- Program Referral --}}
        <a
            href="{{ route('school.referral.index') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('school.referral.*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('school.referral.*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('school.referral.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.referral.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('school.referral.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.referral.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Referral &amp; Komisi</span>
        </a>

        {{-- Pembayaran / Tagihan & Kwitansi (Khusus Kepala Sekolah & Bendahara BOS) --}}
        @if($sidebarUser?->canAccessBilling())
            <a
                href="{{ route('pembayaran.history') }}"
                @click="sidebarOpen = false"
                class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ (request()->routeIs('pembayaran.*') || request()->routeIs('checkout.*')) ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
                :class="{{ (request()->routeIs('pembayaran.*') || request()->routeIs('checkout.*')) ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
            >
                <svg class="h-5 w-5 shrink-0 transition-colors {{ (request()->routeIs('pembayaran.*') || request()->routeIs('checkout.*')) ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ (request()->routeIs('pembayaran.*') || request()->routeIs('checkout.*')) ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="transition-colors {{ (request()->routeIs('pembayaran.*') || request()->routeIs('checkout.*')) ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ (request()->routeIs('pembayaran.*') || request()->routeIs('checkout.*')) ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Tagihan &amp; Kwitansi</span>
            </a>
        @endif

        {{-- Pengaturan Sekolah --}}
        <a
            href="{{ route('school.settings') }}"
            @click="sidebarOpen = false"
            class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('school.settings*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
            :class="{{ request()->routeIs('school.settings*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
        >
            <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('school.settings*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.settings*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="transition-colors {{ request()->routeIs('school.settings*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('school.settings*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Pengaturan</span>
        </a>

        {{-- Admin Panel (if admin) --}}
        @if (Auth::user()?->isAdmin())
            <a
                href="{{ route('admin.dashboard') }}"
                @click="sidebarOpen = false"
                class="group flex items-center gap-3.5 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all duration-200 relative {{ request()->routeIs('admin.*') ? 'bg-white text-[#0f766e] shadow-lg shadow-black/10' : '!text-white hover:bg-white/15' }}"
                :class="{{ request()->routeIs('admin.*') ? 'true' : 'false' }} ? ('bg-white ' + (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') + ' shadow-lg shadow-black/10 hover:bg-white/95') : '!text-white hover:bg-white/15 hover:!text-white'"
            >
                <svg class="h-5 w-5 shrink-0 transition-colors {{ request()->routeIs('admin.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('admin.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="transition-colors {{ request()->routeIs('admin.*') ? 'text-[#0f766e]' : '!text-white' }}" :class="{{ request()->routeIs('admin.*') ? 'true' : 'false' }} ? (themeConfig[schoolTheme]?.activeText || 'text-[#0f766e]') : '!text-white'">Admin Panel</span>
            </a>
        @endif
    </nav>

    {{-- PWA Install Option in Sidebar (Adaptive for Android / Desktop) --}}
    <div class="px-4 py-3 mt-auto border-t border-white/10">
        <x-pwa-install-button variant="sidebar" />
    </div>
</div>
