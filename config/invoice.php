<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Kwitansi / Invoice Resmi
    |--------------------------------------------------------------------------
    |
    | Pengaturan identitas penandatangan dan penerbit kwitansi pembayaran.
    | Nilai dapat disesuaikan melalui file .env server.
    |
    */

    'company_name' => env('INVOICE_COMPANY_NAME', 'PT. VEYRA CREATIVE TECH'),
    'company_sub' => env('INVOICE_COMPANY_SUB', 'Penyedia Layanan Sistem Informasi & Wadah Aplikasi Sekolah Terpadu (Veyra Educate)'),
    'company_address' => env('INVOICE_COMPANY_ADDRESS', 'Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep KodePos: 69461 • Telp/WA: +62 857-0707-4778 • Email: veyracreativetech@gmail.com'),

    'signer_name' => env('INVOICE_SIGNER_NAME', 'Ahmad Fauzi, S.Kom., M.T.'),
    'signer_title' => env('INVOICE_SIGNER_TITLE', 'Finance & Licensing Manager'),
    'signer_department' => env('INVOICE_SIGNER_DEPARTMENT', 'Bagian Keuangan & Penagihan'),
    'signer_city' => env('INVOICE_SIGNER_CITY', 'Sumenep'),
];
