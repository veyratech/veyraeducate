<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $marketing_page?->title ?? 'Surat Penawaran Resmi — Veyra Educate &amp; CBT Online' }}</title>
    <meta name="description" content="{{ $marketing_page?->description ?? 'Proposal resmi untuk keperluan presentasi kepada dinas pendidikan, kepala sekolah, dan pemangku kepentingan. Format A4 siap cetak dengan branding profesional.' }}">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .paper-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 20mm 20mm 25mm 20mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
        }

        @media screen and (max-width: 220mm) {
            .paper-sheet {
                width: 95%;
                padding: 20px;
                margin: 15px auto;
            }
        }

        @media print {
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .paper-sheet {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 10mm 15mm !important;
                box-shadow: none !important;
                border: none !important;
            }
            @page {
                size: A4;
                margin: 10mm 12mm;
            }
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }
        }
    </style>
</head>
<body class="antialiased" x-data="{
    schoolTarget: new URLSearchParams(window.location.search).get('sekolah') || '',
    studentCount: 350,
    examFrequency: 4,
    subjectsCount: 10,
    pagesPerSubject: 4,
    costPerPage: 250,
    cbtYearlyCost: 1100000,
    showCalculator: false,
    formatRupiah(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    },
    get paperCostPerExam() {
        return this.studentCount * this.subjectsCount * this.pagesPerSubject * this.costPerPage;
    },
    get totalPaperCostYearly() {
        return this.paperCostPerExam * this.examFrequency;
    },
    get totalSavings() {
        return Math.max(0, this.totalPaperCostYearly - this.cbtYearlyCost);
    },
    get savingsPercent() {
        if (this.totalPaperCostYearly <= 0) return 0;
        return Math.round((this.totalSavings / this.totalPaperCostYearly) * 100);
    }
}">

    @if ($marketing_page)
        <div class="no-print py-8 bg-gradient-to-b from-brand-50 to-white dark:from-slate-900 dark:to-slate-950 border-b border-brand-100 dark:border-slate-800">
            <div class="container-site text-center max-w-2xl mx-auto px-4">
                @if ($marketing_page->eyebrow)
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-600 dark:text-brand-400 mb-3">{{ $marketing_page->eyebrow }}</p>
                @endif
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink dark:text-white leading-tight">
                    {{ $marketing_page->title }}
                </h1>
                @if ($marketing_page->description)
                    <p class="mt-3 text-sm sm:text-base text-muted dark:text-slate-400 leading-relaxed">
                        {!! nl2br(e($marketing_page->description)) !!}
                    </p>
                @endif
                @if ($marketing_page->cta_text && $marketing_page->cta_url)
                    <a href="{{ $marketing_page->cta_url }}" class="btn-primary inline-flex items-center gap-2 px-6 py-2.5 text-sm mt-5">
                        {{ $marketing_page->cta_text }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Top Action Bar (Hidden when Printing) --}}
    <div class="no-print sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-md shadow-xs">
        <div class="max-w-4xl mx-auto py-3 px-4 flex flex-wrap items-center justify-between gap-3 text-xs sm:text-sm">
            <div class="flex items-center gap-2 font-bold text-slate-800">
                <a href="{{ url('/') }}" class="text-brand-600 hover:underline flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Beranda
                </a>
                <span class="text-slate-300">|</span>
                <span>Proposal Resmi Veyra Educate</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Quick Personalization Input --}}
                <div class="flex items-center gap-1.5 bg-slate-100 rounded-xl px-2.5 py-1 border border-slate-200">
                    <span class="text-[11px] text-slate-500 font-bold">Klien:</span>
                    <input
                        type="text"
                        x-model="schoolTarget"
                        placeholder="Ketik Nama Sekolah..."
                        class="bg-white rounded-lg border border-slate-300 px-2 py-0.5 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-500 w-36 sm:w-48"
                        title="Nama sekolah akan otomatis tercetak di surat"
                    >
                </div>

                {{-- Toggle Interactive BOS Calculator --}}
                <button
                    type="button"
                    @click="showCalculator = !showCalculator"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-300 bg-brand-50 hover:bg-brand-100 text-brand-700 px-3 py-1.5 font-bold transition cursor-pointer"
                    title="Sesuaikan simulasi penghematan dana BOS sekolah"
                >
                    <span>📊</span>
                    <span x-text="showCalculator ? 'Tutup Simulasi BOS' : 'Simulasi Hemat BOS'"></span>
                </button>

                <a
                    href="{{ url('brosur') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    <svg class="h-4 w-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Brosur</span>
                </a>

                <button
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white px-3.5 py-1.5 font-bold shadow-md shadow-brand-600/20 transition cursor-pointer"
                >
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF (A4)</span>
                </button>

                <a
                    href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate,%20kami%20ingin%20mengajukan%20kerjasama%20proposal%20aplikasi%20CBT%20sekolah"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 font-semibold transition"
                >
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>

        {{-- Expanded BOS Calculator Drawer --}}
        <div x-show="showCalculator" x-transition class="border-t border-slate-200 bg-slate-50/95 py-3 px-4">
            <div class="max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jumlah Siswa:</label>
                    <input type="number" x-model.number="studentCount" min="10" max="5000" class="w-full rounded-lg border border-slate-300 px-2.5 py-1 bg-white font-bold text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ujian / Tahun:</label>
                    <select x-model.number="examFrequency" class="w-full rounded-lg border border-slate-300 px-2 py-1 bg-white font-bold text-slate-900">
                        <option :value="2">2 Kali (Semester)</option>
                        <option :value="4">4 Kali (PTS 1, PAS 1, PTS 2, PAT)</option>
                        <option :value="6">6 Kali (+ Tryout &amp; US)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mata Pelajaran / Ujian:</label>
                    <input type="number" x-model.number="subjectsCount" min="1" max="25" class="w-full rounded-lg border border-slate-300 px-2.5 py-1 bg-white font-bold text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Biaya Kertas/Lbr (Rp):</label>
                    <input type="number" x-model.number="costPerPage" min="50" max="1000" step="50" class="w-full rounded-lg border border-slate-300 px-2.5 py-1 bg-white font-bold text-slate-900">
                </div>
            </div>
        </div>
    </div>

    {{-- Official Proposal Document Paper (A4) --}}
    <div class="paper-sheet text-slate-900 text-xs sm:text-[13px] leading-relaxed">
        
        {{-- Official Letterhead / Kop Surat --}}
        <div class="border-b-4 border-double border-slate-900 pb-4 mb-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo-veyra-educate.png') }}" alt="Logo Veyra Educate" class="h-16 w-auto object-contain">
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">
                            {{ config('invoice.company_name', 'PT. VEYRA CREATIVE TECH') }}
                        </h1>
                        <p class="text-xs font-bold text-brand-700 mt-1 uppercase tracking-wider">
                            Divisi Pengembangan Sistem Informasi &amp; Aplikasi Asesmen Sekolah (Veyra Educate)
                        </p>
                        <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                            {{ config('invoice.company_address', 'Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep KodePos: 69461 • Telp/WA: +62 857-0707-4778 • Email: veyracreativetech@gmail.com') }}
                        </p>
                        <p class="text-[10px] text-slate-500 font-mono mt-0.5">Website: https://veyracreative.site • Layanan Konsultasi: 0857-0707-4778</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Meta Surat --}}
        <div class="grid grid-cols-2 gap-4 mb-5 pb-3 border-b border-slate-200 text-xs">
            <div>
                <table class="w-full text-left">
                    <tr>
                        <td class="w-20 font-semibold text-slate-600">Nomor</td>
                        <td class="w-3">:</td>
                        <td class="font-bold text-slate-900 font-mono">018/EXT/VEYRA/EDU/X/{{ date('Y') }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-slate-600">Sifat</td>
                        <td>:</td>
                        <td>Penting / Penawaran Kerjasama</td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-slate-600">Lampiran</td>
                        <td>:</td>
                        <td>1 (Satu) Berkas Rincian Layanan &amp; Anggaran</td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-slate-600">Perihal</td>
                        <td>:</td>
                        <td class="font-bold text-brand-700">Penawaran Digitalisasi Ujian Sekolah Berbasis Komputer &amp; Portal Veyra Educate</td>
                    </tr>
                </table>
            </div>
            <div class="text-right">
                <p class="font-medium text-slate-600">
                    {{ config('invoice.signer_city', 'Sumenep') }}, {{ now()->translatedFormat('d F Y') }}
                </p>
                <div class="mt-2 text-left inline-block">
                    <p class="font-semibold text-slate-700">Kepada Yth.</p>
                    <p class="font-bold text-slate-900 text-sm">
                        Bapak/Ibu Kepala Sekolah
                        <span x-show="schoolTarget.trim() !== ''" x-text="schoolTarget" class="text-brand-700 font-extrabold"></span>
                    </p>
                    <p class="text-slate-600">dan Tim Pengembang Kurikulum / Bendahara Sekolah</p>
                    <p class="text-slate-500 italic">di Tempat</p>
                </div>
            </div>
        </div>

        {{-- Isi Surat --}}
        <div class="space-y-3.5 text-justify">
            <p>
                Dengan hormat,
            </p>
            <p>
                Seiring dengan perkembangan teknologi informasi dan percepatan digitalisasi pendidikan dalam rangka implementasi Kurikulum Merdeka, efisiensi operasional pelaksanaan asesmen pembelajaran serta transparansi akuntabilitas anggaran menjadi faktor penting bagi keberhasilan satuan pendidikan.
            </p>
            <p>
                Melalui surat penawaran ini, kami dari <strong>PT. Veyra Creative Tech</strong> memperkenalkan ekosistem <strong>Veyra Educate &amp; Veyra CBT Online</strong>, yaitu platform terintegrasi untuk menyelenggarakan Asesmen Sumatif Tengah Semester (STS), Asesmen Sumatif Akhir Semester (SAS), Ujian Sekolah, hingga Asesmen Madrasah secara digital, bebas kertas, dan aman dari kecurangan.
            </p>

            {{-- 1. Keunggulan Sistem --}}
            <div class="pt-2">
                <h2 class="font-bold text-slate-900 text-sm border-l-4 border-brand-600 pl-2.5 mb-2">
                    I. KEUNGGULAN UTAMA SISTEM VEYRA CBT ONLINE
                </h2>
                <ul class="list-disc list-outside pl-5 space-y-1.5 text-slate-800">
                    <li>
                        <strong>100% Berbasis Cloud (Tanpa Server Fisik):</strong> Sekolah tidak perlu mengalokasikan anggaran jutaan rupiah untuk pengadaan PC server lokal, instalasi VirtualBox/VHD, maupun konfigurasi kabel jaringan LAN yang rumit. Sistem siap diakses langsung melalui internet.
                    </li>
                    <li>
                        <strong>Dukungan Multi-Perangkat (Smartphone, Tablet, PC Lab):</strong> Siswa dapat mengikuti ujian dengan lancar menggunakan ponsel pintar pribadi maupun fasilitas komputer sekolah tanpa perlu mengunduh aplikasi tambahan.
                    </li>
                    <li>
                        <strong>Sistem Pengamanan Ujian Canggih:</strong> Dilengkapi fitur pengacakan nomor soal, pengacakan pilihan jawaban (A, B, C, D, E), token ujian dinamis, dan pengawasan navigasi layar siswa untuk meminimalisasi kecurangan.
                    </li>
                    <li>
                        <strong>Koreksi &amp; Rekapitulasi Nilai Otomatis:</strong> Nilai siswa terhitung secara instan seketika ujian diselesaikan, serta dapat diekspor langsung ke lembar kerja Microsoft Excel untuk pengisian rapor.
                    </li>
                    <li>
                        <strong>Kemudahan Pengelolaan Bank Soal:</strong> Memfasilitasi import soal massal melalui template Microsoft Word dan Excel, mendukung berbagai format soal (pilihan ganda, gambar, audio, dan uraian).
                    </li>
                </ul>
            </div>

            {{-- 2. Rincian Paket Harga --}}
            <div class="pt-2">
                <h2 class="font-bold text-slate-900 text-sm border-l-4 border-brand-600 pl-2.5 mb-2">
                    II. RINCIAN BIAYA LANGGANAN SEKOLAH
                </h2>
                <p class="mb-2">
                    Kami menawarkan skema investasi berlangganan yang transparan dan sangat terjangkau bagi kas sekolah:
                </p>
                <table class="w-full border-collapse border border-slate-300 text-xs text-left mb-2">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 font-bold">
                            <th class="border border-slate-300 px-3 py-2">Nama Paket</th>
                            <th class="border border-slate-300 px-3 py-2">Masa Berlaku</th>
                            <th class="border border-slate-300 px-3 py-2">Biaya Investasi</th>
                            <th class="border border-slate-300 px-3 py-2">Fasilitas &amp; Cakupan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-bold text-slate-900">Paket Semesteran</td>
                            <td class="border border-slate-300 px-3 py-2">6 Bulan (1 Semester)</td>
                            <td class="border border-slate-300 px-3 py-2 font-extrabold text-slate-900">Rp 600.000,-</td>
                            <td class="border border-slate-300 px-3 py-2">Akses penuh CBT, akun guru &amp; siswa tanpa batas, rekap Excel, bantuan via WhatsApp.</td>
                        </tr>
                        <tr class="bg-brand-50/50">
                            <td class="border border-slate-300 px-3 py-2 font-bold text-brand-700">Paket 1 Tahun Penuh (Rekomendasi)</td>
                            <td class="border border-slate-300 px-3 py-2 font-semibold">12 Bulan Penuh</td>
                            <td class="border border-slate-300 px-3 py-2 font-extrabold text-brand-700">Rp 1.100.000,-</td>
                            <td class="border border-slate-300 px-3 py-2">Semua fitur paket semester, mencakup seluruh STS, SAS, &amp; Ujian Akhir, prioritas support WA.</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-bold text-slate-900">Paket Super VIP</td>
                            <td class="border border-slate-300 px-3 py-2">12 Bulan + Ekstra</td>
                            <td class="border border-slate-300 px-3 py-2 font-extrabold text-slate-900">Rp 1.500.000,-</td>
                            <td class="border border-slate-300 px-3 py-2">Seluruh fitur 1 tahun, pendampingan gladi bersih via Zoom, bantuan konversi bank soal.</td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-[11px] text-slate-500 italic mb-3">
                    * Tarif di atas sudah mencakup biaya pemeliharaan server, pembaruan aplikasi, dan pendampingan teknis operator sekolah tanpa ada pungutan biaya tambahan.
                </p>

                {{-- Simulasi Komparasi Efisiensi BOS --}}
                <div class="bg-slate-50/90 p-3 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="text-brand-600">●</span> Simulasi Efisiensi Anggaran BOS (Basis: <span x-text="studentCount"></span> Siswa)
                        </h3>
                        <span class="text-[11px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
                            Efisiensi: <span x-text="savingsPercent"></span>%
                        </span>
                    </div>
                    <table class="w-full border-collapse border border-slate-300 text-[11px] text-left">
                        <thead>
                            <tr class="bg-slate-200/80 font-bold text-slate-800">
                                <th class="border border-slate-300 px-2.5 py-1.5">Komponen Evaluasi Pembelajaran</th>
                                <th class="border border-slate-300 px-2.5 py-1.5">Metode Kertas Tradisional</th>
                                <th class="border border-slate-300 px-2.5 py-1.5 bg-brand-100 text-brand-900">Veyra CBT &amp; Educate Cloud</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-300 px-2.5 py-1 font-semibold text-slate-700">Pengadaan Lembar Soal &amp; LJK</td>
                                <td class="border border-slate-300 px-2.5 py-1 text-rose-700 font-bold" x-text="formatRupiah(totalPaperCostYearly) + ' / thn'"></td>
                                <td class="border border-slate-300 px-2.5 py-1 text-emerald-700 font-bold bg-brand-50/40">Rp 0,- (100% Paperless)</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-2.5 py-1 font-semibold text-slate-700">Biaya Investasi Sistem</td>
                                <td class="border border-slate-300 px-2.5 py-1 text-slate-500 font-mono">Beban operasional berulang</td>
                                <td class="border border-slate-300 px-2.5 py-1 text-brand-700 font-extrabold bg-brand-50/40">Rp 1.100.000,- / tahun</td>
                            </tr>
                            <tr class="bg-emerald-50/80 font-bold">
                                <td class="border border-slate-300 px-2.5 py-1.5 text-slate-900">Total Penghematan Anggaran BOS</td>
                                <td class="border border-slate-300 px-2.5 py-1.5 text-slate-400 font-mono">-</td>
                                <td class="border border-slate-300 px-2.5 py-1.5 text-emerald-800 font-black text-xs">
                                    Hemat <span x-text="formatRupiah(totalSavings)"></span> (<span x-text="savingsPercent"></span>%)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-[10px] text-slate-500 mt-1.5 italic">
                        * Simulasi dihitung berdasarkan <span x-text="studentCount"></span> siswa, <span x-text="examFrequency"></span> agenda asesmen per tahun, <span x-text="subjectsCount"></span> mapel, dan estimasi fotokopi Rp <span x-text="costPerPage"></span>/lembar.
                    </p>
                </div>
            </div>

            {{-- 3. Legalitas Dana BOS --}}
            <div class="pt-2">
                <h2 class="font-bold text-slate-900 text-sm border-l-4 border-brand-600 pl-2.5 mb-2">
                    III. KESESUAIAN PENGANGGARAN DANA BOS (BOSP)
                </h2>
                <p>
                    Pengadaan langganan aplikasi ini sah dan dapat dianggarkan melalui <strong>Dana Bantuan Operasional Satuan Pendidikan (BOSP / Dana BOS)</strong> sebagaimana diatur dalam <strong>Permendikbudristek No. 63 Tahun 2023</strong>:
                </p>
                <ul class="list-disc list-outside pl-5 space-y-1 text-slate-700 text-xs">
                    <li><strong>Komponen Pelaksanaan Asesmen &amp; Evaluasi Pembelajaran:</strong> Pembiayaan pengadaan sistem ujian berbasis komputer, penilaian sumatif, dan ujian sekolah.</li>
                    <li><strong>Komponen Pengelolaan Sekolah Berbasis TIK:</strong> Penggunaan aplikasi sistem informasi dan administrasi evaluasi hasil belajar.</li>
                </ul>
                <p class="mt-1.5 text-xs text-slate-800">
                    Untuk menjamin akuntabilitas SPJ, sistem kami secara otomatis menerbitkan <strong>Kwitansi Resmi</strong> bertanda tangan digital dan <strong>Faktur Transaksi</strong> resmi yang siap dilampirkan oleh Bendahara Sekolah.
                </p>
            </div>

            {{-- 4. Alur Implementasi --}}
            <div class="pt-2">
                <h2 class="font-bold text-slate-900 text-sm border-l-4 border-brand-600 pl-2.5 mb-2">
                    IV. ALUR PENGAJUAN &amp; UJI COBA GRATIS
                </h2>
                <p>
                    Kami menyediakan kesempatan <strong>Uji Coba Gratis (Free Trial)</strong> bagi sekolah Bapak/Ibu sebelum melakukan komitmen langganan. Tim teknis kami siap memandu operator sekolah dalam melakukan simulasi gladi bersih hingga seluruh alur ujian berjalan lancar.
                </p>
            </div>

            {{-- Penutup --}}
            <p class="pt-1">
                Demikian surat penawaran ini kami sampaikan. Besar harapan kami dapat menjalin kerjasama yang berkelanjutan guna mewujudkan modernisasi dan efisiensi sistem penilaian belajar di sekolah yang Bapak/Ibu pimpin. Atas perhatian dan kesempatan yang diberikan, kami haturkan terima kasih.
            </p>
        </div>

        {{-- Kolom Tanda Tangan & Stempel Resmi --}}
        <div class="mt-8 pt-4 border-t border-slate-200 grid grid-cols-2 gap-8 items-end text-xs">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Layanan Informasi &amp; Onboarding</p>
                <div class="rounded-xl border border-slate-200 p-3 bg-slate-50 space-y-1 text-[11px] text-slate-700">
                    <p><strong>Hotline WhatsApp:</strong> +62 857-0707-4778</p>
                    <p><strong>Email Layanan:</strong> veyracreativetech@gmail.com</p>
                    <p><strong>Portal Registrasi:</strong> https://veyracreative.site</p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-slate-600">
                    {{ config('invoice.signer_city', 'Sumenep') }}, {{ now()->translatedFormat('d F Y') }}
                </p>
                <p class="font-bold text-slate-900 mt-0.5">
                    {{ config('invoice.company_name', 'PT. VEYRA CREATIVE TECH') }}
                </p>
                <p class="text-[11px] text-slate-500">
                    {{ config('invoice.signer_department', 'Bagian Keuangan & Penagihan') }}
                </p>

                {{-- Digital Stamp & Signature Box --}}
                <div class="py-2.5 flex justify-end">
                    <div class="border-2 border-dashed border-emerald-600 bg-emerald-50/60 rounded-xl px-4 py-1.5 text-center shadow-xs">
                        <span class="block text-[8px] font-black text-emerald-800 tracking-wider">OFFICIALLY SIGNED &amp; VERIFIED</span>
                        <span class="font-serif text-base text-slate-900 font-bold italic tracking-wide">
                            {{ config('invoice.signer_name', 'Ahmad Fauzi, S.Kom., M.T.') }}
                        </span>
                        <span class="block text-[8px] text-emerald-700 font-mono">DOKUMEN RESMI PT. VEYRA CREATIVE TECH</span>
                    </div>
                </div>

                <p class="font-extrabold text-slate-900 underline text-xs">
                    {{ config('invoice.signer_name', 'Ahmad Fauzi, S.Kom., M.T.') }}
                </p>
                <p class="text-[11px] text-slate-600">
                    {{ config('invoice.signer_title', 'Finance & Licensing Manager') }}
                </p>
            </div>
        </div>

        {{-- Footer Catatan Dokumen Elektronik --}}
        <div class="mt-8 pt-3 border-t border-slate-100 text-[10px] text-slate-400 text-center">
            Dokumen penawaran ini merupakan surat resmi elektronik yang diterbitkan oleh PT. Veyra Creative Tech melalui sistem portal Veyra Educate.
        </div>
    </div>

</body>
</html>
