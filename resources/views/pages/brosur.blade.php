<x-marketing-layout
    title="{{ $marketing_page?->title ?? 'Brosur Digital — Veyra Educate & CBT Online' }}"
    description="{{ $marketing_page?->description ?? 'Brosur resmi digitalisasi ujian sekolah berbasis komputer (CBT Online) dan portal aplikasi Veyra Educate. Hemat jutaan rupiah biaya cetak kertas, 100% cloud, dan ramah SPJ Dana BOS.' }}">
    {{-- Print Optimization Styles --}}
    <style>
        @media print {
            header, footer, .no-print, .whatsapp-float {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 11pt !important;
            }
            .print-container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-break-inside {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .print-shadow-none {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>

    {{-- Top Action Bar (Floating on Screen, Hidden in Print) --}}
    <div class="no-print sticky top-16 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/90 py-3 transition-all">
        <div class="container-site flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm">
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-bold text-slate-800 dark:text-slate-200">Brosur Resmi Veyra Educate &amp; CBT 2026</span>
            </div>
            <div class="flex items-center gap-2">
                <button
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-1.5 font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer shadow-xs"
                >
                    <svg class="h-4 w-4 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
                <a
                    href="{{ url('proposal') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 dark:border-brand-800 bg-brand-50 dark:bg-brand-950/40 px-3.5 py-1.5 font-semibold text-brand-700 dark:text-brand-300 hover:bg-brand-100 transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Surat Penawaran Resmi</span>
                </a>
                <a
                    href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate,%20saya%20tertarik%20dengan%20brosur%20aplikasi%20ujian%20CBT%20sekolah"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 font-semibold shadow-xs transition"
                >
                    <span>Chat WhatsApp</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Printable Brochure Content --}}
    <main class="print-container container-site py-8 sm:py-12 max-w-5xl">
        {{-- Hero Presentation Banner --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-brand-950 to-slate-900 text-white p-7 sm:p-12 shadow-2xl print-shadow-none print:bg-white print:text-slate-900 print:p-4">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 rounded-full border border-gold-400/40 bg-gold-400/10 px-3.5 py-1 text-xs font-bold text-gold-300 mb-6 print:border-slate-300 print:text-slate-800">
                    <span class="h-2 w-2 rounded-full bg-gold-400"></span>
                    SOLUSI ASESMEN DIGITAL SEKOLAH INDONESIA
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight max-w-3xl">
                    Ujian Sekolah Berbasis Komputer
                    <span class="bg-gradient-to-r from-gold-300 via-amber-200 to-gold-400 bg-clip-text text-transparent print:text-brand-700">
                        Tanpa Ribet Sewa Server Lokal.
                    </span>
                </h1>

                <p class="mt-4 sm:mt-6 text-sm sm:text-lg leading-relaxed text-slate-200 max-w-2xl print:text-slate-700">
                    Tinggalkan pemborosan fotokopi ribuan lembar kertas soal dan kepusingan teknis VHD. Veyra CBT hadir 100% berbasis cloud: siswa ujian dari ponsel/laptop, nilai langsung terhitung otomatis ke Excel, dan dilengkapi kuitansi resmi untuk SPJ Dana BOS.
                </p>

                {{-- Quick Metric Highlights --}}
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 pt-6 border-t border-white/10 print:border-slate-200">
                    <div class="rounded-2xl bg-white/5 p-3.5 border border-white/10 print:border-slate-200 print:bg-slate-50">
                        <p class="text-xl sm:text-3xl font-extrabold text-gold-400 print:text-brand-700">Hemat 70%</p>
                        <p class="text-xs text-slate-300 print:text-slate-600 mt-1">Biaya Cetak Kertas Ujian</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-3.5 border border-white/10 print:border-slate-200 print:bg-slate-50">
                        <p class="text-xl sm:text-3xl font-extrabold text-sky-400 print:text-brand-700">100% Cloud</p>
                        <p class="text-xs text-slate-300 print:text-slate-600 mt-1">Bebas VHD &amp; Kabel LAN</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-3.5 border border-white/10 print:border-slate-200 print:bg-slate-50">
                        <p class="text-xl sm:text-3xl font-extrabold text-emerald-400 print:text-brand-700">1 Detik</p>
                        <p class="text-xs text-slate-300 print:text-slate-600 mt-1">Koreksi &amp; Rekap Excel</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-3.5 border border-white/10 print:border-slate-200 print:bg-slate-50">
                        <p class="text-xl sm:text-3xl font-extrabold text-amber-300 print:text-brand-700">Resmi</p>
                        <p class="text-xs text-slate-300 print:text-slate-600 mt-1">Kwitansi Siap SPJ BOS</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 1: Problem vs Solution --}}
        <section class="mt-12 sm:mt-16 print-break-inside">
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Komparasi Metode</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                    Mengapa Sekolah Beralih ke Veyra CBT?
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">
                    Perbandingan langsung antara metode ujian lembar kertas konvensional dengan sistem digital Veyra.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Traditional Method Card --}}
                <div class="rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50/40 dark:bg-red-950/20 p-6 sm:p-8">
                    <div class="flex items-center gap-2.5 text-red-700 dark:text-red-400 font-bold text-base sm:text-lg mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Metode Konvensional (Kertas &amp; VHD)</span>
                    </div>
                    <ul class="space-y-3.5 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                        <li class="flex items-start gap-2.5">
                            <span class="text-red-500 font-bold">✕</span>
                            <span><strong>Boros Biaya:</strong> Habis jutaan rupiah setiap semester hanya untuk fotokopi kertas lembar soal yang sekali pakai dibuang.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-red-500 font-bold">✕</span>
                            <span><strong>Pusing Teknis Server:</strong> Operator repot setting VirtualBox/VHD, mikrotik sering trouble, dan kabel LAN sering putus.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-red-500 font-bold">✕</span>
                            <span><strong>Koreksi Manual Melelahkan:</strong> Guru harus meluangkan waktu berhari-hari mengoreksi tumpukan kertas jawaban.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-red-500 font-bold">✕</span>
                            <span><strong>Rawan Kebocoran:</strong> Soal kertas mudah difoto, disalin, atau tersebar sebelum ujian dimulai.</span>
                        </li>
                    </ul>
                </div>

                {{-- Veyra CBT Solution Card --}}
                <div class="rounded-2xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50/50 dark:bg-emerald-950/20 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center gap-2.5 text-emerald-700 dark:text-emerald-400 font-bold text-base sm:text-lg mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Solusi Veyra Educate &amp; CBT Online</span>
                    </div>
                    <ul class="space-y-3.5 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Hemat Luar Biasa:</strong> Investasi langganan sangat terjangkau mulai Rp 600rb/semester tanpa batas siswa/guru.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>100% Siap Pakai:</strong> Tanpa instalasi server lokal, siswa dan guru cukup membuka link website dari HP atau komputer lab.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Nilai &amp; Analisis Instan:</strong> Begitu siswa klik selesai, nilai langsung muncul dan rekap nilai per kelas langsung jadi Excel.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Anti-Curang Canggih:</strong> Pengacakan nomor soal, pengacakan opsi jawaban (A,B,C,D,E), dan token dinamis.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Section 2: 6 Core Pillars --}}
        <section class="mt-12 sm:mt-16 print-break-inside">
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Fitur Unggulan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                    Dirancang Khusus untuk Sekolah Indonesia
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">
                    Mendukung Kurikulum Merdeka, Asesmen Sumatif (STS/SAS), Ujian Sekolah, hingga Asesmen Madrasah.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-brand-300 transition">
                    <div class="h-10 w-10 rounded-xl bg-brand-100 dark:bg-brand-950/60 text-brand-600 dark:text-brand-300 flex items-center justify-center font-bold text-lg mb-4">
                        📱
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Multi-Perangkat Ramah Siswa</h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Siswa dapat mengerjakan ujian menggunakan smartphone pribadi (Android/iPhone), tablet, maupun PC laboratorium tanpa perlu instal aplikasi berat.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-brand-300 transition">
                    <div class="h-10 w-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 flex items-center justify-center font-bold text-lg mb-4">
                        ⚡
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Tanpa Sewa Server Fisik</h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Semua sistem berjalan di server cloud kami yang berkecepatan tinggi. Sekolah tidak perlu keluar modal jutaan untuk membeli CPU server ujian.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-brand-300 transition">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 flex items-center justify-center font-bold text-lg mb-4">
                        🛡️
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Keamanan &amp; Anti-Curang</h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Fitur acak urutan soal dan acak pilihan jawaban memastikan siswa di samping kiri-kanan tidak bisa saling mencontek jawaban.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-brand-300 transition">
                    <div class="h-10 w-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 flex items-center justify-center font-bold text-lg mb-4">
                        📥
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Import Soal Fleksibel</h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Guru tidak perlu input manual satu persatu. Soal pilihan ganda, uraian, dan gambar dapat diimpor langsung melalui format Excel atau template Word.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-brand-300 transition">
                    <div class="h-10 w-10 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-300 flex items-center justify-center font-bold text-lg mb-4">
                        📊
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Rekap Nilai Otomatis ke Excel</h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Hasil evaluasi siswa langsung terekap per mata pelajaran dan per kelas. Siap diunduh ke Excel untuk keperluan pengisian nilai E-Rapor.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:border-brand-300 transition">
                    <div class="h-10 w-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 flex items-center justify-center font-bold text-lg mb-4">
                        📜
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Kwitansi Sah untuk SPJ BOS</h3>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Sistem menerbitkan faktur dan kuitansi berstempel digital resmi lengkap dengan tanda tangan digital untuk kemudahan laporan SPJ Bendahara Sekolah.
                    </p>
                </div>
            </div>
        </section>

        {{-- Section 3: Cost Savings Calculator Table --}}
        <section class="mt-12 sm:mt-16 print-break-inside">
            <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Analisis Anggaran Sekolah</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                            Simulasi Penghematan Dana Operasional
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Estimasi perhitungan untuk sekolah dengan 300 siswa (10 mata pelajaran per ujian):
                        </p>
                    </div>
                    <div class="shrink-0 bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-300 text-emerald-800 dark:text-emerald-300 px-4 py-2 rounded-2xl text-center">
                        <span class="text-[10px] uppercase font-bold block">Hemat Biaya Hingga</span>
                        <span class="text-lg sm:text-xl font-extrabold">Rp 4.000.000+ / Tahun</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="border-b border-slate-200 dark:border-slate-800 text-slate-500">
                            <tr>
                                <th class="pb-3 font-semibold">Komponen Pengeluaran</th>
                                <th class="pb-3 font-semibold">Metode Kertas Tradisional</th>
                                <th class="pb-3 font-semibold text-emerald-600 dark:text-emerald-400">Dengan Veyra CBT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70 text-slate-700 dark:text-slate-300">
                            <tr>
                                <td class="py-3 font-medium">Kertas Soal &amp; Lembar Jawaban (LJK)</td>
                                <td class="py-3 text-red-600 font-semibold">Rp 850.000 / ujian</td>
                                <td class="py-3 text-emerald-600 font-bold">Rp 0 (Digital)</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-medium">Biaya Cetak / Fotokopi Soal</td>
                                <td class="py-3 text-red-600 font-semibold">Rp 900.000 / ujian</td>
                                <td class="py-3 text-emerald-600 font-bold">Rp 0 (Digital)</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-medium">Waktu Koreksi Guru</td>
                                <td class="py-3 text-slate-500">3 - 7 hari kerja lembur</td>
                                <td class="py-3 text-emerald-600 font-bold">Otomatis 1 Detik Selesai</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-medium">Alat Tulis, Amplop, &amp; Map Soal</td>
                                <td class="py-3 text-red-600 font-semibold">Rp 250.000 / ujian</td>
                                <td class="py-3 text-emerald-600 font-bold">Rp 0</td>
                            </tr>
                            <tr class="bg-white/80 dark:bg-slate-900/80 font-bold">
                                <td class="py-3.5 text-slate-900 dark:text-slate-100">Total Pengeluaran (STS + SAS per Tahun)</td>
                                <td class="py-3.5 text-red-600 text-sm sm:text-base">Rp 4.000.000 - Rp 6.000.000</td>
                                <td class="py-3.5 text-emerald-600 text-sm sm:text-base">Hanya Rp 1.100.000 / Tahun</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Section 4: Pricing Packages --}}
        <section class="mt-12 sm:mt-16 print-break-inside">
            <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">Pilihan Paket Sekolah</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 mt-1">
                    Tarif Transparan &amp; Sangat Terjangkau
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-2">
                    Tanpa biaya tersembunyi. Termasuk pembaruan sistem dan bantuan teknis onboarding via WhatsApp.
                </p>
            </div>

            {{-- Satu Container Tunggal dengan 3 Split Kolom --}}
            <div class="rounded-3xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl overflow-hidden print-shadow-none">
                <div class="grid md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 dark:divide-slate-800">
                    
                    {{-- Split 1: Paket Semester --}}
                    <div class="p-6 sm:p-8 flex flex-col justify-between transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                        <div>
                            <span class="inline-block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Masa Aktif: 6 Bulan
                            </span>
                            <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100 mt-1.5">
                                Paket Semesteran
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Cocok untuk 1 periode semester ajaran (Ganjil / Genap).
                            </p>

                            <div class="mt-5 pb-5 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">Rp 600.000</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 block mt-1">Sekali bayar untuk 6 bulan penuh</span>
                            </div>

                            <ul class="mt-6 space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Akses penuh Veyra CBT Online</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Akun guru, operator, &amp; siswa tanpa batas</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Bank soal &amp; import Word/Excel</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Rekap nilai langsung ke format Excel</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Kuitansi resmi SPJ Dana BOS</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-800 no-print">
                            <a href="{{ auth()->check() ? route('checkout.show', 'semester') : route('register', ['paket' => 'semester']) }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold">
                                Pilih Paket Semester
                            </a>
                        </div>
                    </div>

                    {{-- Split 2: Paket 1 Tahun Penuh (Featured Highlighted Split) --}}
                    <div class="relative p-6 sm:p-8 flex flex-col justify-between bg-gradient-to-b from-brand-50/80 via-brand-50/40 to-white dark:from-brand-950/50 dark:via-brand-950/20 dark:to-slate-900 border-t-4 md:border-t-0 md:border-t-4 md:border-t-brand-600">
                        <div>
                            <div class="mb-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-600 text-white px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-gold-300 animate-pulse"></span>
                                    Paling Direkomendasikan
                                </span>
                            </div>
                            <span class="inline-block text-[11px] font-bold text-brand-700 dark:text-brand-300 uppercase tracking-wider">
                                Masa Aktif: 12 Bulan Penuh
                            </span>
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-slate-100 mt-1">
                                Paket 1 Tahun Penuh
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                Mencakup seluruh STS, SAS, Ujian Sekolah, &amp; Try Out setahun.
                            </p>

                            <div class="mt-5 pb-5 border-b border-brand-200/70 dark:border-brand-900/50">
                                <span class="text-3xl sm:text-4xl font-black text-brand-700 dark:text-brand-300">Rp 1.100.000</span>
                                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 block mt-1">
                                    ★ Lebih hemat Rp 100.000 dibanding semesteran
                                </span>
                            </div>

                            <ul class="mt-6 space-y-3 text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-medium">
                                <li class="flex items-start gap-2.5">
                                    <span class="text-brand-600 dark:text-brand-400 font-bold shrink-0">✓</span>
                                    <span><strong>Seluruh fasilitas</strong> Paket Semester</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-brand-600 dark:text-brand-400 font-bold shrink-0">✓</span>
                                    <span>Masa berlaku 1 tahun penuh kalender</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-brand-600 dark:text-brand-400 font-bold shrink-0">✓</span>
                                    <span>Prioritas bantuan onboarding WhatsApp</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-brand-600 dark:text-brand-400 font-bold shrink-0">✓</span>
                                    <span>Pembaruan sistem &amp; fitur berkelanjutan</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-brand-600 dark:text-brand-400 font-bold shrink-0">✓</span>
                                    <span>Faktur &amp; Kuitansi resmi SPJ BOS</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 pt-5 border-t border-brand-200/70 dark:border-brand-900/50 no-print">
                            <a href="{{ auth()->check() ? route('checkout.show', 'tahunan') : route('register', ['paket' => 'tahunan']) }}" class="btn-primary w-full text-center text-xs py-2.5 font-bold shadow-md">
                                Pilih 1 Tahun Penuh &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Split 3: Paket Super VIP --}}
                    <div class="p-6 sm:p-8 flex flex-col justify-between transition-colors hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                        <div>
                            <span class="inline-block text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
                                Masa Aktif: 12 Bulan + Ekstra
                            </span>
                            <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100 mt-1.5">
                                Paket Super VIP
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Solusi komprehensif dengan pendampingan prioritas tinggi.
                            </p>

                            <div class="mt-5 pb-5 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">Rp 1.500.000</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 block mt-1">Layanan prioritas satu tahun penuh</span>
                            </div>

                            <ul class="mt-6 space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span><strong>Seluruh fasilitas</strong> Paket 1 Tahun</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Pendampingan gladi bersih via Zoom/Meet</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Bantuan konversi &amp; upload bank soal massal</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>VIP Fast-Track Customer Service</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold shrink-0">✓</span>
                                    <span>Kuitansi berstempel &amp; materai digital</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-8 pt-5 border-t border-slate-100 dark:border-slate-800 no-print">
                            <a href="{{ auth()->check() ? route('checkout.show', 'super') : route('register', ['paket' => 'super']) }}" class="btn-secondary w-full text-center text-xs py-2.5 font-bold">
                                Pilih Paket Super
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- Section 5: Dana BOS Justification Box --}}
        <section class="mt-12 sm:mt-16 print-break-inside">
            <div class="rounded-3xl border-2 border-emerald-400/80 bg-emerald-50/70 dark:bg-emerald-950/30 p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <div class="hidden sm:flex h-12 w-12 rounded-2xl bg-emerald-600 text-white items-center justify-center font-bold text-xl shrink-0">
                        🏛️
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">Kepastian Hukum &amp; Administrasi</span>
                        <h2 class="text-lg sm:text-xl font-bold text-emerald-950 dark:text-emerald-100 mt-0.5">
                            Apakah Layanan Ini Boleh Dibiayai dari Dana BOS?
                        </h2>
                        <p class="mt-2 text-xs sm:text-sm leading-relaxed text-emerald-900/90 dark:text-emerald-200">
                            <strong>YA, SANGAT DIPERBOLEHKAN.</strong> Berdasarkan <strong>Permendikbudristek No. 63 Tahun 2023</strong> tentang Petunjuk Teknis Pengelolaan Dana Bantuan Operasional Satuan Pendidikan (BOSP), langganan aplikasi Veyra Educate &amp; CBT masuk ke dalam:
                        </p>
                        <div class="mt-4 grid sm:grid-cols-2 gap-3 text-xs sm:text-sm text-emerald-950 dark:text-emerald-100">
                            <div class="rounded-xl bg-white/80 dark:bg-slate-900/80 p-3 border border-emerald-200">
                                <strong>1. Komponen Asesmen &amp; Evaluasi Pembelajaran:</strong> Pengadaan sistem ujian berbasis komputer, latihan soal digital, dan asesmen akhir semester.
                            </div>
                            <div class="rounded-xl bg-white/80 dark:bg-slate-900/80 p-3 border border-emerald-200">
                                <strong>2. Komponen Pengelolaan Sekolah Berbasis TIK:</strong> Penggunaan sistem informasi manajemen dan wadah aplikasi terpadu sekolah.
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-emerald-800 dark:text-emerald-300 italic">
                            * Setiap transaksi otomatis menghasilkan Kuitansi Resmi dan Faktur berstempel digital yang langsung bisa diunduh oleh Bendahara Sekolah untuk berkas SPJ.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 6: Official Contact & Footer --}}
        <section class="mt-12 sm:mt-16 pt-8 border-t border-slate-200 dark:border-slate-800 text-center print-break-inside">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">
                Siap Melaksanakan Asesmen Digital Tanpa Ribet?
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-xl mx-auto">
                Konsultasikan kebutuhan sekolah Anda sekarang atau minta akun uji coba gratis melalui tim konsultan kami.
            </p>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-3 no-print">
                <a
                    href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate,%20kami%20ingin%20konsultasi%20aplikasi%20ujian%20CBT%20untuk%20sekolah%20kami"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-primary inline-flex items-center gap-2 px-6 py-3 text-sm font-bold"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.7h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.74.98 1-3.64-.24-.37a9.8 9.8 0 0 1-1.5-5.23c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.03 6.96 2.89a9.78 9.78 0 0 1 2.88 6.95c0 5.42-4.41 9.83-9.83 9.83Zm5.4-7.37c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-50h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
                    </svg>
                    <span>Hubungi WhatsApp: 0857-0707-4778</span>
                </a>
                <a href="{{ url('proposal') }}" class="btn-secondary px-6 py-3 text-sm font-semibold">
                    Unduh Proposal Resmi (PDF)
                </a>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 leading-relaxed">
                <p class="font-bold text-slate-700 dark:text-slate-300">PT. VEYRA CREATIVE TECH — Divisi Veyra Educate</p>
                <p>Website: veyracreative.site • Email: veyracreativetech@gmail.com • WhatsApp: +62 857-0707-4778</p>
                <p>Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep Jawa Timur 69461</p>
            </div>
        </section>
    </main>
</x-marketing-layout>
