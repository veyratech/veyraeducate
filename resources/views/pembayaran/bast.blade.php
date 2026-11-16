@php
    if (!function_exists('penyebutRupiahBast')) {
        function penyebutRupiahBast($nilai) {
            $nilai = abs((int) $nilai);
            $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
            if ($nilai < 12) return " " . $huruf[$nilai];
            if ($nilai < 20) return penyebutRupiahBast($nilai - 10) . " belas";
            if ($nilai < 100) return penyebutRupiahBast((int)($nilai / 10)) . " puluh" . penyebutRupiahBast($nilai % 10);
            if ($nilai < 200) return " seratus" . penyebutRupiahBast($nilai - 100);
            if ($nilai < 1000) return penyebutRupiahBast((int)($nilai / 100)) . " ratus" . penyebutRupiahBast($nilai % 100);
            if ($nilai < 2000) return " seribu" . penyebutRupiahBast($nilai - 1000);
            if ($nilai < 1000000) return penyebutRupiahBast((int)($nilai / 1000)) . " ribu" . penyebutRupiahBast($nilai % 1000);
            if ($nilai < 1000000000) return penyebutRupiahBast((int)($nilai / 1000000)) . " juta" . penyebutRupiahBast($nilai % 1000000);
            return (string) $nilai;
        }
    }

    $amountWords = trim(ucwords(penyebutRupiahBast($order->amount_idr ?? 0))) . ' Rupiah';
    $bastNumber = 'BAST/' . date('Y') . '/BOSP/' . strtoupper(substr(md5($order->order_id), 0, 8));
    $orderDate = optional($order->paid_at) ?? optional($order->created_at) ?? now();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Acara Serah Terima (BAST SPJ BOS) #{{ $order->order_id }} — Veyra Educate</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: #0f172a !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11pt !important;
            }
            .print-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans py-4 sm:py-8 px-2 sm:px-4">

    {{-- Top Action Bar (Screen Only) --}}
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2">
            <a
                href="{{ route('pembayaran.invoice', $order->order_id) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Buka Kwitansi / Invoice</span>
            </a>
            <span class="text-xs text-slate-400">|</span>
            <span class="text-xs font-bold text-slate-600">Dokumen Lampiran SPJ Dana BOS</span>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 text-white text-xs sm:text-sm font-bold shadow-sm hover:bg-brand-700 transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan PDF (A4)</span>
            </button>
        </div>
    </div>

    {{-- Main Printable Sheet --}}
    <main class="print-sheet max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-12 text-slate-800">
        
        {{-- Formal Kop Surat Perusahaan --}}
        <div class="border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-14 w-14 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-black text-2xl shadow-sm">
                        V
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight">
                            {{ config('invoice.company_name', 'PT. VEYRA CREATIVE TECH') }}
                        </h1>
                        <p class="text-xs text-slate-600 font-medium">
                            Penyedia Solusi Teknologi Informasi &amp; Digitalisasi Asesmen Sekolah Indonesia
                        </p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            {{ config('invoice.company_address', 'Jl. Raya Gapura, Sumenep, Jawa Timur') }} &bull; CS WhatsApp: 0857-0707-4778
                        </p>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-300 rounded-lg text-[10px] font-extrabold uppercase">
                        Lampiran SPJ BOS
                    </span>
                    <p class="text-[10px] text-slate-400 font-mono mt-1">ID: {{ substr($order->order_id, 0, 16) }}</p>
                </div>
            </div>
        </div>

        {{-- BAST Title & Number --}}
        <div class="text-center my-6">
            <h2 class="text-base sm:text-lg font-black text-slate-900 uppercase underline tracking-wide">
                BERITA ACARA SERAH TERIMA (BAST)
            </h2>
            <p class="text-xs font-bold text-slate-700 mt-1">
                PENGADAAN LISENSI APLIKASI ASESMEN &amp; CBT ONLINE SEKOLAH
            </p>
            <p class="text-xs font-mono text-slate-500 mt-0.5">
                Nomor: {{ $bastNumber }}
            </p>
        </div>

        {{-- Narration --}}
        <div class="text-xs sm:text-sm leading-relaxed text-slate-700 space-y-4">
            <p>
                Pada hari ini, tanggal <strong>{{ $orderDate->translatedFormat('d F Y') }}</strong>, bertempat di lingkungan sekolah penerima manfaat anggaran Bantuan Operasional Satuan Pendidikan (BOSP), yang bertanda tangan di bawah ini:
            </p>

            {{-- Pihak Pertama --}}
            <div class="pl-4 border-l-2 border-brand-500 space-y-1 text-xs sm:text-sm">
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">1. Nama Penyedia</span>
                    <span class="col-span-8 sm:col-span-9 font-semibold">: {{ config('invoice.signer_name', 'M. Fajar Sidiq') }}</span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</span>
                    <span class="col-span-8 sm:col-span-9">: {{ config('invoice.signer_title', 'Finance & Licensing Manager') }} {{ config('invoice.company_name', 'PT. Veyra Creative Tech') }}</span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">&nbsp;&nbsp;&nbsp;&nbsp;Alamat</span>
                    <span class="col-span-8 sm:col-span-9">: {{ config('invoice.company_address', 'Jl. Raya Gapura, Sumenep, Jawa Timur') }}</span>
                </div>
                <p class="text-[11px] text-slate-500 italic mt-1">
                    Selanjutnya dalam Berita Acara ini disebut sebagai <strong>PIHAK PERTAMA (Penyedia Jasa &amp; Lisensi)</strong>.
                </p>
            </div>

            {{-- Pihak Kedua --}}
            <div class="pl-4 border-l-2 border-emerald-500 space-y-1 text-xs sm:text-sm">
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">2. Nama Lembaga</span>
                    <span class="col-span-8 sm:col-span-9 font-semibold">: {{ $order->school?->name ?? 'Lembaga Pendidikan' }}</span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">&nbsp;&nbsp;&nbsp;&nbsp;NPSN / NSM</span>
                    <span class="col-span-8 sm:col-span-9">: {{ $order->school?->npsn ?? $order->school?->meta['nsm'] ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">&nbsp;&nbsp;&nbsp;&nbsp;Nama Pengguna / PIC</span>
                    <span class="col-span-8 sm:col-span-9">: {{ $order->user?->name ?? 'Kepala Sekolah / Bendahara' }}</span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-4 sm:col-span-3 font-bold text-slate-800">&nbsp;&nbsp;&nbsp;&nbsp;Alamat Sekolah</span>
                    <span class="col-span-8 sm:col-span-9">: {{ $order->school?->address ?? 'Sesuai database Dapodik/EMIS Sekolah' }}</span>
                </div>
                <p class="text-[11px] text-slate-500 italic mt-1">
                    Selanjutnya dalam Berita Acara ini disebut sebagai <strong>PIHAK KEDUA (Kepala Sekolah / Kuasa Pengguna Anggaran)</strong>.
                </p>
            </div>

            <p class="pt-2">
                Berdasarkan <strong>Ketentuan Petunjuk Teknis Pengelolaan Dana Bantuan Operasional Satuan Pendidikan (BOSP) Kemendikbudristek RI</strong> perihal Pembiayaan Asesmen Pembelajaran Siswa, kedua belah pihak menyatakan sepakat:
            </p>

            {{-- Detail Spesifikasi Barang / Jasa --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                <p class="font-bold text-xs uppercase tracking-wider text-slate-700 mb-2">
                    Rincian Layanan &amp; Lisensi yang Diserahterimakan:
                </p>
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="border-b border-slate-300 text-slate-600 font-bold">
                            <th class="py-1.5">No</th>
                            <th class="py-1.5">Uraian Pekerjaan / Lisensi Cloud</th>
                            <th class="py-1.5">Periode Masa Aktif</th>
                            <th class="py-1.5 text-right">Nilai Tagihan (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-2.5 font-bold">1</td>
                            <td class="py-2.5">
                                <p class="font-bold text-slate-900">Langganan Aplikasi Cloud Veyra CBT — Paket {{ $order->plan?->label ?? 'Sekolah' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Mencakup lisensi server multi-tenant, akses guru pembuat soal, ujian peserta tanpa batas, listening audio test, cetak kartu ujian siswa, dan ekspor nilai otomatis ke Excel.
                                </p>
                            </td>
                            <td class="py-2.5 font-medium text-slate-700">
                                @if ($order->subscription?->ends_at)
                                    {{ optional($order->subscription->starts_at)->format('d/m/Y') }} s/d {{ optional($order->subscription->ends_at)->format('d/m/Y') }}
                                @else
                                    Sesuai Paket
                                @endif
                            </td>
                            <td class="py-2.5 text-right font-bold text-slate-900">
                                {{ $order->formatAmount() }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-800 font-black">
                            <td colspan="3" class="py-2 text-right uppercase text-slate-800">Total Biaya Pengadaan:</td>
                            <td class="py-2 text-right text-brand-700">{{ $order->formatAmount() }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-[11px] italic text-slate-600">
                Terbilang: <strong># {{ $amountWords }} #</strong> (Status: Telah Dibayar Lunas).
            </p>

            {{-- Klausul Pernyataan --}}
            <div class="space-y-2 text-xs sm:text-sm text-slate-700 pt-1">
                <p>
                    <strong>PASAL 1:</strong> PIHAK PERTAMA telah menyerahkan seluruh akses login, lisensi perangkat lunak, panduan teknis, dan database aktif aplikasi CBT kepada PIHAK KEDUA dalam kondisi baik, lengkap, dan dapat dioperasikan secara penuh.
                </p>
                <p>
                    <strong>PASAL 2:</strong> PIHAK KEDUA telah memeriksa, menguji, dan menerima seluruh fasilitas perangkat lunak sebagaimana dimaksud dalam rincian di atas dengan hasil <strong>BAIK DAN BERFUNGSI SEMPURNA</strong>.
                </p>
                <p>
                    Demikian Berita Acara Serah Terima ini dibuat dalam rangkap yang memiliki kekuatan hukum yang sama untuk dipergunakan sebagaimana mestinya sebagai bukti sah kelengkapan Surat Pertanggungjawaban (SPJ) Dana BOS / BOP Sekolah.
                </p>
            </div>
        </div>

        {{-- Signatures Area --}}
        <div class="grid grid-cols-2 gap-8 items-start pt-10 mt-8 border-t border-slate-200 text-xs sm:text-sm">
            {{-- Pihak Kedua (Sekolah) --}}
            <div class="text-center space-y-1">
                <p class="font-bold text-slate-800 uppercase">PIHAK KEDUA</p>
                <p class="text-xs text-slate-500">Kepala Sekolah / Penanggung Jawab BOS</p>
                <p class="text-xs font-semibold text-slate-800">{{ $order->school?->name ?? 'Lembaga Pendidikan' }}</p>

                <div class="h-24 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic border border-dashed border-slate-200 rounded-lg px-3 py-1">
                        [Tanda Tangan &amp; Stempel Sekolah]
                    </span>
                </div>

                <p class="font-black text-slate-900 underline">
                    ( {{ $order->user?->name ?? '....................................................' }} )
                </p>
                <p class="text-[11px] text-slate-500">
                    NIP. ....................................................
                </p>
            </div>

            {{-- Pihak Pertama (Veyra) --}}
            <div class="text-center space-y-1">
                <p class="font-bold text-slate-800 uppercase">PIHAK PERTAMA</p>
                <p class="text-xs text-slate-500">Penyedia Jasa &amp; Lisensi Cloud</p>
                <p class="text-xs font-semibold text-slate-800">{{ config('invoice.company_name', 'PT. VEYRA CREATIVE TECH') }}</p>

                <div class="h-24 flex items-center justify-center">
                    <div class="border border-dashed border-emerald-400 bg-emerald-50/70 rounded-xl px-4 py-2 text-center shadow-xs">
                        <span class="block text-[8px] font-bold text-emerald-800 tracking-wider">OFFICIALLY SIGNED</span>
                        <span class="text-xs font-serif text-slate-800 font-bold italic">{{ config('invoice.signer_name', 'M. Fajar Sidiq') }}</span>
                        <span class="block text-[8px] text-slate-400 font-mono">HASH: {{ substr(md5($order->order_id), 0, 10) }}</span>
                    </div>
                </div>

                <p class="font-black text-slate-900 underline">
                    ( {{ config('invoice.signer_name', 'M. Fajar Sidiq') }} )
                </p>
                <p class="text-[11px] text-slate-500">
                    {{ config('invoice.signer_title', 'Finance & Licensing Manager') }}
                </p>
            </div>
        </div>

        {{-- Verification Footer --}}
        <div class="mt-8 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-[10px] text-slate-400 gap-2">
            <span>Dicetak secara elektronik melalui sistem Veyra Educate pada {{ now()->translatedFormat('d F Y H:i') }} WIB</span>
            <span class="font-mono">VALIDASI DOKUMEN: {{ strtoupper(substr(sha1($order->order_id . 'BAST'), 0, 16)) }}</span>
        </div>
    </main>

</body>
</html>
