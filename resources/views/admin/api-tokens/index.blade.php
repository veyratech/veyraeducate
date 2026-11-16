<x-admin-layout title="Token API — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Akses API</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Token API</h2>
                <p class="mt-1 text-sm text-muted">Kelola token API pribadi untuk akses programatik ke layanan.</p>
            </div>
            <a href="{{ route('admin.api-tokens.create') }}" class="btn-minimal-primary whitespace-nowrap">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Token Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">

            @if (session('status'))
                <div class="alert-success stagger-in">
                    <p class="text-xs font-semibold">{{ session('status') }}</p>
                    @if (session('plain_token'))
                        <div class="mt-2 p-2 rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800">
                            <code class="text-xs font-mono break-all text-emerald-700 dark:text-emerald-300">{{ session('plain_token') }}</code>
                        </div>
                        <p class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400">Simpan token ini sekarang. Token tidak dapat dilihat kembali.</p>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Data belum tersimpan</p>
                    <ul class="mt-2 space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($tokens->isEmpty())
                <div class="empty-minimal stagger-in">
                    <div class="empty-minimal-icon">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563.097-1.159.026-1.563-.09L9 4.75V18l6.375-3.938a6 6 0 004.375-6.297zM7.5 10.5h.008v.008H7.5V10.5z"/></svg>
                    </div>
                    <p class="empty-minimal-title">Belum ada token API</p>
                    <p class="empty-minimal-desc">Buat token pertama untuk mengakses API secara programatik.</p>
                </div>
            @else
                <div class="card-minimal overflow-hidden stagger-in">
                    <div class="overflow-x-auto">
                        <table class="table-minimal table-responsive-card">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">Nama Token</th>
                                    <th class="px-4 py-3">Dibuat Oleh</th>
                                    <th class="px-4 py-3">Abilities / Scope</th>
                                    <th class="px-4 py-3">Terpakai Terakhir</th>
                                    <th class="px-4 py-3">Kedaluwarsa</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tokens as $token)
                                    <tr>
                                        <td data-label="Nama Token" class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="avatar-minimal-md avatar-minimal-brand">{{ strtoupper(substr($token->name, 0, 1)) }}</span>
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold text-ink dark:text-slate-100">{{ $token->name }}</p>
                                                    <p class="truncate text-[11px] text-muted dark:text-slate-500 font-mono">{{ Str::limit($token->token, 20) }}...</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Dibuat Oleh" class="px-4 py-3 text-muted dark:text-slate-500">
                                            {{ optional($token->tokenable)->name ?? '—' }}
                                        </td>
                                        <td data-label="Abilities / Scope" class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($token->abilities as $ability)
                                                    <span class="badge-minimal {{ $ability === '*' ? 'badge-minimal-active' : 'badge-minimal-draft' }}">{{ $ability }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td data-label="Terpakai Terakhir" class="px-4 py-3 text-xs text-muted dark:text-slate-500">
                                            @if ($token->last_used_at)
                                                {{ $token->last_used_at->format('d M Y, H:i') }}
                                            @else
                                                <span class="text-slate-400 dark:text-slate-600">Belum pernah</span>
                                            @endif
                                        </td>
                                        <td data-label="Kedaluwarsa" class="px-4 py-3 text-xs text-muted dark:text-slate-500">
                                            @if ($token->expires_at)
                                                {{ $token->expires_at->format('d M Y') }}
                                            @else
                                                <span class="text-slate-400 dark:text-slate-600">Tidak kedaluwarsa</span>
                                            @endif
                                        </td>
                                        <td data-label="Aksi" class="px-4 py-3 text-right">
                                            <form method="POST" action="{{ route('admin.api-tokens.destroy', $token) }}" onsubmit="return confirm('Cabut token \"{{ addslashes($token->name) }}\"?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 transition font-medium" title="Cabut token">
                                                    Cabut
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                        {{ $tokens->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
