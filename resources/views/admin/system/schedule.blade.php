<x-admin-layout title="Pemantau Jadwal Sistem — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Pemeliharaan &amp; Keamanan</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Jadwal &amp; Pemantauan Cron</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    Pantau status eksekusi semua tugas terjadwal di sistem, lihat waktu eksekusi terakhir, dan jalankan manual jika diperlukan.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site max-w-5xl space-y-6">

            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div class="card-minimal overflow-hidden">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Daftar Tugas Terjadwal</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">
                            Status diperbarui secara real-time saat halaman dimuat. Klik "Jalankan Sekarang" untuk mengeksekusi tugas secara manual.
                        </p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-left text-xs uppercase tracking-wide text-muted dark:text-slate-400">
                                    <th class="px-5 py-3 font-semibold">Nama Tugas</th>
                                    <th class="px-5 py-3 font-semibold">Jadwal</th>
                                    <th class="px-5 py-3 font-semibold">Eksekusi Terakhir</th>
                                    <th class="px-5 py-3 font-semibold">Eksekusi Berikutnya</th>
                                    <th class="px-5 py-3 font-semibold">Status</th>
                                    <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($tasks as $task)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="font-semibold text-ink dark:text-slate-100">{{ $task['command'] }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <code class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded font-mono text-slate-600 dark:text-slate-300">{{ $task['expression'] }}</code>
                                        </td>
                                        <td class="px-5 py-3.5 text-xs text-muted dark:text-slate-400">
                                            @if ($task['last_run_at'])
                                                <span>{{ $task['last_run_at']->format('d M Y, H:i') }}</span>
                                                <span class="block text-[11px] text-slate-400 dark:text-slate-500">{{ $task['last_run_at']->diffForHumans() }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Belum pernah</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-xs text-muted dark:text-slate-400">
                                            {{ $task['next_run_at'] }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @php
                                                $statusBadge = match ($task['status']) {
                                                    'ok' => 'badge-minimal-active',
                                                    'warning' => 'badge-minimal-pending',
                                                    'error' => 'badge-minimal-inactive',
                                                    default => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                                                };
                                                $statusLabel = match ($task['status']) {
                                                    'ok' => 'Oke',
                                                    'warning' => 'Peringatan',
                                                    'error' => 'Error',
                                                    default => 'Never',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusBadge }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $task['status'] === 'ok' ? 'bg-emerald-500' : ($task['status'] === 'warning' ? 'bg-amber-500 animate-pulse' : ($task['status'] === 'error' ? 'bg-red-500' : 'bg-slate-400')) }}"></span>
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right">
                                            <button
                                                type="button"
                                                data-signature="{{ e($task['command']) }}"
                                                onclick="runTask(this)"
                                                class="btn-minimal-secondary text-xs py-1.5 px-3 inline-flex items-center gap-1.5"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Jalankan Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center">
                                            <p class="text-sm text-muted dark:text-slate-400">Belum ada tugas terjadwal yang terdeteksi.</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pastikan cron schedule telah dikonfigurasi di <code class="font-mono text-xs">routes/console.php</code>.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-minimal">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Petunjuk Penggunaan</h3>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="text-xs text-muted dark:text-slate-400 space-y-1.5 list-disc list-inside">
                        <li>Tombol <strong>Jalankan Sekarang</strong> akan memicu eksekusi tugas secara sinkron melalui Artisan.</li>
                        <li>Hasil eksekusi manual tidak ditampilkan di sini — gunakan <code class="font-mono">php artisan schedule:work</code> atau <code class="font-mono">crontab</code> untuk eksekusi otomatis jangka panjang.</li>
                        <li>Status <strong>Oke</strong> berarti tugas berjalan dalam 2 jam terakhir. <strong>Peringatan</strong> artinya lebih dari 2 jam tetapi kurang dari 24 jam sejak eksekusi terakhir. <strong>Error</strong> berarti belum berjalan dalam 24 jam atau lebih.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>

<script>
function runTask(btn) {
    const signature = btn.getAttribute('data-signature');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg> Menjalankan…';

    fetch('{{ route("admin.schedule.run-now") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ signature: signature }),
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.ok) {
            btn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Berhasil';
            setTimeout(function() { location.reload(); }, 800);
        } else {
            btn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Gagal';
            console.error('Run failed:', data.error || data);
        }
    })
    .catch(function(err) {
        btn.innerHTML = originalText;
        btn.disabled = false;
        console.error('Request error:', err);
    });
}
</script>
