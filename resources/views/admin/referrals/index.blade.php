<x-admin-layout title="Referral & Komisi — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Kemitraan Sekolah</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Manajemen Referral & Klaim Komisi</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-site space-y-6">

            {{-- Info Banner: Cara Kerja Kas & Saldo Komisi Admin --}}
            <div class="rounded-2xl border border-indigo-200/80 bg-indigo-50/70 p-4 sm:p-5 dark:border-indigo-900/50 dark:bg-indigo-950/30">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-xs">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-xs sm:text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                        <p class="font-bold text-slate-900 dark:text-white">Apakah Panel Admin perlu diisi saldo (Top-Up)? Tidak perlu.</p>
                        <p class="mt-1">
                            Dana komisi referral <strong>(10%)</strong> otomatis dihitung dari pembayaran langganan sekolah rekrutan yang <strong>sudah masuk 100% ke rekening/Midtrans Veyra</strong>.
                            Untuk klaim <strong>Transfer Bank/E-Wallet</strong>, silakan transfer manual melalui M-Banking Veyra ke rekening sekolah di bawah lalu klik <strong>"Setujui / Sudah Ditransfer"</strong>.
                            Untuk klaim <strong>Voucher Langganan</strong>, tidak ada uang keluar — klik <strong>"Setujui"</strong> dan sistem akan otomatis menambah masa aktif langganan sekolah tersebut.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 4 Stat Cards: Kas & Kewajiban Komisi --}}
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-brand-100 text-brand-700 dark:bg-brand-950/40 dark:text-brand-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="stat-label">Omzet dari Referral (100%)</p>
                    <p class="stat-value">Rp {{ number_format($totalReferralRevenue, 0, ',', '.') }}</p>
                    <p class="stat-change neutral">Total pembayaran lunas sekolah rekrutan</p>
                </div>

                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-violet-100 text-violet-700 dark:bg-violet-950/40 dark:text-violet-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="stat-label">Alokasi Dana Komisi (10%)</p>
                    <p class="stat-value">Rp {{ number_format($totalCommissionPool, 0, ',', '.') }}</p>
                    <p class="stat-change neutral">Cadangan komisi dari omzet referral</p>
                </div>

                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="stat-label">Menunggu Pencairan ({{ $pendingCount }})</p>
                    <p class="stat-value">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</p>
                    <p class="stat-change {{ $pendingCount > 0 ? 'negative' : 'positive' }}">
                        {{ $pendingCount > 0 ? $pendingCount.' pengajuan perlu diproses' : 'Tidak ada antrean klaim' }}
                    </p>
                </div>

                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="stat-label">Total Sudah Dicairkan</p>
                    <p class="stat-value">Rp {{ number_format($paidOutAmount, 0, ',', '.') }}</p>
                    <p class="stat-change positive">Komisi selesai ditransfer / dikonversi</p>
                </div>
            </div>

            {{-- Filter & Daftar Klaim Komisi --}}
            <div class="card-minimal overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Pengajuan Klaim Komisi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Setujui pencairan setelah transfer atau tolak jika nomor rekening tidak valid (saldo otomatis kembali ke sekolah).</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach ([
                            'all' => 'Semua',
                            'pending' => 'Menunggu Diproses',
                            'approved' => 'Sudah Dicairkan',
                            'rejected' => 'Ditolak',
                        ] as $key => $label)
                            <a href="{{ route('admin.referrals.index', ['status' => $key]) }}"
                               class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $statusFilter === $key ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if ($claims->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada pengajuan klaim komisi pada filter ini.</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pengajuan klaim dari sekolah mitra akan otomatis tampil di tabel ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-5 py-3.5">ID Klaim & Tanggal</th>
                                    <th class="px-5 py-3.5">Sekolah Pengaju</th>
                                    <th class="px-5 py-3.5">Metode & Tujuan Pencairan</th>
                                    <th class="px-5 py-3.5">Nominal Komisi</th>
                                    <th class="px-5 py-3.5">Status</th>
                                    <th class="px-5 py-3.5 text-right">Tindakan Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($claims as $claim)
                                    @php
                                        /** @var \App\Models\School $claimSchool */
                                        $claimSchool = $claim['school'];
                                        $statusLower = strtolower($claim['status']);
                                        $isPending = $statusLower === 'sedang diproses';
                                        $isApproved = in_array($statusLower, ['selesai', 'dicairkan', 'disetujui', 'approved', 'paid'], true);
                                        $isRejected = in_array($statusLower, ['ditolak', 'rejected', 'batal', 'cancelled'], true);
                                        $bonusDays = max(15, (int) round(($claim['amount'] / 100000) * 30));
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition align-top">
                                        <td class="px-5 py-4">
                                            <p class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">{{ $claim['id'] }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $claim['date'] }}</p>
                                            @if ($claim['processed_at'])
                                                <p class="text-[10px] text-slate-400 mt-0.5">Diproses: {{ $claim['processed_at'] }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('admin.schools.show', $claimSchool) }}" class="font-bold text-slate-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">
                                                {{ $claimSchool->name }}
                                            </a>
                                            <p class="text-xs text-slate-500 mt-0.5">Kode: <span class="font-mono font-semibold">{{ $claimSchool->referral_code }}</span></p>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($claim['type'] === 'discount_voucher')
                                                <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                                    Voucher Perpanjangan Langganan (+{{ $bonusDays }} Hari)
                                                </span>
                                                <p class="text-xs text-slate-500 mt-1">Otomatis memperpanjang masa aktif langganan sekolah saat disetujui.</p>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                    Transfer Tunai — {{ $claim['bank_name'] }}
                                                </span>
                                                <p class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">
                                                    No. Rek: {{ $claim['account_number'] }}
                                                </p>
                                                <p class="text-xs text-slate-500">A/n: {{ $claim['account_holder'] }}</p>
                                            @endif
                                            @if ($claim['admin_notes'])
                                                <p class="mt-1.5 text-[11px] italic text-slate-500 dark:text-slate-400">Catatan: "{{ $claim['admin_notes'] }}"</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">
                                                Rp {{ number_format($claim['amount'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($isApproved)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ $claim['status'] }}
                                                </span>
                                            @elseif ($isRejected)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 dark:bg-rose-950/60 px-2.5 py-1 text-xs font-bold text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                    {{ $claim['status'] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 text-xs font-bold text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    {{ $claim['status'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            @if ($isPending)
                                                <div class="flex flex-col items-end gap-2" x-data="{ showNotes: false }">
                                                    <div class="flex flex-wrap justify-end gap-1.5">
                                                        <form method="POST" action="{{ route('admin.referrals.update-status', [$claimSchool, $claim['id']]) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition cursor-pointer">
                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                {{ $claim['type'] === 'discount_voucher' ? 'Setujui (+'.$bonusDays.' Hari)' : 'Setujui (Sudah Ditransfer)' }}
                                                            </button>
                                                        </form>

                                                        <button type="button" @click="showNotes = !showNotes" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 dark:border-rose-800/60 dark:bg-rose-950/40 dark:text-rose-300 transition cursor-pointer">
                                                            Tolak
                                                        </button>
                                                    </div>

                                                    <form x-show="showNotes" x-cloak method="POST" action="{{ route('admin.referrals.update-status', [$claimSchool, $claim['id']]) }}" class="w-full max-w-xs space-y-1.5 pt-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="action" value="reject">
                                                        <input type="text" name="admin_notes" placeholder="Alasan penolakan (mis: No. rek salah)..." class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                                        <button type="submit" class="w-full rounded-lg bg-rose-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-rose-700 transition cursor-pointer">
                                                            Konfirmasi Tolak & Kembalikan Saldo
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400">Telah diproses</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Ringkasan Mitra Sekolah Pengundang --}}
            @if ($topReferrers->isNotEmpty())
                <div class="card-minimal overflow-hidden">
                    <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Performa Sekolah Mitra Pengundang</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Daftar sekolah yang telah mengundang sekolah lain beserta saldo komisi mereka.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50/80 dark:bg-slate-900/50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-5 py-3">Sekolah Pengundang</th>
                                    <th class="px-5 py-3">Kode Referral</th>
                                    <th class="px-5 py-3">Sekolah Direkrut</th>
                                    <th class="px-5 py-3">Total Hak Komisi (10%)</th>
                                    <th class="px-5 py-3">Sisa Saldo Siap Klaim</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($topReferrers as $row)
                                    <tr>
                                        <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">{{ $row['school']->name }}</td>
                                        <td class="px-5 py-3.5 font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ $row['school']->referral_code }}</td>
                                        <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">
                                            {{ $row['invited_count'] }} sekolah ({{ $row['active_count'] }} aktif berlangganan)
                                        </td>
                                        <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-slate-200">
                                            Rp {{ number_format($row['total_earnings'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-3.5 font-bold text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($row['available_balance'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>

