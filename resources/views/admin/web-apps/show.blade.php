<x-admin-layout title="{{ $webApp->name }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Wadah Aplikasi</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">{{ $webApp->name }}</h2>
                <p class="mt-1 text-sm text-muted">Manajemen detail aplikasi</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.link-access.index') }}" class="btn-minimal-secondary">Kelola link</a>
                <a href="{{ route('admin.web-apps.index') }}" class="btn-minimal-secondary">← Kembali</a>
                <a href="{{ route('admin.web-apps.edit', $webApp->slug) }}" class="btn-minimal-primary">Edit Aplikasi</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site space-y-6">

            @if (session('status'))
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Main Info Card --}}
            <div class="card-minimal p-6 stagger-in">
                <div class="flex items-start gap-4 mb-6">
                    <div class="avatar-minimal-md avatar-minimal-brand">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-bold text-ink dark:text-slate-100">{{ $webApp->name }}</h3>
                            <span class="badge-minimal {{ $deployed && $webApp->enabled ? 'badge-minimal-active' : ($deployed ? 'badge-minimal-inactive' : 'badge-minimal-pending') }}">
                                {{ ! $deployed ? 'Draft / Not Deployed' : ($webApp->enabled ? 'Deployed' : 'Nonaktif') }}
                            </span>
                        </div>
                        <p class="text-xs text-muted font-mono dark:text-slate-500 mt-1">/{{ $webApp->slug }}</p>
                    </div>
                </div>

                @if ($webApp->description)
                    <p class="text-sm text-muted leading-relaxed mb-4">{{ $webApp->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="form-group-minimal">
                        <x-input-label value="Kategori" />
                        <p class="mt-1 text-sm text-ink dark:text-slate-100">{{ $webApp->category ?? 'Umum' }}</p>
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label value="Ikon" />
                        <p class="mt-1 text-sm text-ink dark:text-slate-100">{{ $webApp->icon ?? 'app' }}</p>
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label value="URL akses" />
                        <p class="mt-1 text-sm font-mono text-brand-700 dark:text-brand-300">{{ $webApp->access_url ?? '—' }}</p>
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label value="Versi" />
                        <p class="mt-1 text-sm text-ink dark:text-slate-100">{{ $webApp->version ?? ($meta['version'] ?? '—') }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-xs text-muted">
                        <span class="font-semibold text-ink dark:text-slate-100">Tanggal install:</span>
                        {{ $installedAt ? \Carbon\Carbon::parse($installedAt)->format('d M Y H:i') : '—' }}
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-ink dark:text-slate-100">Akses Paket:</span>
                        @if ($webApp->plans->isEmpty())
                            <span class="badge-minimal badge-minimal-active">Semua Paket</span>
                        @else
                            @foreach ($webApp->plans as $plan)
                                <span class="badge-minimal badge-minimal-pending">{{ $plan->label }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Deployment History --}}
            <div class="card-minimal p-6 stagger-in" style="animation-delay: 100ms;">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-ink dark:text-slate-100">Riwayat Deploy</p>
                        <p class="text-xs text-muted dark:text-slate-400">{{ $deployments->count() }} deployment</p>
                    </div>
                    <span class="badge-minimal badge-minimal-inactive"> Riwayat</span>
                </div>

                @if ($deployments->isEmpty())
                    <div class="empty-minimal stagger-in">
                        <div class="empty-minimal-icon">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h2.25m3 0h.008v.008H13.25V12zm-3.75 3.75h.008v.008H9v-.008zm1.5-7.5h.008v.008H10.5V8.25zm7.5 0h.008v.008H18V8.25zm1.5 3.75h.008v.008H19.5V12zm-1.5 3.75h.008v.008H18v-.008zM21 8.25h.008v.008H21V8.25zM3.75 12h.008v.008H3.75V12zm0 3.75h.008v.008H3.75V15.75zM3.75 8.25h.008v.008H3.75V8.25z" />
                            </svg>
                        </div>
                        <p class="empty-minimal-title">Belum ada riwayat deploy</p>
                        <p class="empty-minimal-desc">Deployment akan tercatat di sini.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($deployments as $deployment)
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 dark:border-slate-800">
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold
                                    {{ match ($deployment->status) {
                                        'success' => 'bg-emerald-100 text-emerald-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'deploying' => 'bg-gold-100 text-gold-800',
                                        default => 'bg-slate-100 text-slate-800',
                                    } }}">
                                    {{ match ($deployment->status) {
                                        'success' => 'Berhasil',
                                        'failed' => 'Gagal',
                                        'deploying' => 'Sedang Deploy',
                                        default => $deployment->status,
                                    } }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-ink dark:text-slate-100 truncate">
                                        v{{ $deployment->version ?? '—' }}
                                    </p>
                                    <p class="text-xs text-muted dark:text-slate-400">
                                        {{ $deployment->created_at->format('d M Y H:i') }}
                                        @if ($deployment->creator)
                                            · {{ $deployment->creator->name }}
                                        @endif
                                    </p>
                                </div>
                                @if ($deployment->notes)
                                    <p class="text-xs text-muted max-w-[200px] truncate">{{ $deployment->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="card-minimal p-6 stagger-in" style="animation-delay: 200ms;">
                <p class="text-sm font-bold text-ink dark:text-slate-100 mb-4">Aksi Cepat</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.web-apps.edit', $webApp->slug) }}" class="btn-minimal-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Aplikasi
                    </a>
                    <form method="POST" action="{{ route('admin.web-apps.toggle', $webApp->slug) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-minimal-ghost text-xs px-3 py-1.5">
                            {{ $webApp->enabled ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.web-apps.uninstall', $webApp->slug) }}"
                          data-confirm="Hapus {{ $webApp->name }}?"
                          onsubmit="return confirm(this.dataset.confirm);">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-minimal-ghost text-xs px-3 py-1.5 text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            Hapus Aplikasi
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
