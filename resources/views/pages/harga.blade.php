<x-marketing-layout
    title="Harga — Paket Langganan Veyra Educate"
    description="Harga transparan Veyra Educate. Tanpa biaya tersembunyi."
>
    @php
        $plans = \App\Models\Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $annualPlan = $plans->firstWhere('code', 'tahunan');
        $annualPrice = (int) ($annualPlan?->price_idr ?? 1100000);
        $annualFormatted = 'Rp ' . number_format($annualPrice, 0, ',', '.');
    @endphp

    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 to-white dark:border-slate-700/50 dark:from-slate-900 dark:to-slate-950">
        <div class="container-site section">
            <x-section-heading
                eyebrow="Harga"
                title="Paket langganan transparan"
                lead="Harga yang tercantum di sini sama dengan yang ditagihkan saat checkout. Tanpa biaya tersembunyi. Pembayaran via QRIS, virtual account, dan e-wallet (Midtrans)."
                align="center"
            />

            <div class="mt-12 grid gap-6 lg:grid-cols-3">
                @forelse ($plans as $plan)
                    <article class="card-3d flex flex-col p-5 sm:p-8 {{ $plan->is_featured ? 'relative border-gold-300 ring-1 ring-gold-300' : '' }}">
                        @if ($plan->is_featured)
                            <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-gold-500 px-3.5 py-1 text-xs font-bold text-ink">
                                Paling dipilih sekolah
                            </span>
                        @endif

                        <h2 class="text-base sm:text-lg font-bold text-ink depth-1">{{ $plan->label }}</h2>
                        <p class="mt-1 text-xs sm:text-sm text-muted depth-1">{{ $plan->description }}</p>
                        <p class="mt-4 sm:mt-6 text-2xl sm:text-4xl font-extrabold tracking-tight text-ink depth-1">{{ $plan->formatPrice() }}</p>
                        <p class="mt-1 text-xs sm:text-sm font-medium {{ $plan->is_featured ? 'text-gold-700' : 'text-brand-700' }} depth-1">
                            {{ $plan->badge }}
                        </p>

                        <ul class="mt-7 flex-1 space-y-3 border-t {{ $plan->is_featured ? 'border-gold-100' : 'border-brand-100' }} pt-6 text-sm text-muted depth-1">
                            @foreach ($plan->features ?? [] as $feature)
                                <li class="flex gap-2.5">
                                    <span class="mt-0.5 shrink-0 {{ $plan->is_featured ? 'text-gold-600' : 'text-brand-600' }}" aria-hidden="true">✓</span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if ($plan->is_featured)
                            <x-gold-button variant="3d" class="mt-8 w-full"
                                href="{{ auth()->check() ? route('checkout.show', $plan->code) : route('register', ['paket' => $plan->code]) }}">
                                Pilih {{ $plan->label }}
                            </x-gold-button>
                        @elseif ($plan->code === 'tahunan')
                            <x-primary-button variant="3d" class="mt-8 w-full"
                                href="{{ auth()->check() ? route('checkout.show', $plan->code) : route('register', ['paket' => $plan->code]) }}">
                                Pilih {{ $plan->label }}
                            </x-primary-button>
                        @else
                            <x-secondary-button variant="3d" class="mt-8 w-full"
                                href="{{ auth()->check() ? route('checkout.show', $plan->code) : route('register', ['paket' => $plan->code]) }}">
                                Pilih {{ $plan->label }}
                            </x-secondary-button>
                        @endif
                        <p class="mt-3 text-center text-xs text-muted">Daftar, bayar, langsung aktif.</p>
                    </article>
                @empty
                    <p class="text-sm text-muted lg:col-span-3">Paket belum tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Interactive BOS Savings Calculator --}}
    <section class="border-y border-brand-100 bg-gradient-to-br from-emerald-50/50 via-white to-brand-50/40 dark:border-slate-800 dark:from-slate-900/60 dark:via-slate-950 dark:to-slate-900/40 py-14 sm:py-20" x-data="bosSavingsCalculator()">
        <div class="container-site">
            <div class="max-w-2xl mx-auto text-center mb-10">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-3.5 py-1 text-xs font-bold mb-3 shadow-sm">
                    <span>💡</span> KALKULATOR EFISIENSI ANGGARAN BOSP
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink dark:text-white">
                    Berapa Juta Dana BOS yang Bisa Anda Hemat?
                </h2>
                <p class="mt-3 text-sm text-muted leading-relaxed">
                    Ujian berbasis kertas memakan anggaran besar untuk lembar soal, LJK, master cetak, dan fotokopi. Geser indikator di bawah untuk melihat potensi penghematan riil di sekolah Anda.
                </p>
            </div>

            <div class="grid lg:grid-cols-12 gap-8 items-center max-w-5xl mx-auto">
                {{-- Form Controls Slider --}}
                <div class="lg:col-span-7 card p-6 sm:p-8 space-y-6 border-slate-200/90 dark:border-slate-800 shadow-md bg-white dark:bg-slate-900">
                    {{-- Slider 1: Jumlah Siswa --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs sm:text-sm font-bold text-ink dark:text-slate-200 flex items-center gap-2">
                                <span>👥</span> Jumlah Siswa Peserta Ujian:
                            </label>
                            <span class="px-3 py-1 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 font-mono font-black text-sm border border-brand-200 dark:border-brand-800" x-text="studentCount + ' Siswa'"></span>
                        </div>
                        <input
                            type="range"
                            min="50"
                            max="1500"
                            step="25"
                            x-model.number="studentCount"
                            class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-brand-600"
                        />
                        <div class="flex justify-between text-[11px] text-slate-400 mt-1 font-mono">
                            <span>50 siswa</span>
                            <span>500 siswa</span>
                            <span>1.000 siswa</span>
                            <span>1.500 siswa</span>
                        </div>
                    </div>

                    {{-- Slider 2: Frekuensi Ujian per Tahun --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs sm:text-sm font-bold text-ink dark:text-slate-200 flex items-center gap-2">
                                <span>📅</span> Frekuensi Asesmen per Tahun:
                            </label>
                            <span class="px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-mono font-black text-sm border border-emerald-200 dark:border-emerald-800" x-text="examsPerYear + 'x Pelaksanaan'"></span>
                        </div>
                        <input
                            type="range"
                            min="2"
                            max="6"
                            step="1"
                            x-model.number="examsPerYear"
                            class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-emerald-600"
                        />
                        <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                            <span>2x (STS &amp; SAS)</span>
                            <span>4x (+ UTS/UAS Semester 2)</span>
                            <span>6x (+ Tryout &amp; Ujian Sekolah)</span>
                        </div>
                    </div>

                    {{-- Selector: Rata-rata Mapel & Lembar Soal --}}
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">
                                Rata-rata Mapel per Ujian:
                            </label>
                            <select
                                x-model.number="subjectsCount"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            >
                                <option value="8">8 Mata Pelajaran</option>
                                <option value="10">10 Mata Pelajaran (Standar)</option>
                                <option value="12">12 Mata Pelajaran</option>
                                <option value="15">15 Mata Pelajaran (SMK/Komplit)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">
                                Estimasi Lembar per Mapel:
                            </label>
                            <select
                                x-model.number="pagesPerSubject"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            >
                                <option value="3">3 Lembar (Singkat)</option>
                                <option value="4">4 Lembar (Soal + LJK)</option>
                                <option value="5">5 Lembar (Komprehensif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-[11px] text-muted bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                        *Asumsi biaya cetak/fotokopi kertas konvensional Rp 300/lembar (termasuk biaya master, kertas F4/A4 70gr, tinta, dan penjilidan).
                    </div>
                </div>

                {{-- Result Output Card --}}
                <div class="lg:col-span-5 card p-6 sm:p-8 border-2 border-emerald-500/40 bg-gradient-to-b from-white via-emerald-50/20 to-emerald-100/30 dark:from-slate-900 dark:via-emerald-950/20 dark:to-slate-900 shadow-xl relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-emerald-400/10 blur-2xl pointer-events-none"></div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil Simulasi Efisiensi</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-600 text-white text-[11px] font-bold shadow-sm" x-text="'Hemat ' + savingsPercent() + '%'">
                                Hemat 88%
                            </span>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-muted">Estimasi Dana BOS yang Berhasil Dihemat:</div>
                            <div class="text-3xl sm:text-4xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight mt-1 font-mono" x-text="formatRupiah(savings())">
                                Rp 16.700.000
                            </div>
                            <div class="text-[11px] text-emerald-700 dark:text-emerald-300 font-semibold mt-0.5">
                                per tahun anggaran sekolah
                            </div>
                        </div>

                        {{-- Perbandingan Detail --}}
                        <div class="space-y-2 pt-3 border-t border-slate-200/80 dark:border-slate-800 text-xs">
                            <div class="flex items-center justify-between py-1">
                                <span class="text-slate-600 dark:text-slate-400">Biaya Kertas &amp; Cetak Manual:</span>
                                <span class="font-bold font-mono text-rose-600 line-through" x-text="formatRupiah(paperCost())"></span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span class="text-slate-600 dark:text-slate-400">Biaya Lisensi Veyra (1 Tahun):</span>
                                <span class="font-bold font-mono text-brand-600 dark:text-brand-400">{{ $annualFormatted }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-slate-600 dark:text-slate-400">Kertas yang Dieliminasi:</span>
                                <span class="font-bold font-mono text-ink dark:text-white" x-text="totalSheets().toLocaleString('id-ID') + ' lembar'"></span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span class="text-slate-600 dark:text-slate-400">Dampak Lingkungan Hijau:</span>
                                <span class="font-bold text-emerald-600" x-text="'± ' + treesSaved() + ' pohon diselamatkan 🌳'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-200/80 dark:border-slate-800 space-y-2.5">
                        <a
                            href="/proposal"
                            class="btn-primary w-full justify-center text-xs sm:text-sm py-2.5 shadow-md shadow-brand-500/20"
                        >
                            Cetak Bukti Hemat untuk Rapat BOS (A4) &rarr;
                        </a>
                        <a
                            href="{{ auth()->check() ? route('checkout.show', 'tahunan') : route('register', ['paket' => 'tahunan']) }}"
                            class="btn-secondary w-full justify-center text-xs py-2 border-slate-200 dark:border-slate-700"
                        >
                            Pilih Paket 1 Tahun ({{ $annualFormatted }})
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Script BOS Savings Calculator Logic --}}
    <script>
        function bosSavingsCalculator() {
            return {
                studentCount: 400,
                examsPerYear: 4,
                subjectsCount: 10,
                pagesPerSubject: 4,
                costPerPage: 300,
                veyraCost: {{ $annualPrice }},
                totalSheets() {
                    return this.studentCount * this.examsPerYear * this.subjectsCount * this.pagesPerSubject;
                },
                paperCost() {
                    return this.totalSheets() * this.costPerPage;
                },
                savings() {
                    return Math.max(0, this.paperCost() - this.veyraCost);
                },
                savingsPercent() {
                    const cost = this.paperCost();
                    if (cost <= 0) return 0;
                    return Math.round((this.savings() / cost) * 100);
                },
                treesSaved() {
                    // Rata-rata 1 pohon menghasilkan sekitar 8.333 lembar kertas HVS
                    return (this.totalSheets() / 8333).toFixed(1);
                },
                formatRupiah(num) {
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                }
            };
        }
    </script>

    <section class="section container-site">
            <x-section-heading
                eyebrow="Bandingkan"
                title="Apa yang berubah antar paket?"
                lead="Akses panel & aplikasi tersedia di semua paket. Paket lebih tinggi menambah masa berlaku, branding, dan prioritas support."
                align="center"
            />

            <div class="mt-8 sm:mt-10">
                <p class="text-xs text-muted mb-2 sm:hidden flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                    <span>Geser tabel ke samping untuk melihat semua paket &rarr;</span>
                </p>
                <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                    <table class="w-full min-w-[600px] border-separate border-spacing-0 overflow-hidden rounded-2xl border border-brand-100 bg-white text-left text-sm dark:border-slate-700/50 dark:bg-slate-900">
                    <thead class="bg-brand-50 dark:bg-slate-800/80">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-bold text-ink dark:text-slate-100">Fitur</th>
                            <th scope="col" class="px-5 py-4 font-bold text-ink dark:text-slate-100">Semester</th>
                            <th scope="col" class="px-5 py-4 font-bold text-ink dark:text-slate-100">1 Tahun</th>
                            <th scope="col" class="px-5 py-4 font-bold text-gold-800 dark:text-amber-300">Super VIP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-100 dark:divide-slate-700/50">
                        @foreach ([
                            ['Panel sekolah & akun tim (Kepala Sekolah, Operator, Bendahara BOS)', true, true, true],
                            ['Semua aplikasi pendidikan (CBT, E-Raport, Absensi, PPDB, dll)', true, true, true],
                            ['Generator Cetak Kartu Ujian & Kartu Pelajar (A4 + Barcode/QR)', true, true, true],
                            ['Pusat Broadcast WhatsApp Sekolah & Pengingat Wali Murid', true, true, true],
                            ['Kwitansi Resmi & Dokumen BAST Otomatis untuk SPJ Dana BOS', true, true, true],
                            ['Program Kemitraan Referral Sekolah (Komisi 10%)', true, true, true],
                            ['Pembayaran QRIS, Virtual Account & Transfer Bank BOS', true, true, true],
                            ['Masa berlaku 1 tahun penuh + pembaruan fitur prioritas', false, true, true],
                            ['Custom branding sekolah & sub-domain khusus', false, false, true],
                            ['Pendampingan onboarding & pelatihan tim operator/guru', false, false, true],
                            ['Priority / VIP support via WhatsApp', false, true, true],
                        ] as [$label, $a, $b, $c])
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                            <th scope="row" class="px-5 py-3.5 font-medium text-ink dark:text-slate-200">{{ $label }}</th>
                            <td class="px-5 py-3.5 font-semibold {{ $a ? 'text-emerald-600 dark:text-emerald-400' : 'text-muted' }}">{{ $a ? '✓' : '—' }}</td>
                            <td class="px-5 py-3.5 font-semibold {{ $b ? 'text-emerald-600 dark:text-emerald-400' : 'text-muted' }}">{{ $b ? '✓' : '—' }}</td>
                            <td class="px-5 py-3.5 font-semibold {{ $c ? 'text-emerald-600 dark:text-emerald-400' : 'text-muted' }}">{{ $c ? '✓' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="border-t border-brand-100 bg-white dark:border-slate-700/50 dark:bg-slate-950">
        <div class="container-site section">
            <div class="grid gap-8 lg:grid-cols-3">
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-slate-100">Cara bayar</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted dark:text-slate-400">
                        QRIS, virtual account bank, e-wallet melalui Midtrans, serta Transfer Bank Manual untuk rekening Dana BOS sekolah.
                    </p>
                </div>
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-slate-100">Aktivasi &amp; Dokumen SPJ BOS</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted dark:text-slate-400">
                        Setelah pelunasan terkonfirmasi, langganan langsung aktif dan sekolah dapat mencetak Kwitansi Resmi beserta BAST A4 untuk laporan SPJ Dana BOS.
                    </p>
                </div>
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-slate-100">Ganti paket &amp; Komisi Referral</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted dark:text-slate-400">
                        Sekolah juga dapat menggunakan saldo komisi referral (10%) untuk memotong tagihan checkout atau memperpanjang masa aktif langganan.
                    </p>
                </div>
            </div>

            <div class="card-3d mt-10 flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:justify-between sm:text-left">
                <div class="depth-1">
                    <h3 class="text-lg font-extrabold text-ink">Siap memilih paket?</h3>
                    <p class="mt-1 text-sm text-muted">Checkout kurang dari lima menit. Tim kami siap dampingi onboarding.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row depth-1">
                    <x-primary-button variant="3d" href="{{ route('register') }}">Daftar sekarang</x-primary-button>
                    <x-secondary-button variant="3d" href="/faq">Baca FAQ</x-secondary-button>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
