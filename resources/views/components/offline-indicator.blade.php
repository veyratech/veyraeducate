{{-- Offline & Online Connection Status Banner --}}
<div
    x-data="{
        isOnline: navigator.onLine,
        showReconnected: false,
        timeoutId: null,
        init() {
            window.addEventListener('online', () => {
                this.isOnline = true;
                this.showReconnected = true;
                clearTimeout(this.timeoutId);
                this.timeoutId = setTimeout(() => {
                    this.showReconnected = false;
                }, 3500);
            });
            window.addEventListener('offline', () => {
                this.isOnline = false;
                this.showReconnected = false;
            });
        }
    }"
    x-cloak
>
    {{-- 1. Offline Alert Banner --}}
    <div
        x-show="!isOnline"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="-translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-full opacity-0"
        class="fixed top-0 inset-x-0 z-[120] bg-gradient-to-r from-amber-600 via-rose-600 to-amber-600 text-white shadow-xl border-b border-amber-300/30 px-3 py-2 sm:py-2.5 backdrop-blur-md safe-area-top"
        role="alert"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3 text-xs sm:text-sm font-semibold">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="flex h-2.5 w-2.5 relative shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                </span>
                <p class="truncate leading-tight">
                    <span class="font-extrabold">Koneksi Terputus:</span> Berjalan dalam mode offline (cache lokal aktif).
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="button"
                    @click="window.location.reload()"
                    class="px-2.5 py-1 rounded-lg bg-white/20 hover:bg-white/30 active:scale-95 text-white text-[11px] font-bold transition shadow-xs cursor-pointer"
                >
                    Coba Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Online Restored Alert Banner --}}
    <div
        x-show="showReconnected && isOnline"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="-translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-full opacity-0"
        class="fixed top-0 inset-x-0 z-[120] bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 text-white shadow-xl border-b border-emerald-300/30 px-3 py-2 sm:py-2.5 backdrop-blur-md safe-area-top"
        role="status"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3 text-xs sm:text-sm font-semibold">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="truncate leading-tight">
                    <span class="font-extrabold">Koneksi Kembali Online:</span> Data sistem tersinkronisasi normal.
                </p>
            </div>
            <button
                type="button"
                @click="showReconnected = false"
                class="p-1 rounded-md text-white/80 hover:text-white transition"
                aria-label="Tutup notifikasi"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
