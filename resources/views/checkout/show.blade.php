<x-marketing-layout
    title="Checkout — {{ $plan->label }}"
    description="Checkout langganan {{ $plan->label }} — {{ $plan->formatPrice() }}."
>
    <section class="section container-site">
        <div class="grid gap-8 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <p class="eyebrow">Checkout</p>
                <h1 class="section-title">Data sekolah &amp; paket</h1>
                <p class="section-lead">
                    Harga final sama dengan halaman Harga. Pembayaran diproses Midtrans ({{ config('midtrans.is_production') ? 'produksi' : 'sandbox' }}).
                </p>

                 <form method="POST" action="{{ route('checkout.store') }}" class="card mt-6 sm:mt-8 p-5 sm:p-8 dark:bg-slate-850 dark:border-slate-700/50">
                    @csrf
                    <input type="hidden" name="plan_code" value="{{ $plan->code }}">

                    @if ($errors->has('snap'))
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('snap') }}
                        </div>
                    @endif

                    <fieldset class="mb-7">
                         <legend class="text-sm font-bold uppercase tracking-wider text-ink dark:text-slate-100">Paket dipilih</legend>
                         <div class="mt-3 rounded-xl border border-brand-100 dark:border-slate-700/50 bg-brand-50/50 dark:bg-slate-800/50 p-4">
                             <p class="font-bold text-ink dark:text-slate-100">{{ $plan->label }}</p>
                             <p class="mt-0.5 text-sm text-muted dark:text-slate-500">{{ $plan->description }}</p>
                             <p class="mt-2 text-2xl font-extrabold text-ink dark:text-slate-100">{{ $plan->formatPrice() }}</p>
                             <p class="text-sm text-brand-700 dark:text-brand-400">Masa aktif {{ $plan->duration_label }}</p>
                        </div>
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="school_name" class="block text-sm font-medium text-ink">Nama sekolah</label>
                            <input id="school_name" name="school_name" type="text" required autofocus
                             value="{{ old('school_name', $school?->name) }}"
                                 class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 text-ink dark:text-slate-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800"
                                placeholder="SMA Negeri 1 Contoh">
                            @error('school_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                             <label for="school_email" class="block text-sm font-medium text-ink dark:text-slate-200">Email sekolah</label>
                            <input id="school_email" name="school_email" type="email" required
                                value="{{ old('school_email', $school?->email ?? auth()->user()?->email) }}"
                                class="mt-1 block w-full rounded-xl border-brand-200 text-ink shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('school_email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                             <label for="school_phone" class="block text-sm font-medium text-ink dark:text-slate-200">Nomor WhatsApp</label>
                            <input id="school_phone" name="school_phone" type="tel" required
                                value="{{ old('school_phone', $school?->phone ?? auth()->user()?->phone) }}"
                                 class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 text-ink dark:text-slate-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800"
                                 placeholder="08xxxxxxxxxx">
                            @error('school_phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                             <label for="city" class="block text-sm font-medium text-ink dark:text-slate-200">Kota <span class="font-normal text-muted">(opsional)</span></label>
                             <input id="city" name="city" type="text"
                                 value="{{ old('city', $school?->city) }}"
                                 class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 text-ink dark:text-slate-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800">
                         </div>
                         <div>
                             <label for="npsn" class="block text-sm font-medium text-ink dark:text-slate-200">NPSN <span class="font-normal text-muted">(opsional)</span></label>
                             <input id="npsn" name="npsn" type="text"
                                 value="{{ old('npsn', $school?->npsn) }}"
                                 class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 text-ink dark:text-slate-200 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800">
                        </div>
                    </div>

                    @if (($availableReferralBalance ?? 0) > 0)
                        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-800/50 dark:bg-emerald-950/30">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" name="use_referral_balance" value="1" checked class="mt-1 rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm">
                                    <span class="font-bold text-emerald-900 dark:text-emerald-200">Gunakan Saldo Komisi Referral (Rp {{ number_format($availableReferralBalance, 0, ',', '.') }})</span>
                                    <span class="mt-0.5 block text-xs text-emerald-700 dark:text-emerald-300">
                                        Potong langsung tagihan paket ini dengan saldo komisi kemitraan sekolah Anda.
                                    </span>
                                </span>
                            </label>
                        </div>
                    @endif

                    <fieldset class="mt-6 mb-7">
                        <legend class="text-sm font-bold uppercase tracking-wider text-ink dark:text-slate-100">Metode Pembayaran</legend>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2" x-data="{ selectedMethod: 'midtrans' }">
                            <label class="relative flex cursor-pointer flex-col rounded-xl border p-4 shadow-sm transition"
                                :class="selectedMethod === 'midtrans' ? 'border-brand-500 bg-brand-50/40 dark:bg-slate-800 dark:border-brand-400 ring-2 ring-brand-500' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-850 hover:border-brand-200'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="payment_method" value="midtrans" x-model="selectedMethod" class="text-brand-600 focus:ring-brand-500">
                                        <span class="font-bold text-ink dark:text-slate-100">Otomatis / Midtrans</span>
                                    </div>
                                    <span class="rounded bg-brand-100 px-2 py-0.5 text-[10px] font-bold text-brand-800 dark:bg-brand-900/50 dark:text-brand-300">Instan</span>
                                </div>
                                <p class="mt-2 text-xs text-muted dark:text-slate-400">
                                    QRIS (Gopay/ShopeePay), Virtual Account Bank (BCA, Mandiri, BNI, BRI), Kartu Kredit.
                                </p>
                            </label>

                            <label class="relative flex cursor-pointer flex-col rounded-xl border p-4 shadow-sm transition"
                                :class="selectedMethod === 'manual_transfer' ? 'border-brand-500 bg-brand-50/40 dark:bg-slate-800 dark:border-brand-400 ring-2 ring-brand-500' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-850 hover:border-brand-200'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="payment_method" value="manual_transfer" x-model="selectedMethod" class="text-brand-600 focus:ring-brand-500">
                                        <span class="font-bold text-ink dark:text-slate-100">Transfer Bank / BOS</span>
                                    </div>
                                    <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Giro & SPJ</span>
                                </div>
                                <p class="mt-2 text-xs text-muted dark:text-slate-400">
                                    Transfer antar-rekening lembaga (BSI, Mandiri, BPD). Verifikasi bukti manual via upload slip.
                                </p>
                            </label>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn-primary mt-4 w-full sm:w-auto sm:px-8 sm:py-3.5">
                        Lanjut ke pembayaran
                    </button>
                    <p class="mt-3 text-xs text-muted">
                        Faktur &amp; Proforma Kwitansi resmi SPJ BOS langsung tersedia di halaman status pesanan.
                    </p>
                </form>
            </div>

             <aside class="lg:col-span-2">
                 <div class="card sticky top-24 p-5 sm:p-7 dark:bg-slate-850 dark:border-slate-700/50">
                     <h2 class="text-base font-bold text-ink dark:text-slate-100">Ringkasan</h2>
                     <dl class="mt-4 space-y-3 text-sm">
                         <div class="flex justify-between gap-4">
                             <dt class="text-muted dark:text-slate-500">Paket</dt>
                             <dd class="text-right font-semibold text-ink dark:text-slate-100">{{ $plan->label }}</dd>
                         </div>
                         <div class="flex justify-between gap-4">
                             <dt class="text-muted dark:text-slate-500">Durasi</dt>
                             <dd class="text-right font-semibold text-ink dark:text-slate-100">{{ $plan->duration_label }}</dd>
                         </div>
                         <div class="flex justify-between gap-4 border-t border-brand-100 dark:border-slate-700/50 pt-3">
                             <dt class="text-muted dark:text-slate-500">Total</dt>
                             <dd class="text-right text-lg font-extrabold text-ink dark:text-slate-100">{{ $plan->formatPrice() }}</dd>
                         </div>
                     </dl>

                     <ul class="mt-5 space-y-2 text-sm text-muted dark:text-slate-500">
                         @foreach ($plan->features as $feature)
                             <li class="flex gap-2">
                                 <span class="text-brand-600 dark:text-brand-400" aria-hidden="true">✓</span>
                                 <span>{{ $feature }}</span>
                             </li>
                         @endforeach
                     </ul>

                     <div class="mt-6 rounded-xl bg-surface dark:bg-slate-800/50 p-4 text-xs text-muted dark:text-slate-500">
                         Kanal: QRIS · Virtual Account · e-wallet (Midtrans).
                         Status order dipantau setelah Anda menekan tombol bayar.
                     </div>

                     <button
                         type="button"
                         onclick="document.getElementById('planModal').showModal()"
                         class="mt-5 text-sm font-medium text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300"
                     >
                        ← Ganti paket
                    </button>
                </div>
            </aside>
        </div>
    </section>

    <x-plan-modal :plans="$plans" :current-plan="$currentPlan" title="Ganti paket" />
</x-marketing-layout>
