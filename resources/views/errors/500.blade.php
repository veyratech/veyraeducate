<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Kendala Server | Veyra Educate</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        {{-- Floating Badge --}}
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-bold mb-6 shadow-xs">
            <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
            Status 500 &bull; Server Sedang Mengalami Kendala
        </div>

        {{-- Illustration Icon --}}
        <div class="h-24 w-24 mx-auto rounded-3xl bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 flex items-center justify-center text-rose-600 dark:text-rose-400 text-4xl shadow-lg shadow-rose-500/10 mb-6">
            ⚙️
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Server Sedang Dipulihkan
        </h1>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            Terjadi gangguan komunikasi sesaat pada server atau kapasitas antrian data sedang padat. Sistem otomatis sedang memulihkan koneksi.
        </p>

        {{-- Exam Safety Notice --}}
        <div class="mt-6 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left text-xs text-slate-600 dark:text-slate-400 shadow-xs">
            <p class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5 mb-1">
                <span>🛡️</span> Data Ujian &amp; Jawaban Anda Aman!
            </p>
            <p>
                Sistem Veyra menyimpan jawaban peserta didik secara bertahap (autosave). Silakan tunggu 1-2 menit lalu muat ulang halaman ini (tekan F5).
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <button
                type="button"
                onclick="window.location.reload()"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-600/20 transition cursor-pointer"
                style="color: #ffffff !important;"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Muat Ulang Halaman</span>
            </button>
            <a
                href="https://wa.me/6285707074778?text=Lapor%20gangguan%20server%20500%20Veyra"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm transition"
            >
                <span>Lapor Pengawas / CS</span>
            </a>
        </div>
    </div>
</body>
</html>
