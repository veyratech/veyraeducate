<x-admin-layout title="Audit Log &amp; Jejak Aktivitas — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Keamanan &amp; Audit Trail</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Audit Log &amp; Jejak Aktivitas</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    Catatan riwayat transaksi, perubahan data penting, otentikasi, dan aktivitas staf platform.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-850 dark:text-slate-200">
                    Total: {{ number_format($totalLogs, 0, ',', '.') }} Catatan
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">
            {{-- Search & Filters --}}
            <div class="card-minimal p-4 stagger-in">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <input
                            type="text"
                            name="search"
                            value="{{ $currentSearch }}"
                            placeholder="Cari aksi, modul, atau alamat IP..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                        >
                    </div>

                    <div class="w-full sm:w-auto">
                        <select
                            name="action"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            onchange="this.form.submit()"
                        >
                            <option value="">Semua Aksi</option>
                            @foreach ($actions as $act)
                                <option value="{{ $act }}" @selected($currentAction === $act)>{{ ucfirst($act) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn-minimal-primary text-xs py-2 px-4">
                        Filter
                    </button>

                    @if ($currentSearch || $currentAction)
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn-minimal-ghost text-xs py-2 px-3">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Audit Logs Table --}}
            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 60ms;">
                <div class="overflow-x-auto">
                    <table class="table-minimal table-responsive-card">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Pengguna</th>
                                <th class="px-4 py-3">Aksi</th>
                                <th class="px-4 py-3">Modul / Entitas</th>
                                <th class="px-4 py-3">IP Address</th>
                                <th class="px-4 py-3 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr x-data="{ openDetail: false }">
                                    <td data-label="Waktu" class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                        <p class="font-bold text-ink dark:text-slate-200">{{ $log->created_at->format('d M Y') }}</p>
                                        <p class="text-[11px] font-mono">{{ $log->created_at->format('H:i:s') }} WIB</p>
                                    </td>

                                    <td data-label="Pengguna" class="px-4 py-3">
                                        @if ($log->user)
                                            <p class="text-xs font-bold text-ink dark:text-slate-100">{{ $log->user->name }}</p>
                                            <p class="text-[11px] text-muted dark:text-slate-500">{{ $log->user->email }}</p>
                                        @else
                                            <span class="text-xs italic text-slate-400">Sistem / Tamu</span>
                                        @endif
                                    </td>

                                    <td data-label="Aksi" class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider
                                            @if(str_contains(strtolower($log->action), 'delete')) bg-red-100 text-red-800 dark:bg-red-950/50 dark:text-red-300
                                            @elseif(str_contains(strtolower($log->action), 'pay') || str_contains(strtolower($log->action), 'verified')) bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300
                                            @elseif(str_contains(strtolower($log->action), 'create') || str_contains(strtolower($log->action), 'upload')) bg-brand-100 text-brand-800 dark:bg-brand-950/50 dark:text-brand-300
                                            @else bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 @endif">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    <td data-label="Modul" class="px-4 py-3 text-xs">
                                        <p class="font-semibold text-ink dark:text-slate-200">{{ $log->shortAuditableType() }}</p>
                                        @if ($log->auditable_id)
                                            <p class="text-[11px] font-mono text-muted dark:text-slate-500">ID: {{ $log->auditable_id }}</p>
                                        @endif
                                    </td>

                                    <td data-label="IP Address" class="px-4 py-3 text-xs font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                        {{ $log->ip_address ?: '—' }}
                                    </td>

                                    <td data-label="Detail" class="px-4 py-3 text-right">
                                        @if ($log->old_values || $log->new_values)
                                            <button
                                                type="button"
                                                @click="openDetail = !openDetail"
                                                class="btn-minimal-ghost text-xs px-2.5 py-1"
                                            >
                                                <span x-text="openDetail ? 'Tutup' : 'Lihat Data'"></span>
                                            </button>
                                        @else
                                            <span class="text-xs text-muted">—</span>
                                        @endif

                                        {{-- Dropdown Data Modal / Accordion --}}
                                        <div
                                            x-show="openDetail"
                                            x-cloak
                                            class="mt-3 text-left p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2 text-xs"
                                        >
                                            @if ($log->old_values)
                                                <div>
                                                    <strong class="text-rose-600 dark:text-rose-400">Data Sebelum:</strong>
                                                    <pre class="mt-1 p-2 bg-white dark:bg-slate-950 rounded border border-slate-200 dark:border-slate-800 text-[11px] overflow-x-auto">{{ is_array($log->old_values) ? json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $log->old_values }}</pre>
                                                </div>
                                            @endif

                                            @if ($log->new_values)
                                                <div>
                                                    <strong class="text-emerald-600 dark:text-emerald-400">Data Baru:</strong>
                                                    <pre class="mt-1 p-2 bg-white dark:bg-slate-950 rounded border border-slate-200 dark:border-slate-800 text-[11px] overflow-x-auto">{{ is_array($log->new_values) ? json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $log->new_values }}</pre>
                                                </div>
                                            @endif

                                            @if ($log->user_agent)
                                                <p class="text-[10px] text-muted italic pt-1 border-t border-slate-200 dark:border-slate-800 truncate" title="{{ $log->user_agent }}">
                                                    Agent: {{ $log->user_agent }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-muted dark:text-slate-500">
                                        Belum ada riwayat aktivitas yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="border-t border-slate-200 dark:border-slate-800 px-4 py-3">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
