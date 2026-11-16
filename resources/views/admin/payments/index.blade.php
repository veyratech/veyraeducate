<x-admin-layout title="Pembayaran — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Transaksi</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Pembayaran</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">
            {{-- Toolbar: Search & Status Filter --}}
            <div class="flex flex-col gap-3 stagger-in sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-2">
                    @php
                        $tabs = [
                            '' => 'Semua ('.$statusCounts['all'].')',
                            'proof' => '📎 Verifikasi Bukti ('.($statusCounts['proof'] ?? 0).')',
                            'pending' => 'Pending ('.$statusCounts['pending'].')',
                            'paid' => 'Lunas ('.$statusCounts['paid'].')',
                            'failed' => 'Gagal ('.$statusCounts['failed'].')',
                            'expired' => 'Kedaluwarsa ('.$statusCounts['expired'].')',
                        ];
                    @endphp
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('admin.payments.index', array_filter(['status' => $key, 'q' => $search ?? ''])) }}"
                           class="inline-flex items-center gap-1 rounded-lg px-3.5 py-1.5 text-xs font-semibold transition-colors
                          {{ $status === $key
                               ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900'
                               : 'border border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:text-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.payments.index') }}" class="flex items-center gap-2">
                    @if ($status !== '')
                        <input type="hidden" name="status" value="{{ $status }}" />
                    @endif
                    <div class="relative flex-1 sm:w-56">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <x-text-input class="form-input-minimal !pl-10 text-xs" type="search" name="q" :value="$search ?? ''" placeholder="Cari Order ID, sekolah…" />
                    </div>
                    <x-primary-button class="btn-minimal-primary text-xs whitespace-nowrap">Cari</x-primary-button>
                </form>

                <form method="GET" action="{{ route('admin.payments.export') }}" class="flex items-center gap-2 shrink-0">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input-minimal text-xs w-36" />
                    <span class="text-xs text-muted dark:text-slate-500">s/d</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input-minimal text-xs w-36" />
                    <x-primary-button class="btn-minimal-primary text-xs whitespace-nowrap">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </x-primary-button>
                </form>
            </div>

            {{-- Orders Table --}}
            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 60ms;">
                <div class="overflow-x-auto">
                    <table class="table-minimal table-responsive-card">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Order</th>
                                <th class="px-4 py-3">Sekolah</th>
                                <th class="px-4 py-3">Paket</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td data-label="Order" class="px-4 py-3 font-mono text-xs font-semibold text-ink dark:text-slate-200">{{ $order->order_id }}</td>
                                    <td data-label="Sekolah" class="px-4 py-3">
                                        <p class="font-semibold text-ink dark:text-slate-100">{{ $order->school?->name ?? '—' }}</p>
                                        <p class="text-xs text-muted dark:text-slate-500">{{ $order->user?->email }}</p>
                                    </td>
                                    <td data-label="Paket" class="px-4 py-3 text-muted dark:text-slate-500">{{ $order->plan?->label ?? '—' }}</td>
                                    <td data-label="Jumlah" class="px-4 py-3 font-semibold text-ink dark:text-slate-200">{{ $order->formatAmount() }}</td>
                                    <td data-label="Status" class="px-4 py-3">
                                        <div class="flex flex-col items-start gap-1">
                                            @if ($order->hasProof() && $order->status->value === 'pending')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-900 dark:bg-amber-900/50 dark:text-amber-200">
                                                    <span>📎</span> Verifikasi Bukti
                                                </span>
                                            @else
                                                <span class="badge-minimal
                                                    {{ match ($order->status->value) {
                                                        'paid' => 'badge-minimal-active',
                                                        'pending' => 'badge-minimal-pending',
                                                        default => 'badge-minimal-inactive',
                                                    } }}">{{ $order->status->label() }}</span>
                                            @endif
                                            <span class="text-[10px] font-medium text-slate-500">
                                                {{ $order->isManualTransfer() ? 'Transfer BOS' : 'Midtrans' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Aksi" class="px-4 py-3">
                                        <a href="{{ route('admin.payments.show', $order) }}" class="btn-minimal-ghost text-xs px-3 py-1.5">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="empty-minimal py-4">
                                            <div class="empty-minimal-icon">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <p class="empty-minimal-title">Tidak ada order.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-slate-200 dark:border-slate-800 px-4 py-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
