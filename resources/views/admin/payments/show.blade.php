<x-admin-layout title="{{ $order->order_id }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Detail order</p>
                <h2 class="mt-1 font-mono text-xl font-bold leading-tight text-ink dark:text-slate-100">{{ $order->order_id }}</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400"><a href="{{ route('admin.payments.index') }}" class="link-muted">← Semua pembayaran</a></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-3xl space-y-6">
            <div class="card-minimal p-6 stagger-in">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="stat-label">Status</p>
                        <span class="badge-minimal mt-1 {{ match ($order->status->value) {
                            'paid' => 'badge-minimal-active',
                            'pending' => 'badge-minimal-pending',
                            default => 'badge-minimal-inactive',
                        } }}">{{ $order->status->label() }}</span>
                        <p class="mt-3 text-3xl font-extrabold text-ink dark:text-slate-100">{{ $order->formatAmount() }}</p>
                        <p class="mt-1 text-sm text-muted dark:text-slate-500">{{ $order->plan?->label ?? '—' }} · {{ $order->payment_method ?: '—' }}</p>
                        @php
                            $refDiscount = (int) ($order->payload['referral_discount_idr'] ?? 0);
                            $origAmount = (int) ($order->payload['original_amount_idr'] ?? $order->plan?->price_idr ?? 0);
                        @endphp
                        @if ($refDiscount > 0)
                            <p class="mt-1.5 inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 text-xs font-semibold text-emerald-800 dark:text-emerald-300">
                                <span>🎁</span> Harga Normal Rp {{ number_format($origAmount, 0, ',', '.') }} — Potongan Saldo Referral Rp {{ number_format($refDiscount, 0, ',', '.') }}
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a
                            href="{{ route('pembayaran.invoice', $order->order_id) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition"
                            style="color: #ffffff !important;"
                        >
                            <svg class="h-4 w-4 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span class="text-white" style="color: #ffffff !important;">Cetak Kwitansi / Invoice</span>
                        </a>

                        @if ($order->status->value === 'paid')
                            <a
                                href="{{ route('pembayaran.bast', $order->order_id) }}"
                                target="_blank"
                                class="btn-minimal-secondary text-xs py-2 px-3"
                            >
                                📄 Cetak BAST SPJ BOS
                            </a>
                        @endif

                        @if ($order->status->value !== 'paid')
                            <form method="POST" action="{{ route('admin.payments.mark-paid', $order) }}">
                                @csrf
                                <x-primary-button class="btn-minimal-primary" onclick="return confirm('Tandai order ini lunas dan aktifkan langganan?')">
                                    Tandai lunas
                                </x-primary-button>
                            </form>
                        @endif
                    </div>
                </div>

                <dl class="mt-6 grid gap-4 border-t border-slate-200 dark:border-slate-800 pt-5 sm:grid-cols-2 stagger-in" style="animation-delay: 60ms;">
                    <div>
                        <dt class="stat-label">Sekolah</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink dark:text-slate-100">{{ $order->school?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="stat-label">User</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink dark:text-slate-100">{{ $order->user?->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="stat-label">Dibuat</dt>
                        <dd class="mt-1 text-sm text-ink dark:text-slate-200">{{ optional($order->created_at)->format('d M Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="stat-label">Dibayar</dt>
                        <dd class="mt-1 text-sm text-ink dark:text-slate-200">{{ optional($order->paid_at)->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="stat-label">Kedaluwarsa</dt>
                        <dd class="mt-1 text-sm text-ink dark:text-slate-200">{{ optional($order->expired_at)->format('d M Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="stat-label">Transaction ID</dt>
                        <dd class="mt-1 font-mono text-sm text-ink dark:text-slate-200">{{ $order->transaction_id ?: '—' }}</dd>
                    </div>
                </dl>
            </div>

            @if ($order->hasProof())
                <div class="card-minimal p-6 border-brand-300 dark:border-brand-500/30 bg-brand-50/30 dark:bg-slate-800/50 stagger-in" style="animation-delay: 90ms;">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white font-bold text-xs">📎</span>
                            <div>
                                <h3 class="text-sm font-bold text-ink dark:text-slate-100">Bukti Transfer Bank Sekolah</h3>
                                <p class="text-xs text-muted dark:text-slate-400">
                                    Bank Pengirim: <strong>{{ $order->bank_sender ?: 'Tidak disebutkan' }}</strong>
                                    @if($order->notes) &bull; Catatan: "{{ $order->notes }}" @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ $order->proofUrl() }}" target="_blank" class="btn-minimal-primary text-xs py-1.5 px-3">
                            Buka Berkas Penuh ↗
                        </a>
                    </div>
                    @if (Str::endsWith(strtolower($order->proof_path), ['.jpg', '.jpeg', '.png', '.webp']))
                        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 max-h-96">
                            <img src="{{ $order->proofUrl() }}" alt="Bukti Transfer" class="w-full object-contain max-h-96">
                        </div>
                    @else
                        <div class="mt-3 p-4 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-300">
                            Berkas dokumen PDF dilampirkan: <a href="{{ $order->proofUrl() }}" target="_blank" class="text-brand-600 dark:text-brand-400 font-bold underline">Klik untuk membuka dokumen PDF</a>
                        </div>
                    @endif
                </div>
            @endif

            @if ($order->subscription)
                <div class="card-minimal p-6 stagger-in" style="animation-delay: 120ms;">
                    <h3 class="text-sm font-bold text-ink dark:text-slate-100">Langganan terkait</h3>
                    <p class="mt-2 text-sm text-muted dark:text-slate-400">
                        {{ $order->subscription->plan?->label }} ·
                        {{ $order->subscription->status->label() }} ·
                        s/d {{ optional($order->subscription->ends_at)->format('d M Y') ?? '—' }}
                    </p>
                    @if ($order->subscription->school)
                        <a href="{{ route('admin.schools.show', $order->subscription->school) }}" class="link-sm mt-2 inline-block">
                            Lihat sekolah →
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
