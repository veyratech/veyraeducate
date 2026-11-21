<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Terbatas | Veyra Educate</title>
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
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold mb-6 shadow-xs">
            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
            Status 403 &bull; Akses Terbatas
        </div>

        <div class="h-24 w-24 mx-auto rounded-3xl bg-amber-50 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 flex items-center justify-center text-amber-600 dark:text-amber-400 text-4xl shadow-lg shadow-amber-500/10 mb-6">
            🔐
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
            Akses Dibatasi
        </h1>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            Anda tidak memiliki izin untuk membuka halaman atau berkas ini. Pastikan Anda telah masuk dengan akun yang memiliki hak akses (Administrator / Guru).
        </p>

        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a
                href="{{ route('dashboard') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm shadow-md shadow-brand-600/20 transition"
                style="color: #ffffff !important;"
            >
                <span>Ke Dashboard</span>
            </a>
            <a
                href="/"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm transition"
            >
                <span>Beranda Utama</span>
            </a>
        </div>
    </div>
</body>
</html>
