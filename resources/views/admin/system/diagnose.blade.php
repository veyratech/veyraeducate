<x-admin-layout title="Diagnosa &amp; Pemeliharaan Sistem — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Pemeliharaan &amp; Keamanan</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Diagnosa &amp; Pemeliharaan Sistem</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    Jalankan diagnosa kesehatan platform, cek status backup, dan pantau audit log sistem.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site max-w-5xl space-y-6">

            {{-- Diagnosa Kesehatan --}}
            <div class="card-minimal">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Diagnosa Kesehatan Sistem</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">Cek status database, cache, storage, queue, dan dependensi lainnya.</p>
                    </div>
                    <form id="diagnoseForm" method="POST" action="{{ route('admin.system.diagnose.run') }}" onsubmit="return runDiagnose(event)">
                        @csrf
                        <button type="submit" id="diagnoseBtn" class="btn-minimal-primary text-xs inline-flex items-center gap-1.5 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>Jalankan Diagnosa</span>
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div id="diagnoseOutput" class="rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-4 text-xs font-mono text-slate-700 dark:text-slate-300 whitespace-pre-wrap min-h-[120px]">
                        Klik "Jalankan Diagnosa" untuk memeriksa kesehatan sistem.
                    </div>
                </div>
            </div>

            {{-- Backup --}}
            <div class="card-minimal">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Cadangan Sistem (Backup)</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">Backup database dan folder school-app ke server. Backup otomatis berjalan setiap hari pukul 03:00.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.backup.index') }}" class="btn-minimal-secondary text-xs inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span>Kelola Backup</span>
                        </a>
                        <form id="backupForm" method="POST" action="{{ route('admin.system.backup.run') }}" onsubmit="return runBackup(event)">
                            @csrf
                            <button type="submit" id="backupBtn" class="btn-minimal-primary text-xs inline-flex items-center gap-1.5 shadow-sm">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Backup Sekarang</span>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div id="backupOutput" class="rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-4 text-xs font-mono text-slate-700 dark:text-slate-300 whitespace-pre-wrap min-h-[80px]">
                        Klik "Backup Sekarang" untuk membuat cadangan database dan school-app saat ini.
                    </div>
                </div>
            </div>

            {{-- Audit Log Quick Link --}}
            <div class="card-minimal">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Audit Log</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">Riwayat semua aktivitas instalasi, uninstall, dan perubahan penting di platform.</p>
                    </div>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn-minimal-secondary text-xs inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Lihat Semua Audit Log</span>
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-sm text-muted dark:text-slate-400">
                        Audit log mencatat setiap pemasangan dan penghapusan aplikasi sekolah, serta perubahan konfigurasi penting lainnya.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>

<script>
function runDiagnose(e) {
    e.preventDefault();
    var btn = document.getElementById('diagnoseBtn');
    var output = document.getElementById('diagnoseOutput');
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Memeriksa...';
    output.textContent = 'Menjalankan diagnosa...';

    fetch('{{ route('admin.system.diagnose.run') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        output.textContent = data.output || '(kosong)';
        btn.querySelector('span').textContent = data.status === 0 ? 'Selesai — OK' : 'Selesai — Ada Error';
    })
    .catch(function(err) {
        output.textContent = 'Gagal memuat hasil: ' + err.message;
        btn.querySelector('span').textContent = 'Coba Lagi';
    })
    .finally(function() {
        btn.disabled = false;
    });
}

function runBackup(e) {
    e.preventDefault();
    var btn = document.getElementById('backupBtn');
    var output = document.getElementById('backupOutput');
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Membackup...';
    output.textContent = 'Sedang membuat backup... (ini bisa memakan waktu)';

    fetch('{{ route('admin.system.backup.run') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        output.textContent = data.output || '(kosong)';
        btn.querySelector('span').textContent = data.status === 0 ? 'Backup Selesai' : 'Backup Gagal';
    })
    .catch(function(err) {
        output.textContent = 'Gagal memuat hasil: ' + err.message;
        btn.querySelector('span').textContent = 'Coba Lagi';
    })
    .finally(function() {
        btn.disabled = false;
    });
}
</script>
