<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg sm:text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span>Riwayat Tagihan &amp; Kwitansi</span>
                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 px-2.5 py-0.5 text-xs font-bold border border-emerald-200/60">
                        Arsip BOS
                    </span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Arsip resmi bukti pembayaran lisensi sekolah &amp; kwitansi ber-KOP untuk pelaporan SPJ BOS / Yayasan
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="document.getElementById('planModal')?.showModal()"
                    class="btn-primary text-xs !text-white flex items-center gap-1.5 cursor-pointer shadow-sm"
                    style="color: #ffffff !important;"
                >
                    <svg class="h-4 w-4 !text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="text-white" style="color: #ffffff !important;">Perpanjang / Ganti Paket</span>
                </button>
            </div>
        </div>
    </x-slot>

    {{-- Plan Selection Modal --}}
    <x-plan-modal :plans="$plans" :current-plan="$currentPlan" title="Pilihan Paket Langganan Sekolah" />

    <div class="space-y-6">

        {{-- Stat Summary Cards (4 Cards) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            
            {{-- Card 1: Total Investasi Lisensi --}}
            <div class="bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">
                        Total Biaya Lisensi
                    </span>
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-lg sm:text-2xl font-black text-slate-900 dark:text-slate-100">
                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                </p>
                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1">
                    Total transaksi terbayar lunas
                </p>
            </div>

            {{-- Card 2: Kwitansi Sah / Lunas --}}
            <div class="bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">
                        Kwitansi Sah
                    </span>
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-lg sm:text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $paidCount }}</span>
                    <span class="text-xs text-slate-400 font-medium">dokumen</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1">
                    Siap dicetak untuk LPJ
                </p>
            </div>

            {{-- Card 3: Menunggu Pembayaran --}}
            <div class="bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">
                        Menunggu Bayar
                    </span>
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-lg sm:text-2xl font-black text-amber-600 dark:text-amber-400">{{ $pendingCount }}</span>
                    <span class="text-xs text-slate-400 font-medium">tagihan</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1">
                    {{ $pendingCount > 0 ? 'Perlu diselesaikan' : 'Semua tagihan lunas' }}
                </p>
            </div>

            {{-- Card 4: Paket Aktif Saat Ini --}}
            <div class="bg-white dark:bg-slate-850 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">
                        Paket Sekolah
                    </span>
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <p class="text-base sm:text-lg font-black text-slate-900 dark:text-slate-100 truncate">
                    {{ $activeSubscription?->plan?->label ?? 'Belum Aktif' }}
                </p>
                <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1 truncate">
                    @if ($activeSubscription?->ends_at)
                        Berakhir: {{ $activeSubscription->ends_at->format('d M Y') }}
                    @else
                        Silakan pilih paket langganan
                    @endif
                </p>
            </div>

        </div>

        {{-- Filter Tabs & Main Table Card --}}
        <div class="bg-white dark:bg-slate-850 rounded-[2rem] p-5 sm:p-6 shadow-sm border border-emerald-50/60 dark:border-slate-800 space-y-5">
            
            {{-- Header Row: Filters & Info --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                
                {{-- Status Filter Pills --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                    <a
                        href="{{ route('pembayaran.history') }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shrink-0 {{ empty($currentStatus) ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Semua ({{ $orders->total() }})
                    </a>
                    <a
                        href="{{ route('pembayaran.history', ['status' => 'paid']) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shrink-0 {{ $currentStatus === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Lunas ({{ $paidCount }})
                    </a>
                    <a
                        href="{{ route('pembayaran.history', ['status' => 'pending']) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition shrink-0 {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}"
                    >
                        Menunggu ({{ $pendingCount }})
                    </a>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>Format cetak standar A4 KOP resmi</span>
                </div>
            </div>

            {{-- Table View (Desktop & Tablet) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3 px-3">Order ID &amp; Waktu</th>
                            <th class="py-3 px-3">Paket Langganan</th>
                            <th class="py-3 px-3">Nominal Tagihan</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Metode Bayar</th>
                            <th class="py-3 px-3 text-right">Dokumen Sah / Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors">
                                {{-- Order ID & Date --}}
                                <td class="py-3.5 px-3">
                                    <span class="font-mono font-bold text-slate-900 dark:text-slate-100 text-xs block">
                                        {{ $order->order_id }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </span>
                                </td>

                                {{-- Plan --}}
                                <td class="py-3.5 px-3">
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">
                                        {{ $order->plan?->label ?? 'Paket Sekolah' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">
                                        {{ $order->plan?->duration_days ? $order->plan->duration_days . ' hari aktif' : 'Lisensi cloud' }}
                                    </span>
                                </td>

                                {{-- Amount --}}
                                <td class="py-3.5 px-3">
                                    <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm block">
                                        {{ $order->formatAmount() }}
                                    </span>
                                    @if ($order->paid_at)
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold block mt-0.5">
                                            Lunas: {{ $order->paid_at->format('d M Y') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="py-3.5 px-3">
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold',
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' => $order->status->value === 'paid',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300' => $order->status->value === 'pending',
                                        'bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300' => in_array($order->status->value, ['failed', 'expired'], true),
                                    ])>
                                        @if ($order->status->value === 'paid')
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        @elseif ($order->status->value === 'pending')
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        @endif
                                        <span>{{ $order->status->label() }}</span>
                                    </span>
                                </td>

                                {{-- Payment Method --}}
                                <td class="py-3.5 px-3">
                                    <span class="text-slate-600 dark:text-slate-300 text-xs font-medium block">
                                        {{ $order->payment_method ? strtoupper($order->payment_method) : 'Midtrans' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">
                                        Otomatis Webhook
                                    </span>
                                </td>

                                {{-- Action Buttons --}}
                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($order->status->value === 'paid')
                                            <a
                                                href="{{ route('pembayaran.invoice', $order->order_id) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition active:scale-95"
                                                style="color: #ffffff !important;"
                                                title="Cetak Kwitansi Resmi Format PDF untuk LPJ BOS"
                                            >
                                                <svg class="h-3.5 w-3.5 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span class="text-white" style="color: #ffffff !important;">Cetak Kwitansi (PDF)</span>
                                            </a>
                                        @elseif ($order->status->value === 'pending')
                                            <a
                                                href="{{ route('pembayaran.show', $order->order_id) }}"
                                                class="inline-flex items-center gap-1 rounded-xl bg-amber-500 hover:bg-amber-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition active:scale-95"
                                                style="color: #ffffff !important;"
                                            >
                                                <span class="text-white" style="color: #ffffff !important;">Bayar Sekarang</span>
                                                <svg class="h-3.5 w-3.5 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </a>
                                            <a
                                                href="{{ route('pembayaran.invoice', $order->order_id) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition"
                                                title="Cetak Invoice Tagihan Sementara"
                                            >
                                                <span>Invoice</span>
                                            </a>
                                        @else
                                            <a
                                                href="{{ route('pembayaran.show', $order->order_id) }}"
                                                class="inline-flex items-center gap-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition"
                                            >
                                                <span>Detail</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mb-3">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Belum ada riwayat tagihan</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Semua transaksi langganan dan kwitansi resmi sekolah Anda akan tercatat rapi di sini.
                                    </p>
                                    <div class="mt-4">
                                        <button
                                            type="button"
                                            onclick="document.getElementById('planModal')?.showModal()"
                                            class="btn-primary text-xs !text-white"
                                            style="color: #ffffff !important;"
                                        >
                                            <span class="text-white" style="color: #ffffff !important;">Pilih Paket Sekarang</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View (Hidden on Desktop) --}}
            <div class="md:hidden space-y-3">
                @forelse ($orders as $order)
                    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/50 p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono font-bold text-slate-900 dark:text-slate-100 text-xs block">
                                    {{ $order->order_id }}
                                </span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider',
                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' => $order->status->value === 'paid',
                                'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300' => $order->status->value === 'pending',
                                'bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300' => in_array($order->status->value, ['failed', 'expired'], true),
                            ])>
                                {{ $order->status->label() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Paket:</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $order->plan?->label ?? 'Paket' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Total Biaya:</span>
                                <span class="font-extrabold text-slate-900 dark:text-slate-100">{{ $order->formatAmount() }}</span>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $order->payment_method ? strtoupper($order->payment_method) : 'Midtrans' }}
                            </span>

                            <div class="flex items-center gap-2">
                                @if ($order->status->value === 'paid')
                                    <a
                                        href="{{ route('pembayaran.invoice', $order->order_id) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition"
                                        style="color: #ffffff !important;"
                                    >
                                        <svg class="h-3.5 w-3.5 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span class="text-white" style="color: #ffffff !important;">Cetak Kwitansi</span>
                                    </a>
                                @elseif ($order->status->value === 'pending')
                                    <a
                                        href="{{ route('pembayaran.show', $order->order_id) }}"
                                        class="inline-flex items-center gap-1 rounded-xl bg-amber-500 hover:bg-amber-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition"
                                        style="color: #ffffff !important;"
                                    >
                                        <span class="text-white" style="color: #ffffff !important;">Bayar</span>
                                    </a>
                                @else
                                    <a
                                        href="{{ route('pembayaran.show', $order->order_id) }}"
                                        class="inline-flex items-center gap-1 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200"
                                    >
                                        <span>Detail</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-4">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum ada riwayat tagihan</p>
                        <p class="text-[11px] text-slate-400 mt-1">Semua transaksi akan tercatat otomatis di sini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if ($orders->hasPages())
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>

        {{-- Legal / BOS Information Banner for Treasurers --}}
        <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-sky-50 dark:from-slate-850 dark:via-slate-850 dark:to-slate-800 rounded-3xl p-5 sm:p-6 border border-emerald-100 dark:border-slate-750 shadow-sm transition">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white text-xs font-black shadow-sm">✓</span>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                            Keabsahan Dokumen untuk SPJ / LPJ Dana BOS &amp; Yayasan
                        </h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 max-w-3xl leading-relaxed">
                        Setiap lembar kwitansi yang dicetak ber-KOP resmi <span class="font-bold text-slate-800 dark:text-slate-100">PT. VEYRA CREATIVE TECH</span>, dilengkapi Nomor Transaksi Unik, rincian paket, kotak Terbilang Rupiah, Stempel Lunas, serta <span class="font-bold text-slate-800 dark:text-slate-100">QR Code Verifikasi Dokumen Digital</span> yang dapat divalidasi langsung oleh Auditor BPK / Inspektorat / Dinas Pendidikan.
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a
                        href="https://wa.me/6285707074778?text=Halo%20Veyra%20Creative%20Tech,%20saya%20bendahara%20sekolah%20{{ urlencode($school?->name ?? 'saya') }}%20ingin%20konsultasi%20faktur%20pembayaran"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-semibold px-4 py-2 text-xs transition hover:bg-slate-50 dark:hover:bg-slate-700 inline-flex items-center gap-1.5"
                    >
                        <svg class="h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.149.929 3.182 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.768-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.074-2.127-.518-1.782-.738-2.919-2.56-3.007-2.678-.088-.118-.716-.953-.716-1.815 0-.862.451-1.286.613-1.46.162-.174.354-.218.472-.218.118 0 .236.001.339.006.109.005.253-.042.395.3.147.354.502 1.224.546 1.313.044.089.074.192.015.31-.059.118-.089.192-.177.295-.088.104-.186.232-.266.311-.088.089-.18.186-.077.363.103.177.458.756.983 1.224.675.602 1.244.788 1.421.877.177.089.28-.074.383-.192.103-.118.442-.516.56-.693.118-.177.236-.148.398-.089.162.059 1.026.484 1.203.573.177.089.295.133.339.207.044.074.044.43-.1 1.007z"/>
                        </svg>
                        <span>Bantuan Bendahara</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
