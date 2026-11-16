@props([
    'variant' => 'desktop-bar', // 'desktop-bar' | 'mobile-icon' | 'sidebar-button'
])

@if ($variant === 'mobile-icon')
    {{-- Mobile Icon Trigger Button --}}
    <button
        type="button"
        @click="$dispatch('open-command-palette')"
        class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-emerald-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 dark:text-slate-400 dark:hover:bg-slate-800 transition cursor-pointer"
        aria-label="Pencarian Cepat"
        title="Pencarian cepat (Ctrl+K)"
    >
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </button>
@elseif ($variant === 'sidebar-button')
    {{-- Sidebar Full Width Button --}}
    <div class="px-3 pt-3 pb-1" :class="(typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed) ? 'lg:px-2' : ''">
        <button
            type="button"
            @click="$dispatch('open-command-palette')"
            class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white/90 text-xs font-semibold backdrop-blur-md transition border border-white/10 cursor-pointer shadow-sm group hover:scale-[1.01]"
            :class="(typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed) ? 'lg:justify-center lg:px-2' : ''"
            aria-label="Pencarian Cepat"
            :title="(typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed) ? 'Pencarian Cepat (Ctrl+K)' : null"
        >
            <div class="flex items-center gap-2.5">
                <svg class="h-4 w-4 text-white/70 group-hover:text-white transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="sidebar-text truncate">Pencarian Cepat</span>
            </div>
            <kbd class="sidebar-text px-1.5 py-0.5 text-[10px] font-bold bg-black/20 text-white/80 rounded-md border border-white/20 shadow-xs">Ctrl K</kbd>
        </button>
    </div>
@else
    {{-- Desktop Header Bar Trigger --}}
    <button
        type="button"
        @click="$dispatch('open-command-palette')"
        class="hidden sm:flex items-center gap-2.5 px-3 sm:px-3.5 py-1.5 text-xs text-slate-400 bg-white/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-700/80 rounded-xl transition shadow-xs hover:shadow-sm group cursor-pointer"
        aria-label="Cari menu, modul, aksi... (Ctrl+K)"
        title="Pencarian cepat (Tekan Ctrl+K)"
    >
        <svg class="h-3.5 w-3.5 text-slate-400 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <span class="text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200 font-medium">Cari modul, menu, aksi...</span>
        <kbd class="ml-2 px-1.5 py-0.5 text-[10px] font-bold text-slate-400 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/80 border border-slate-200 dark:border-slate-600 rounded-md shadow-xs">Ctrl K</kbd>
    </button>
@endif
