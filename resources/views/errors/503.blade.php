<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 — Pemeliharaan Sistem | Veyra Educate</title>
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
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-50 dark:bg-brand-950/60 border border-brand-200 dark:border-brand-800 text-brand-800 dark:text-brand-300 text-xs font-bold mb-6 shadow-xs">
            <span class="h-2 w-2 rounded-full bg-brand-500 animate-pulse"></span>
            Status 503 &bull; Pemeliharaan Rutin
        </div>

        <div class="h-24 w-24 mx-auto rounded-3xl bg-brand-50 dark:bg-brand-950/80 border border-brand-200 dark:border-brand-800 flex items-center justify-center text-brand-600 dark:text-brand-400 text-4xl shadow-lg shadow-brand-500/10 mb-6">
            🚀
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Sistem Sedang Ditingkatkan
        </h1>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            Kami sedang melakukan pembaruan berkala untuk meningkatkan kecepatan server, kestabilan ujian, dan keamanan data sekolah Anda.
        </p>

        <div class="mt-6 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            Estimasi waktu selesai: <strong>5 - 15 menit</strong>. Terima kasih atas kesabaran Anda.
        </div>

        <div class="mt-8 flex justify-center">
            <button
                type="button"
                onclick="window.location.reload()"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-600/20 transition cursor-pointer"
                style="color: #ffffff !important;"
            >
                <span>Cek Kembali Sekarang</span>
            </button>
        </div>
    </div>
</body>
</html>
