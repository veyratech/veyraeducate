@props(['id' => 'planModal', 'plans' => [], 'currentPlan' => null, 'title' => 'Pilih Paket Langganan Sekolah'])

<dialog
    id="{{ $id }}"
    x-data
    x-init="$nextTick(() => {
        document.querySelectorAll('dialog#' + $el.id).forEach((d) => {
            if (d !== $el) d.remove();
        });
        if ($el.parentElement !== document.body) {
            document.body.appendChild($el);
        }
    })"
    class="modal"
    style="max-width: 56rem; width: calc(100vw - 2rem); margin: auto; padding: 0; border: 0; background: transparent; overflow: visible;"
    onclick="if (event.target === this) this.close()"
>
    <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 sm:p-8 shadow-2xl max-h-[88vh] overflow-y-auto text-left">
        <div class="flex items-start justify-between gap-4 pb-5 border-b border-slate-100 dark:border-slate-800">
            <div class="min-w-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 mb-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Paket Aplikasi Sekolah
                </span>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-white">{{ $title }}</h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    Masa berlaku dihitung sejak pembayaran terverifikasi. Mendukung pembayaran otomatis &amp; dokumen SPJ BOS.
                </p>
            </div>

            <form method="dialog" class="shrink-0">
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 transition cursor-pointer"
                >
                    <span>Tutup</span>
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse ($plans as $plan)
                @php
                    $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                    $isUpgrade = $currentPlan && $plan->price_idr > $currentPlan->price_idr;
                @endphp

                <div class="relative flex flex-col justify-between rounded-2xl border p-5 transition-shadow hover:shadow-lg {{ $plan->is_featured ? 'border-amber-400 bg-amber-50/40 dark:border-amber-500/60 dark:bg-amber-950/20 ring-1 ring-amber-400/50' : 'border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/60' }}">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">{{ $plan->label }}</h3>
                            @if ($plan->badge)
                                <span class="shrink-0 rounded-full bg-amber-500 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide text-slate-950">
                                    {{ $plan->badge }}
                                </span>
                            @endif
                        </div>

                        <p class="mt-3 text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $plan->formatPrice() }}</p>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $plan->duration_label ?? $plan->duration_months.' bulan' }}</p>

                        @if ($plan->description)
                            <p class="mt-2.5 text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $plan->description }}</p>
                        @endif

                        <ul class="mt-4 space-y-2 text-xs text-slate-600 dark:text-slate-300 border-t border-slate-200/70 dark:border-slate-700/70 pt-3.5">
                            @foreach (array_slice($plan->features ?? [], 0, 5) as $feature)
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-black shrink-0" aria-hidden="true">✓</span>
                                    <span class="dark:text-slate-200 leading-snug">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-6 pt-2">
                        @if ($isCurrent)
                            <span class="block w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 py-2.5 px-4 text-center text-xs font-bold text-slate-500 dark:text-slate-400">
                                Paket Saat Ini
                            </span>
                        @elseif ($plan->is_featured)
                            <a
                                href="{{ route('checkout.show', $plan->code) }}"
                                class="block w-full rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-black py-2.5 px-4 text-center text-xs shadow-md transition active:scale-95"
                            >
                                {{ $isUpgrade ? 'Upgrade ke' : 'Pilih' }} {{ $plan->label }} &rarr;
                            </a>
                        @else
                            <a
                                href="{{ route('checkout.show', $plan->code) }}"
                                class="block w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-4 text-center text-xs shadow-md transition active:scale-95"
                            >
                                {{ $isUpgrade ? 'Upgrade ke' : 'Pilih' }} {{ $plan->label }} &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500 md:col-span-3 dark:text-slate-300 py-4 text-center">Paket belum tersedia.</p>
            @endforelse
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400">
            <span>Seluruh paket sudah termasuk akses SSO 1-Klik ke server CBT Online &amp; E-Raport.</span>
            <a href="{{ route('harga') }}" class="font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline shrink-0">
                Lihat perbandingan lengkap &rarr;
            </a>
        </div>
    </div>
</dialog>
