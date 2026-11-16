@props([
    'position' => 'app', // 'app' | 'marketing'
])

@php
    $user = auth()->user();
    $school = $user?->school;
    $schoolName = $school?->name ?? 'sekolah kami';
    $userName = $user?->name ?? 'Operator';

    $defaultMsg = auth()->check()
        ? "Halo Tim Teknis Veyra Educate, saya {$userName} dari {$schoolName}. Saya membutuhkan bantuan terkait sistem/modul..."
        : "Halo Tim Veyra Educate, saya ingin konsultasi terkait sistem sekolah...";

    $waUrl = "https://wa.me/6285707074778?text=" . urlencode($defaultMsg);
@endphp

<div
    x-data="{
        isOpen: false,
        toggleWidget() {
            if (window.vibrateHaptic) window.vibrateHaptic(12);
            this.isOpen = !this.isOpen;
        }
    }"
    @click.outside="isOpen = false"
    class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-40 select-none"
>
    {{-- Popover Card (Slide up when opened) --}}
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="absolute bottom-16 right-0 w-72 sm:w-80 rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 p-4 sm:p-5 shadow-2xl border border-slate-200/90 dark:border-slate-800 ring-1 ring-black/10 transition-all z-50 text-slate-800 dark:text-slate-100"
        x-cloak
    >
        {{-- Header Popover --}}
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.7h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.74.98 1-3.64-.24-.37a9.8 9.8 0 0 1-1.5-5.23c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.03 6.96 2.89a9.78 9.78 0 0 1 2.88 6.95c0 5.42-4.41 9.83-9.83 9.83Zm5.4-7.37c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
                    </svg>
                </div>
                <div>
                    <h5 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-slate-100">Bantuan Operator</h5>
                    <div class="flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">Teknisi Standby</span>
                    </div>
                </div>
            </div>
            <button
                type="button"
                @click="isOpen = false"
                class="rounded-lg p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
                aria-label="Tutup popup"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body Description --}}
        <div class="py-3 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
            <p>
                Mengalami kendala sinkronisasi CBT, cetak rapor, atau pembayaran paket? Chat langsung dengan tim teknis Veyra Educate.
            </p>
        </div>

        {{-- CTA Button --}}
        <a
            href="{{ $waUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            @click="if (window.vibrateHaptic) window.vibrateHaptic([20, 40, 20]); isOpen = false"
            class="flex items-center justify-center gap-2 w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-98 text-white px-4 py-2.5 text-xs font-bold shadow-md shadow-emerald-600/20 transition-all cursor-pointer"
        >
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.7h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.74.98 1-3.64-.24-.37a9.8 9.8 0 0 1-1.5-5.23c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.03 6.96 2.89a9.78 9.78 0 0 1 2.88 6.95c0 5.42-4.41 9.83-9.83 9.83Zm5.4-7.37c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
            </svg>
            <span>Mulai Chat WhatsApp</span>
        </a>
    </div>

    {{-- Floating Action Button (FAB) --}}
    <button
        type="button"
        @click="toggleWidget()"
        class="group relative flex items-center gap-2 rounded-full bg-emerald-600 hover:bg-emerald-500 active:scale-95 !text-white text-white shadow-xl shadow-emerald-950/20 p-3 sm:px-4 sm:py-3 transition-all duration-300 hover:shadow-2xl hover:-translate-y-0.5 cursor-pointer ring-4 ring-emerald-500/20"
        aria-label="Bantuan WhatsApp"
        title="Hubungi bantuan WhatsApp"
    >
        {{-- Pulsating online indicator dot --}}
        <span class="absolute top-0 right-0 flex h-3 w-3 -mt-0.5 -mr-0.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400 border-2 border-white dark:border-slate-900"></span>
        </span>

        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 sm:h-5 sm:w-5 shrink-0 !text-white text-white" fill="currentColor">
            <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.7h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.74.98 1-3.64-.24-.37a9.8 9.8 0 0 1-1.5-5.23c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.03 6.96 2.89a9.78 9.78 0 0 1 2.88 6.95c0 5.42-4.41 9.83-9.83 9.83Zm5.4-7.37c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
        </svg>

        <span class="hidden sm:inline font-bold text-xs tracking-wide !text-white text-white">Bantuan WA</span>
    </button>
</div>
