<x-admin-layout title="Deployment — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Wadah Aplikasi</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Deployment</h2>
                <p class="mt-1 text-sm text-muted">Riwayat deployment aplikasi ke sekolah.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site space-y-6">

            <div class="card-minimal p-4 stagger-in">
                <form method="GET" action="{{ route('admin.deployments.index') }}" class="flex flex-wrap items-center gap-3">
                    <select name="app" class="form-input-minimal text-xs w-auto bg-white dark:bg-slate-800" onchange="this.form.submit()">
                        <option value="">Semua Aplikasi</option>
                        @foreach ($apps ?? [] as $app)
                            <option value="{{ $app->id }}" @selected(($selectedApp ?? '') == (string) $app->id || ($selectedApp ?? '') === $app->slug)>
                                {{ $app->name }} ({{ $app->slug }})
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="form-input-minimal text-xs w-auto bg-white dark:bg-slate-800" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="success" @selected(($selectedStatus ?? '') === 'success')>Berhasil</option>
                        <option value="pending" @selected(($selectedStatus ?? '') === 'pending')>Menunggu</option>
                        <option value="deploying" @selected(($selectedStatus ?? '') === 'deploying')>Dideploy</option>
                        <option value="failed" @selected(($selectedStatus ?? '') === 'failed')>Gagal</option>
                    </select>

                    <button type="submit" class="btn-minimal-primary text-xs py-2 px-4">Filter</button>
                    @if (! empty($selectedApp) || ! empty($selectedStatus))
                        <a href="{{ route('admin.deployments.index') }}" class="btn-minimal-secondary text-xs py-2 px-3">Reset</a>
                    @endif
                </form>
            </div>

            @if ($deployments->isEmpty())
                <div class="empty-minimal stagger-in">
                    <div class="empty-minimal-icon">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <p class="empty-minimal-title">Belum ada deployment</p>
                    <p class="empty-minimal-desc">Deployment akan tercatat saat aplikasi di-deploy ke sekolah.</p>
                </div>
            @else
                <div class="card-minimal p-0 overflow-hidden stagger-in">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-muted">Aplikasi</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-muted">Versi</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-muted">Status</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-muted">Dideploy</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-muted">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($deployments as $deploy)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-colors">
                                        <td class="px-4 py-3">
                                            <a href="{{ route('admin.web-apps.show', $deploy->webApp?->slug ?? '') }}"
                                               class="font-medium text-ink dark:text-slate-100 hover:text-brand-600 dark:hover:text-brand-400 transition-colors"
                                            >
                                                {{ $deploy->webApp?->name ?? $deploy->slug }}
                                            </a>
                                            <p class="text-xs text-muted font-mono mt-0.5">{{ $deploy->slug }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-muted">{{ $deploy->version ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'badge-minimal-warning',
                                                    'deploying' => 'badge-minimal-info',
                                                    'success' => 'badge-minimal-active',
                                                    'failed' => 'badge-minimal-inactive',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Menunggu',
                                                    'deploying' => 'Dideploy',
                                                    'success' => 'Berhasil',
                                                    'failed' => 'Gagal',
                                                ];
                                            @endphp
                                            <span class="badge-minimal {{ $statusColors[$deploy->status] ?? 'badge-minimal-inactive' }}">
                                                {{ $statusLabels[$deploy->status] ?? $deploy->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-muted">
                                            {{ $deploy->creator?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-muted">
                                            {{ $deploy->deployed_at ? $deploy->deployed_at->format('d M Y H:i') : ($deploy->created_at->format('d M Y H:i')) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 px-4 py-3">
                        <p class="text-xs text-muted">{{ $deployments->count() }} deployment</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
