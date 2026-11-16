<x-admin-layout title="Kesehatan Sistem — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Pemeliharaan &amp; Keamanan</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Kesehatan Sistem</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    Pantau status real-time seluruh komponen platform: database, cache, queue, storage, dan integrasi pihak ketiga.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span id="healthStatusBadge" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Memuat...
                </span>
                <button type="button" onclick="refreshHealth()" class="btn-minimal-secondary text-xs inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site max-w-5xl space-y-6" id="healthContent">

            {{-- Overall Status Hero --}}
            <div id="healthHero" class="rounded-2xl border p-6 flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                    <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-base font-bold text-ink dark:text-slate-100" id="heroTitle">Memeriksa kesehatan sistem...</p>
                    <p class="mt-0.5 text-sm text-muted dark:text-slate-400" id="heroDetail">Mohon tunggu sebentar.</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-muted dark:text-slate-500">Terakhir diperbarui</p>
                    <p class="mt-0.5 text-xs font-mono text-slate-500 dark:text-slate-400" id="heroTime">—</p>
                </div>
            </div>

            {{-- Component Cards Grid --}}
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" id="componentGrid">
                {{-- Cards akan diisi oleh JavaScript --}}
            </div>

            {{-- School Apps Section --}}
            <div class="card-minimal" id="appsSection" style="display:none;">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Aplikasi Sekolah Terpasang</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">Daftar aplikasi yang sudah ditanam di server.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div id="appsList" class="space-y-2"></div>
                </div>
            </div>

            {{-- Schedule Tasks Section --}}
            <div class="card-minimal" id="scheduleSection" style="display:none;">
                <div class="card-header">
                    <div>
                        <h3 class="text-sm font-semibold text-ink dark:text-slate-100">Tugas Terjadwal</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mt-0.5">Jadwal cron yang berjalan di sistem.</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-left text-xs uppercase tracking-wide text-muted dark:text-slate-400">
                                    <th class="px-5 py-3 font-semibold">Perintah</th>
                                    <th class="px-5 py-3 font-semibold">Eksekusi Terakhir</th>
                                    <th class="px-5 py-3 font-semibold">Eksekusi Berikutnya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="scheduleTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.system.diagnose') }}" class="btn-minimal-secondary text-xs inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Diagnosa Lengkap
                </a>
                <a href="{{ route('admin.schedule.index') }}" class="btn-minimal-secondary text-xs inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jadwal Sistem
                </a>
                <a href="{{ route('admin.backup.index') }}" class="btn-minimal-secondary text-xs inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Backup &amp; Database
                </a>
            </div>

        </div>
    </div>
</x-admin-layout>

<script>
const HEALTH_API = '{{ route("admin.system.health.api") }}';

async function fetchHealth() {
    try {
        const res = await fetch(HEALTH_API);
        const data = await res.json();
        renderHealth(data);
    } catch (e) {
        setLastError(e.message);
    }
}

function renderHealth(data) {
    // Hero badge
    const badge = document.getElementById('healthStatusBadge');
    if (data.overall_ok) {
        badge.className = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300';
        badge.innerHTML = '<span class="h-2 w-2 rounded-full bg-emerald-400"></span> Sehat';
    } else {
        badge.className = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300';
        badge.innerHTML = '<span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span> Ada Peringatan';
    }

    // Hero card
    document.getElementById('heroTitle').textContent = data.overall_label || 'Sistem Sehat';
    document.getElementById('heroDetail').textContent = `${Object.values(data.components).filter(c => c.ok).length}/${Object.keys(data.components).length} komponen normal`;
    document.getElementById('heroTime').textContent = data.updated_at ? new Date(data.updated_at).toLocaleTimeString('id-ID') : '—';

    // Component cards
    const grid = document.getElementById('componentGrid');
    grid.innerHTML = '';
    for (const [key, comp] of Object.entries(data.components)) {
        const icon = getComponentIcon(key);
        const colorClass = comp.ok
            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400'
            : 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400';
        const dotClass = comp.ok ? 'bg-emerald-400' : 'bg-red-400';
        grid.innerHTML += `
            <div class="card-minimal">
                <div class="card-body flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ${colorClass}">
                        ${icon}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-ink dark:text-slate-100">${comp.label}</p>
                            <span class="h-1.5 w-1.5 rounded-full ${dotClass} ${!comp.ok ? 'animate-pulse' : ''}"></span>
                        </div>
                        <p class="mt-0.5 text-xs text-muted dark:text-slate-400 break-all">${escapeHtml(comp.detail)}</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Apps section
    const appsComp = data.components.school_apps;
    const appsSection = document.getElementById('appsSection');
    if (appsComp && appsComp.count > 0) {
        appsSection.style.display = '';
        const list = document.getElementById('appsList');
        list.innerHTML = appsComp.list.map(app => `
            <div class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                ${escapeHtml(app)}
            </div>
        `).join('');
    } else {
        appsSection.style.display = 'none';
    }

    // Schedule section
    const scheduleTasks = data.schedule_tasks || [];
    const scheduleSection = document.getElementById('scheduleSection');
    if (scheduleTasks.length > 0) {
        scheduleSection.style.display = '';
        const tbody = document.getElementById('scheduleTableBody');
        tbody.innerHTML = scheduleTasks.map(t => `
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="px-5 py-3">
                    <span class="font-mono text-xs font-semibold text-ink dark:text-slate-200">${escapeHtml(t.command)}</span>
                    ${t.description ? `<p class="text-[11px] text-muted dark:text-slate-500 mt-0.5">${escapeHtml(t.description)}</p>` : ''}
                </td>
                <td class="px-5 py-3 text-xs text-muted dark:text-slate-400">${t.last_run || '<span class="italic">Belum pernah</span>'}</td>
                <td class="px-5 py-3 text-xs text-muted dark:text-slate-400">${escapeHtml(t.next_run || '—')}</td>
            </tr>
        `).join('');
    } else {
        scheduleSection.style.display = 'none';
    }
}

function getComponentIcon(key) {
    const icons = {
        database: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>',
        cache: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
        queue: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
        storage: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>',
        midtrans: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
        sso: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
        school_apps: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>',
        runtime: '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-.426-1.756-2.924-1.756-3.35 0a1.724 1.724 0 00-2.573 1.066c-1.543.94-3.31.826-2.37 2.37a1.724 1.724 0 00-1.066 2.573z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    };
    return icons[key] || icons.runtime;
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function refreshHealth() {
    fetchHealth();
}

function setLastError(msg) {
    document.getElementById('heroTitle').textContent = 'Gagal memuat data';
    document.getElementById('heroDetail').textContent = msg || 'Periksa koneksi ke server.';
    document.getElementById('heroTime').textContent = '—';
    document.getElementById('componentGrid').innerHTML = '<p class="text-sm text-red-500 col-span-full">Tidak dapat mengambil data kesehatan. Coba refresh halaman.</p>';
}

// Auto-refresh setiap 60 detik
let refreshTimer = null;
function startAutoRefresh() {
    clearInterval(refreshTimer);
    refreshTimer = setInterval(fetchHealth, 60000);
}

// Initial load
fetchHealth();
startAutoRefresh();
</script>
