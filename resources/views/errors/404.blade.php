<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | Veyra Educate</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        {{-- Floating Badge --}}
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold mb-6 shadow-xs">
            <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
            Status 404 &bull; Halaman Tidak Ditemukan
        </div>

        {{-- Illustration Icon --}}
        <div class="h-24 w-24 mx-auto rounded-3xl bg-brand-50 dark:bg-brand-950/80 border border-brand-200 dark:border-brand-800 flex items-center justify-center text-brand-600 dark:text-brand-400 text-4xl shadow-lg shadow-brand-500/10 mb-6">
            🧭
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Waduh, Halaman Belum Tersedia
        </h1>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            Alamat web yang Anda tuju mungkin salah ketik, sudah dipindahkan, atau belum dipublikasikan di portal Veyra Educate.
        </p>

        {{-- Exam Safety Notice --}}
        <div class="mt-6 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left text-xs text-slate-600 dark:text-slate-400 shadow-xs">
            <p class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5 mb-1">
                <span>🛡️</span> Sedang Mengikuti Ujian Siswa?
            </p>
            <p>
                Tenang, seluruh jawaban yang sudah Anda klik tetap tersimpan aman di server. Silakan login kembali melalui tautan resmi sekolah Anda.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a
                href="/"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-600/20 transition"
                style="color: #ffffff !important;"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Halaman Utama</span>
            </a>
            <a
                href="https://wa.me/6285707074778?text=Halo%20Bantuan%20Veyra%20Educate,%20saya%20menemui%20error%20404"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm transition"
            >
                <span>Bantuan WhatsApp</span>
            </a>
        </div>
    </div>
</body>
</html>
