@php
    if (!function_exists('penyebutRupiah')) {
        function penyebutRupiah($nilai) {
            $nilai = abs((int) $nilai);
            $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
            if ($nilai < 12) return " " . $huruf[$nilai];
            if ($nilai < 20) return penyebutRupiah($nilai - 10) . " belas";
            if ($nilai < 100) return penyebutRupiah((int)($nilai / 10)) . " puluh" . penyebutRupiah($nilai % 10);
            if ($nilai < 200) return " seratus" . penyebutRupiah($nilai - 100);
            if ($nilai < 1000) return penyebutRupiah((int)($nilai / 100)) . " ratus" . penyebutRupiah($nilai % 100);
            if ($nilai < 2000) return " seribu" . penyebutRupiah($nilai - 1000);
            if ($nilai < 1000000) return penyebutRupiah((int)($nilai / 1000)) . " ribu" . penyebutRupiah($nilai % 1000);
            if ($nilai < 1000000000) return penyebutRupiah((int)($nilai / 1000000)) . " juta" . penyebutRupiah($nilai % 1000000);
            return (string) $nilai;
        }
    }

    $amountWords = trim(ucwords(penyebutRupiah($order->amount_idr ?? 0))) . ' Rupiah';
    $invoiceNumber = 'INV/' . (optional($order->paid_at)->format('Ym') ?? date('Ym')) . '/' . $order->order_id;
    $isPaid = $order->status->value === 'paid';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi & Invoice Resmi #{{ $order->order_id }} — Veyra Educate</title>
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
                href="{{ route('pembayaran.show', $order->order_id) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>
            <span class="text-xs text-slate-400">|</span>
            <span class="text-xs font-bold text-slate-600">Dokumen Bukti Transaksi Resmi</span>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('pembayaran.bast', $order->order_id) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition shadow-xs"
            >
                <svg class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Cetak BAST (SPJ BOS) &rarr;</span>
            </a>
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-600/20 transition cursor-pointer"
                style="color: #ffffff !important;"
            >
                <svg class="h-4 w-4 text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span class="text-white" style="color: #ffffff !important;">Cetak / Simpan PDF (Ctrl + P)</span>
            </button>
        </div>
    </div>

    {{-- Official A4 Printable Invoice Sheet --}}
    <div class="print-sheet max-w-4xl mx-auto bg-white p-6 sm:p-12 rounded-3xl shadow-xl border border-slate-200/80 relative">
        
        {{-- Official Header / Kop Surat --}}
        <div class="border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('images/logo-veyra-educate.png') }}" alt="Logo Veyra" class="h-14 w-auto object-contain">
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-slate-900 leading-none">
                            {{ config('invoice.company_name', 'PT. VEYRA CREATIVE TECH') }}
                        </h1>
                        <p class="text-xs font-bold text-brand-700 mt-1 uppercase tracking-wider">
                            {{ config('invoice.company_sub', 'Penyedia Layanan Sistem Informasi & Wadah Aplikasi Sekolah Terpadu (Veyra Educate)') }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 leading-tight">
                            {{ config('invoice.company_address', 'Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep KodePos: 69461 • Telp/WA: +62 857-0707-4778 • Email: veyracreativetech@gmail.com') }}
                        </p>
                    </div>
                </div>

                {{-- Status Stamp --}}
                <div class="shrink-0 text-right">
                    @if ($isPaid)
                        <div class="inline-block border-4 border-emerald-600 px-3.5 py-1 rounded-xl transform rotate-[-6deg] shadow-xs">
                            <span class="block text-base sm:text-lg font-black tracking-widest text-emerald-600 uppercase">
                                LUNAS / PAID
                            </span>
                            <span class="block text-[9px] font-bold text-emerald-700 tracking-wider">
                                VERIFIED BY VEYRA
                            </span>
                        </div>
                    @else
                        <div class="inline-block border-4 border-amber-600 px-3.5 py-1 rounded-xl transform rotate-[-6deg] shadow-xs">
                            <span class="block text-base sm:text-lg font-black tracking-widest text-amber-600 uppercase">
                                {{ strtoupper($order->status->value) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Document Title --}}
        <div class="text-center my-6">
            <h2 class="text-base sm:text-lg font-black uppercase tracking-wider text-slate-900">
                KWITANSI PEMBAYARAN RESMI &amp; BUKTI TRANSAKSI
            </h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Dokumen Sah Penerimaan Biaya Lisensi Layanan Cloud Sekolah (Dapat Digunakan untuk LPJ BOS / Yayasan)
            </p>
        </div>

        {{-- Metadata Info Grid (2 Columns) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 p-4 sm:p-5 rounded-2xl border border-slate-200/80 mb-6 text-xs">
            {{-- Left Col: Transaction Details --}}
            <div class="space-y-1.5">
                <p class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Data Transaksi</p>
                <div class="flex justify-between border-b border-slate-200/60 pb-1">
                    <span class="text-slate-500">Nomor Invoice:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $invoiceNumber }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-1">
                    <span class="text-slate-500">Order ID:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $order->order_id }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-1">
                    <span class="text-slate-500">Tanggal Bayar:</span>
                    <span class="font-bold text-slate-900">
                        {{ (optional($order->paid_at) ?? optional($order->created_at))->translatedFormat('d F Y, H:i') }} WIB
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-semibold text-slate-900">
                        {{ strtoupper(str_replace('_', ' ', $order->payment_method ?? 'Payment Gateway Midtrans / VA')) }}
                    </span>
                </div>
            </div>

            {{-- Right Col: School Details --}}
            <div class="space-y-1.5">
                <p class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Diterima Dari (Lembaga / Sekolah)</p>
                <div class="flex justify-between border-b border-slate-200/60 pb-1">
                    <span class="text-slate-500">Nama Sekolah:</span>
                    <span class="font-extrabold text-slate-900 text-right">{{ $order->school?->name ?? 'Sekolah Mitra' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-1">
                    <span class="text-slate-500">NPSN / NSM:</span>
                    <span class="font-mono font-semibold text-slate-900">{{ $order->school?->npsn ?: '—' }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-1">
                    <span class="text-slate-500">Wilayah / Kota:</span>
                    <span class="font-semibold text-slate-900">{{ $order->school?->city ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Penanggung Jawab:</span>
                    <span class="font-medium text-slate-800 text-right">{{ $order->user?->name ?? 'Operator' }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Item Details Table --}}
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold">
                        <th class="py-2.5 px-3 rounded-l-xl">No.</th>
                        <th class="py-2.5 px-3">Uraian / Deskripsi Layanan</th>
                        <th class="py-2.5 px-3">Masa Berlaku</th>
                        <th class="py-2.5 px-3 text-right rounded-r-xl">Jumlah (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    <tr>
                        <td class="py-3 px-3 font-bold">1.</td>
                        <td class="py-3 px-3">
                            <p class="font-bold text-slate-900">
                                Lisensi Akses Cloud Veyra Educate — Paket {{ $order->plan?->label ?? 'Sekolah' }}
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Akses penuh aplikasi CBT &amp; Ujian Online, E-Rapor Kurikulum Merdeka, Presensi Siswa/Guru QR, dan penyimpanan data terpusat.
                            </p>
                        </td>
                        <td class="py-3 px-3 font-medium">
                            @if ($order->subscription?->ends_at)
                                {{ optional($order->subscription->starts_at)->format('d M Y') }} s/d {{ optional($order->subscription->ends_at)->format('d M Y') }}
                            @else
                                Sesuai Ketentuan Paket
                            @endif
                        </td>
                        <td class="py-3 px-3 text-right font-bold text-slate-900">
                            {{ $order->formatAmount() }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-900">
                        <td colspan="3" class="py-3 px-3 text-right font-extrabold uppercase text-slate-800">
                            Total Pembayaran Lunas
                        </td>
                        <td class="py-3 px-3 text-right font-black text-sm text-brand-700">
                            {{ $order->formatAmount() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Terbilang Box --}}
        <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-2xl p-3.5 mb-8 text-xs">
            <span class="font-bold text-emerald-900 uppercase text-[10px] block">Jumlah Terbilang:</span>
            <span class="italic font-bold text-emerald-800 mt-0.5 block text-sm">
                # {{ $amountWords }} #
            </span>
        </div>

        {{-- Signatures & Verification Stamp --}}
        <div class="grid grid-cols-2 gap-8 items-end pt-4 border-t border-slate-200 text-xs">
            {{-- Left: Digital QR Verification --}}
            <div class="space-y-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Verifikasi Keaslian Dokumen</p>
                <div class="flex items-center gap-3">
                    <div class="h-16 w-16 bg-slate-100 border border-slate-300 rounded-xl flex items-center justify-center p-1 shrink-0">
                        <svg class="h-12 w-12 text-slate-800" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-1h-2v2h2v-2zm-4 0h-2v2h2v-2zm2 4h-2v2h2v-2zm4-4h2v2h-2v-2zm0 4h2v2h-2v-2zm-4 2h-2v2h2v-2z"/>
                        </svg>
                    </div>
                    <div class="text-[10px] text-slate-500 leading-tight">
                        <p class="font-bold text-slate-800">Dokumen Sah Terverifikasi</p>
                        <p>ID Transaksi: <span class="font-mono">{{ substr($order->order_id, 0, 16) }}</span></p>
                        <p class="text-emerald-600 font-semibold mt-0.5">Status: SAH &amp; TERDAFTAR SISTEM</p>
                    </div>
                </div>
            </div>

            {{-- Right: Authorized Signature --}}
            <div class="text-right space-y-1">
                <p class="text-slate-500">
                    {{ config('invoice.signer_city', 'Sumenep') }}, {{ (optional($order->paid_at) ?? optional($order->created_at) ?? now())->translatedFormat('d F Y') }}
                </p>
                <p class="font-bold text-slate-800">
                    {{ config('invoice.signer_department', 'Bagian Keuangan & Penagihan') }}
                </p>
                <p class="text-[11px] text-slate-500">
                    {{ config('invoice.company_name', 'PT. Veyra Creative Tech') }}
                </p>

                {{-- Digital Signature & Stamp --}}
                <div class="py-2 flex justify-end">
                    <div class="inline-block border border-dashed border-emerald-400 bg-emerald-50/50 rounded-xl px-4 py-1 text-center">
                        <span class="block text-[9px] font-bold text-emerald-800">SIGNED DIGITALLY</span>
                        <span class="font-script text-base text-slate-800 font-bold italic">{{ config('invoice.signer_name', 'Finance Manager') }}</span>
                        <span class="block text-[8px] text-slate-400 font-mono">HASH: {{ substr(md5($order->order_id), 0, 10) }}</span>
                    </div>
                </div>

                <p class="font-extrabold text-slate-900 underline text-xs">
                    {{ config('invoice.signer_name', 'Finance Manager') }}
                </p>
                <p class="text-[10px] text-slate-500">
                    {{ config('invoice.signer_title', 'Finance & Licensing Manager') }}
                </p>
            </div>
        </div>

        {{-- Footer Notes --}}
        <div class="mt-8 pt-4 border-t border-slate-100 text-[10px] text-slate-400 text-center leading-relaxed">
            Kwitansi ini adalah dokumen elektronik yang diterbitkan secara sah oleh sistem cloud Veyra Educate dan memiliki kekuatan pembuktian yang mengikat sesuai peraturan perundang-undangan.
        </div>
    </div>
</body>
</html>
