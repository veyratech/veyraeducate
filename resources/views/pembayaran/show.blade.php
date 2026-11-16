<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-ink">
            Pembayaran
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-3xl">
            <div class="card p-5 sm:p-8 dark:bg-slate-850 dark:border-slate-700/50">
                 <div class="flex flex-wrap items-start justify-between gap-4 dark:bg-slate-850 dark:border-slate-700/50">
                     <div>
                         <p class="text-sm font-medium uppercase tracking-wider text-muted dark:text-slate-500">Order</p>
                         <p class="mt-1 font-mono text-lg font-bold text-ink dark:text-slate-100">{{ $order->order_id }}</p>
                    </div>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider',
                        'bg-gold-100 text-gold-900' => $order->status->value === 'pending',
                        'bg-brand-100 text-brand-900' => $order->status->value === 'paid',
                        'bg-red-100 text-red-800' => in_array($order->status->value, ['failed', 'expired'], true),
                    ])>
                        {{ $order->status->label() }}
                    </span>
                </div>

                 <dl class="mt-6 space-y-3 border-t border-brand-100 dark:border-slate-700/50 pt-5 text-sm">
                     <div class="flex justify-between gap-4">
                         <dt class="text-muted dark:text-slate-500">Paket</dt>
                         <dd class="text-right font-semibold text-ink dark:text-slate-100">{{ $order->plan?->label }}</dd>
                     </div>
                     <div class="flex justify-between gap-4">
                         <dt class="text-muted dark:text-slate-500">Sekolah</dt>
                         <dd class="text-right font-semibold text-ink dark:text-slate-100">{{ $order->school?->name }}</dd>
                     </div>
                     <div class="flex justify-between gap-4">
                         <dt class="text-muted dark:text-slate-500">Jumlah</dt>
                         <dd class="text-right text-lg font-extrabold text-ink dark:text-slate-100">{{ $order->formatAmount() }}</dd>
                     </div>
                     @if ($order->paid_at)
                         <div class="flex justify-between gap-4">
                             <dt class="text-muted dark:text-slate-500">Dibayar pada</dt>
                             <dd class="text-right font-semibold text-ink dark:text-slate-100">{{ $order->paid_at->format('d M Y H:i') }}</dd>
                         </div>
                     @endif
                     @if ($order->subscription?->ends_at && $order->status->value === 'paid')
                         <div class="flex justify-between gap-4">
                             <dt class="text-muted dark:text-slate-500">Masa aktif sampai</dt>
                             <dd class="text-right font-semibold text-ink dark:text-slate-100">{{ $order->subscription->ends_at->format('d M Y') }}</dd>
                         </div>
                    @endif
                </dl>

                @if (in_array($order->status->value, ['pending', 'failed'], true))
                    <div x-data="{ showManualTransfer: {{ $order->isManualTransfer() ? 'true' : 'false' }} }" class="mt-8">
                        @if (! $order->isManualTransfer())
                            <div class="mb-4">
                                <button
                                    type="button"
                                    @click="showManualTransfer = !showManualTransfer"
                                    class="w-full rounded-xl border border-brand-200 dark:border-slate-700 bg-brand-50/70 dark:bg-slate-800 px-4 py-3 text-left text-xs font-bold text-brand-800 dark:text-brand-300 hover:bg-brand-100/70 transition flex items-center justify-between gap-2 cursor-pointer"
                                >
                                    <span>💳 Ingin bayar via Transfer Bank / Giro BOS Manual? Klik untuk lihat rekening &amp; unggah bukti transfer</span>
                                    <span x-text="showManualTransfer ? '▲ Tutup' : '▼ Buka'"></span>
                                </button>
                            </div>
                        @endif

                        <div x-show="showManualTransfer" x-cloak class="rounded-2xl border border-brand-200 bg-brand-50/60 p-5 sm:p-6 dark:border-slate-700 dark:bg-slate-800/60">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="font-bold text-ink dark:text-slate-100">Rekening Tujuan Transfer Resmi</h3>
                                        <p class="text-xs text-muted dark:text-slate-400">Gunakan rekening giro resmi di bawah ini untuk pencairan dana BOS sekolah:</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-800 dark:bg-brand-900/50 dark:text-brand-300">
                                    Manual / Giro BOS
                                </span>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm dark:border-slate-700 dark:bg-slate-850">
                                    <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">BANK SYARIAH INDONESIA (BSI)</p>
                                    <p class="mt-1 font-mono text-base font-extrabold text-ink dark:text-slate-100">7188 2938 12</p>
                                    <p class="text-[11px] text-muted dark:text-slate-400">a.n. PT Veyra Educate Indonesia</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm dark:border-slate-700 dark:bg-slate-850">
                                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400">BANK MANDIRI</p>
                                    <p class="mt-1 font-mono text-base font-extrabold text-ink dark:text-slate-100">142-00-998877-1</p>
                                    <p class="text-[11px] text-muted dark:text-slate-400">a.n. PT Veyra Educate Indonesia</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm dark:border-slate-700 dark:bg-slate-850">
                                    <p class="text-xs font-bold text-blue-800 dark:text-blue-300">BANK BRI</p>
                                    <p class="mt-1 font-mono text-base font-extrabold text-ink dark:text-slate-100">0341-01-002891-30-2</p>
                                    <p class="text-[11px] text-muted dark:text-slate-400">a.n. PT Veyra Educate Indonesia</p>
                                </div>
                            </div>

                            <div class="mt-4 rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-900 dark:bg-amber-950/40 dark:border-amber-800 dark:text-amber-200">
                                <strong>Penting:</strong> Masukkan nomor order <span class="font-mono font-bold">{{ $order->order_id }}</span> pada kolom berita transfer, dan transfer nominal tepat <strong class="font-mono">{{ $order->formatAmount() }}</strong>.
                            </div>

                            {{-- Status Upload Bukti Transfer --}}
                            <div class="mt-6 border-t border-brand-200 pt-5 dark:border-slate-700">
                                @if ($order->hasProof())
                                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 dark:border-emerald-800 dark:bg-emerald-950/40">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2">
                                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white text-xs">✓</span>
                                                <div>
                                                    <p class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Bukti Transfer Berhasil Diunggah</p>
                                                    <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                                        Status: <strong>Sedang diverifikasi oleh admin platform</strong>
                                                        @if($order->bank_sender) &bull; Bank: {{ $order->bank_sender }} @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ $order->proofUrl() }}" target="_blank" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition">
                                                Lihat Bukti
                                            </a>
                                        </div>
                                        @if ($order->notes)
                                            <p class="mt-2 text-xs italic text-emerald-800 dark:text-emerald-300">Catatan: "{{ $order->notes }}"</p>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-4" x-data="{ openUpload: {{ $order->hasProof() ? 'false' : 'true' }} }">
                                    @if ($order->hasProof())
                                        <button type="button" @click="openUpload = !openUpload" class="text-xs font-bold text-brand-600 hover:underline dark:text-brand-400">
                                            + Unggah ulang / perbarui bukti transfer
                                        </button>
                                    @endif

                                    <form x-show="openUpload" method="POST" action="{{ route('pembayaran.upload-proof', $order->order_id) }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                                        @csrf
                                        <div>
                                            <label for="proof" class="block text-xs font-bold text-ink dark:text-slate-200">
                                                Unggah Foto Bukti Transfer / Resi Giro (JPG, PNG, WEBP, atau PDF maks 5MB)
                                            </label>
                                            <input id="proof" name="proof" type="file" required accept="image/*,application/pdf"
                                                class="mt-1 block w-full text-xs text-slate-500 file:mr-3 file:rounded-xl file:border-0 file:bg-brand-600 file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-brand-500">
                                            @error('proof')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                        </div>

                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <label for="bank_sender" class="block text-xs font-medium text-ink dark:text-slate-200">Bank Pengirim / Rekening Asal</label>
                                                <input id="bank_sender" name="bank_sender" type="text" placeholder="Contoh: BPD Jatim / Mandiri"
                                                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-850 dark:text-slate-100">
                                            </div>
                                            <div>
                                                <label for="notes" class="block text-xs font-medium text-ink dark:text-slate-200">Catatan Tambahan (Opsional)</label>
                                                <input id="notes" name="notes" type="text" placeholder="Nomor referensi / keterangan BOS"
                                                    class="mt-1 block w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-850 dark:text-slate-100">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn-primary text-xs py-2 px-4">
                                            Kirim Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-7 flex flex-wrap items-center gap-3">
                    <x-plan-modal :plans="$plans" :current-plan="$currentPlan" title="Ganti paket" />

                    @if ($order->status->value === 'pending' && $order->snap_token)
                        <button
                            type="button"
                            id="btn-open-snap"
                            class="btn-primary"
                            data-snap-token="{{ $order->snap_token }}"
                            data-client-key="{{ config('midtrans.client_key') }}"
                            data-order-id="{{ $order->order_id }}"
                            data-callback="{{ route('pembayaran.show', $order->order_id) }}"
                        >
                            Bayar sekarang
                        </button>
                        <a
                            href="{{ route('pembayaran.invoice', $order->order_id) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-750 text-white px-4 py-2 text-sm font-bold shadow-sm transition dark:bg-slate-700 dark:hover:bg-slate-600"
                            style="color: #ffffff !important;"
                        >
                            <svg class="h-4 w-4 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-white" style="color: #ffffff !important;">Cetak Proforma Invoice</span>
                        </a>
                        <button type="button" onclick="document.getElementById('planModal').showModal()" class="btn-secondary">
                            Ganti paket
                        </button>
                    @elseif ($order->status->value === 'pending')
                        <a
                            href="{{ route('pembayaran.invoice', $order->order_id) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-750 text-white px-4 py-2 text-sm font-bold shadow-sm transition dark:bg-slate-700 dark:hover:bg-slate-600"
                            style="color: #ffffff !important;"
                        >
                            <svg class="h-4 w-4 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-white" style="color: #ffffff !important;">Cetak Proforma Invoice</span>
                        </a>
                        <a
                            href="{{ route('pembayaran.bast', $order->order_id) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-brand-700 hover:bg-brand-600 text-white px-4 py-2 text-sm font-bold shadow-sm transition"
                            style="color: #ffffff !important;"
                        >
                            <span class="text-white" style="color: #ffffff !important;">Draft BAST (SPJ BOS)</span>
                        </a>
                        <button type="button" onclick="document.getElementById('planModal').showModal()" class="btn-secondary">
                            Ganti paket
                        </button>
                    @else
                        @if ($order->status->value === 'paid')
                            <a
                                href="{{ route('pembayaran.invoice', $order->order_id) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition"
                                style="color: #ffffff !important;"
                            >
                                <svg class="h-4 w-4 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span class="text-white" style="color: #ffffff !important;">Cetak Kwitansi Resmi</span>
                            </a>
                            <a
                                href="{{ route('pembayaran.bast', $order->order_id) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-slate-700 transition"
                                style="color: #ffffff !important;"
                            >
                                <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-white" style="color: #ffffff !important;">Cetak BAST (SPJ BOS)</span>
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn-primary">Ke dashboard</a>
                        <button type="button" onclick="document.getElementById('planModal').showModal()" class="btn-secondary">
                            Lihat paket lain
                        </button>
                    @endif
                </div>

                @if ($order->status->value === 'pending')
                     <p class="mt-5 text-xs text-muted dark:text-slate-500">
                        @if ($order->isManualTransfer())
                            Transfer via Rekening Giro BOS diverifikasi maksimal 1x24 jam kerja setelah bukti transfer diunggah.
                        @else
                            Status diperbarui otomatis via notifikasi Midtrans (webhook). Jika pending lama, muat ulang halaman ini.
                        @endif
                    </p>
                @endif
            </div>

             <div class="card mt-6 p-5 text-sm text-muted sm:p-6 dark:bg-slate-850 dark:border-slate-700/50 dark:text-slate-500">
                 <p class="font-semibold text-ink dark:text-slate-100">Butuh bantuan bayar?</p>
                 <p class="mt-1">Sebutkan nomor order <span class="font-mono text-ink dark:text-slate-100">{{ $order->order_id }}</span> saat chat WhatsApp.</p>
                <a class="btn-secondary mt-4 inline-flex items-center justify-center w-full sm:w-auto" href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate%2C%20order%20{{ $order->order_id }}" target="_blank" rel="noopener noreferrer">
                    Chat WhatsApp
                </a>
            </div>
        </div>
    </div>

    @if ($order->status->value === 'pending' && $order->snap_token)
        @push('scripts')
            <script src="{{ config('midtrans.snap_js_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
            <script>
                document.getElementById('btn-open-snap')?.addEventListener('click', function () {
                    var token = this.dataset.snapToken;
                    if (!window.snap || !token) {
                        window.location.reload();
                        return;
                    }
                    window.snap.pay(token, {
                        onSuccess: function () {
                            window.location.reload();
                        },
                        onPending: function () {
                            window.location.reload();
                        },
                        onError: function () {
                            window.location.reload();
                        },
                        onClose: function () {
                            window.location.reload();
                        },
                    });
                });
            </script>
        @endpush
    @endif
</x-app-layout>
