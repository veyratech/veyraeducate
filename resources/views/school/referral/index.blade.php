<x-app-layout>
    <div
        x-data="{
            copied: false,
            copyLink() {
                navigator.clipboard.writeText('{{ $referralLink }}');
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            },
            showClaimModal: false,
            claimType: 'discount_voucher'
        }"
        class="space-y-6"
    >
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-900 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white font-bold">✓</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-900 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white font-bold">!</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Hero Referral Banner with Dynamic School Theme --}}
        <div
            :class="'bg-gradient-to-r ' + (themeConfig[schoolTheme]?.hero || themeConfig.teal.hero)"
            class="relative overflow-hidden rounded-[2rem] p-6 sm:p-8 text-white shadow-xl shadow-black/10 transition-all duration-300"
        >
            <div class="pointer-events-none absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-10 h-44 w-44 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>

            <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-white/20 backdrop-blur-md border border-white/20 text-white">
                        <span>🎁</span> Program Kemitraan &amp; Afiliasi Operator Sekolah
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Ajak Rekan Sekolah, Dapatkan Reward Komisi 10%
                    </h1>
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed">
                        Bantu sekolah rekanan Anda di MGMP, KKG, maupun jaringan satuan pendidikan menikmati kemudahan ujian digital bebas server lokal. Setiap sekolah yang berlangganan melalui kode Anda memberikan keuntungan komisi langsung atau potongan perpanjangan BOS.
                    </p>
                </div>

                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/20 flex flex-col gap-3 min-w-[280px]">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-bold text-white/80 uppercase tracking-wider">Saldo Siap Klaim</span>
                        @if (($claimedAmount ?? 0) > 0)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/20 text-white/90">
                                Diklaim: Rp {{ number_format($claimedAmount, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            Rp {{ number_format($availableBalance ?? $totalEarnings, 0, ',', '.') }}
                        </span>
                    </div>
                    <button
                        type="button"
                        @click="showClaimModal = true"
                        class="w-full py-2.5 px-4 rounded-xl bg-white text-slate-800 font-bold text-xs sm:text-sm shadow-md hover:bg-slate-50 transition cursor-pointer flex items-center justify-center gap-2"
                    >
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Klaim Saldo Reward</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Share & Link Box --}}
        <div class="rounded-[2rem] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                <span>🔗</span> Tautan &amp; Kode Referral Sekolah Anda
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Tautan Registrasi Khusus:</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            readonly
                            value="{{ $referralLink }}"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-800 focus:outline-none dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200 select-all"
                        >
                        <button
                            type="button"
                            @click="copyLink()"
                            class="shrink-0 px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition flex items-center gap-1.5 cursor-pointer dark:bg-brand-600 dark:hover:bg-brand-700"
                        >
                            <span x-text="copied ? 'Tersalin! ✓' : 'Salin'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-500">Kode unik Anda: <strong class="font-mono text-brand-600 font-extrabold">{{ $referralCode }}</strong>. Setiap sekolah baru yang membuka link ini akan otomatis terdata di bawah sekolah Anda.</p>
                </div>

                {{-- Quick WhatsApp Share --}}
                <div class="flex flex-col gap-2 justify-center">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Bagikan Langsung:</label>
                    <a
                        href="{{ $waShareUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.699c.971.53 1.838.814 2.8.814 3.18 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.768-5.772-5.768zm7.399 5.767c-.001 4.08-3.32 7.397-7.399 7.397-1.264 0-2.457-.323-3.5-.929l-4.531 1.189 1.21-4.417c-.675-1.085-1.037-2.339-1.036-3.64.001-4.08 3.32-7.397 7.4-7.397 4.08.001 7.399 3.32 7.399 7.397z"/>
                        </svg>
                        <span>Kirim Rekomendasi ke WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 3 KPI Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 font-bold text-xl dark:bg-blue-900/30 dark:text-blue-400">
                    🏫
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500">Sekolah Terdaftar</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalInvited }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 font-bold text-xl dark:bg-emerald-900/30 dark:text-emerald-400">
                    ⚡
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500">Langganan Aktif</span>
                    <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalSubscribed }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 font-bold text-xl dark:bg-amber-900/30 dark:text-amber-400">
                    💰
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500">Total Akumulasi Komisi</span>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($totalEarnings, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Table of Referred Schools --}}
        <div class="rounded-[2rem] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Sekolah Mitra yang Bergabung</h3>
                    <p class="text-xs text-slate-500">Riwayat sekolah yang mendaftar menggunakan link referensi Anda</p>
                </div>
                <span class="text-xs font-bold text-brand-600 bg-brand-50 dark:bg-brand-900/30 px-3 py-1 rounded-full w-fit">
                    {{ $totalInvited }} Satuan Pendidikan
                </span>
            </div>

            @if ($referredSchools->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center dark:border-slate-800">
                    <span class="text-3xl block mb-2">🤝</span>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum ada sekolah yang bergabung</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-4">
                        Salin tautan di atas dan bagikan ke grup WhatsApp operator sekolah atau rekan guru untuk mulai mengumpulkan reward.
                    </p>
                    <button
                        type="button"
                        @click="copyLink()"
                        class="px-4 py-2 rounded-xl bg-brand-600 text-white font-bold text-xs hover:bg-brand-700 transition cursor-pointer"
                    >
                        Salin Tautan Sekarang
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-bold dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-300">
                                <th class="px-3.5 py-2.5 rounded-l-xl">Nama Sekolah</th>
                                <th class="px-3.5 py-2.5">Jenjang / Kota</th>
                                <th class="px-3.5 py-2.5">Tanggal Bergabung</th>
                                <th class="px-3.5 py-2.5">Status Langganan</th>
                                <th class="px-3.5 py-2.5 rounded-r-xl text-right">Potensi Reward</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($referredSchools as $refSchool)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-3.5 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $refSchool->name }}
                                        @if ($refSchool->npsn)
                                            <span class="block text-[10px] font-mono text-slate-400">NPSN: {{ $refSchool->npsn }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                                        {{ $refSchool->meta['level'] ?? 'Sekolah' }} • {{ $refSchool->city ?? 'Indonesia' }}
                                    </td>
                                    <td class="px-3.5 py-3 text-slate-500 font-mono">
                                        {{ $refSchool->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-3.5 py-3">
                                        @if ($refSchool->hasActiveSubscription())
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                ● Berlangganan Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                Uji Coba / Belum Bayar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                        @if ($refSchool->hasActiveSubscription())
                                            +10% Komisi Lunas
                                        @else
                                            Menunggu Checkout
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Claim History Table --}}
        @if (!empty($claimHistory))
            <div class="rounded-[2rem] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Pengajuan Klaim Komisi</h3>
                        <p class="text-xs text-slate-500">Daftar pencairan dana atau penukaran voucher potongan tagihan sekolah</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-300 px-3 py-1 rounded-full w-fit">
                        {{ count($claimHistory) }} Pengajuan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-bold dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-300">
                                <th class="px-3.5 py-2.5 rounded-l-xl">ID Klaim</th>
                                <th class="px-3.5 py-2.5">Tanggal</th>
                                <th class="px-3.5 py-2.5">Jenis Pencairan</th>
                                <th class="px-3.5 py-2.5">Tujuan / Rekening</th>
                                <th class="px-3.5 py-2.5">Nominal</th>
                                <th class="px-3.5 py-2.5 rounded-r-xl text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($claimHistory as $claim)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-3.5 py-3 font-mono font-bold text-slate-900 dark:text-white">
                                        {{ $claim['id'] ?? '-' }}
                                    </td>
                                    <td class="px-3.5 py-3 text-slate-500 font-mono">
                                        {{ $claim['date'] ?? '-' }}
                                    </td>
                                    <td class="px-3.5 py-3 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ ($claim['type'] ?? '') === 'cash' ? 'Transfer Bank / E-Wallet' : 'Voucher Potongan Tagihan' }}
                                    </td>
                                    <td class="px-3.5 py-3 text-slate-600 dark:text-slate-400">
                                        {{ $claim['bank_name'] ?? '-' }}
                                        @if (!empty($claim['account_number']) && $claim['account_number'] !== '-')
                                            <span class="block text-[10px] font-mono text-slate-400">{{ $claim['account_number'] }} ({{ $claim['account_holder'] ?? '-' }})</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-3 font-extrabold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format((int) ($claim['amount'] ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="px-3.5 py-3 text-right">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60">
                                            {{ $claim['status'] ?? 'Sedang Diproses' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- How it Works / Ketentuan --}}
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-5 text-xs text-slate-700 space-y-2 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-300">
            <h4 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                <span>💡</span> Cara Kerja &amp; Ketentuan Program Referral
            </h4>
            <ol class="list-decimal list-outside pl-4 space-y-1.5 text-slate-600 dark:text-slate-400 leading-relaxed">
                <li><strong>Bagikan Link:</strong> Kirimkan tautan referral ke kawan operator sekolah, bendahara BOS, atau grup MGMP/KKG Anda.</li>
                <li><strong>Sekolah Mendaftar:</strong> Rekan Anda mendaftar melalui tautan tersebut untuk mendapatkan akun dan uji coba CBT langsung.</li>
                <li><strong>Komisi 10% Otomatis:</strong> Setiap kali sekolah mitra menyelesaikan transaksi pembayaran paket langganan (semesteran maupun tahunan), Anda berhak atas 10% dari nilai transaksi.</li>
                <li><strong>Fleksibilitas Klaim:</strong> Saldo reward dapat dicairkan langsung ke rekening bank Anda atau ditukarkan menjadi voucher potongan perpanjangan langganan sekolah Anda sendiri.</li>
            </ol>
        </div>

        {{-- Claim Modal --}}
        <div
            x-show="showClaimModal"
            x-cloak
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
        >
            <div
                @click.away="showClaimModal = false"
                class="w-full max-w-md rounded-[2rem] bg-white p-6 shadow-2xl dark:bg-slate-900 dark:border dark:border-slate-800"
            >
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Klaim Reward Referral</h3>
                    <button type="button" @click="showClaimModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form method="POST" action="{{ route('school.referral.claim') }}" class="mt-4 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilihan Penarikan Reward:</label>
                        <select name="claim_type" x-model="claimType" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold dark:border-slate-700 dark:bg-slate-800">
                            <option value="discount_voucher">Tukar Potongan Tagihan Sekolah Sendiri (Rekomendasi)</option>
                            <option value="cash">Pencairan Transfer Bank / E-Wallet</option>
                        </select>
                    </div>

                    <div x-show="claimType === 'cash'" class="space-y-3 pt-2">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Bank / E-Wallet:</label>
                            <input type="text" name="bank_name" :required="claimType === 'cash'" placeholder="BCA / BRI / Mandiri / Dana / Gopay" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-800">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rekening / No. HP E-Wallet:</label>
                            <input type="text" name="account_number" :required="claimType === 'cash'" placeholder="Contoh: 1234567890" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-800">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Atas Nama Pemilik Rekening:</label>
                            <input type="text" name="account_holder" :required="claimType === 'cash'" placeholder="Nama sesuai buku tabungan" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-800">
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-200 dark:bg-slate-800 dark:border-slate-700">
                        <span class="text-[11px] text-slate-500">Saldo Siap Klaim Saat Ini:</span>
                        <p class="text-base font-extrabold text-emerald-600">Rp {{ number_format($availableBalance ?? $totalEarnings, 0, ',', '.') }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showClaimModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition cursor-pointer">
                            Ajukan Klaim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
