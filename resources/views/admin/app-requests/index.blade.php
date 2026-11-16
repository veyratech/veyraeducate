<x-admin-layout title="Permintaan Aplikasi — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Aplikasi</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Permintaan Aplikasi</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn-minimal-secondary text-sm">← Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">
            @if (session('status'))
                <div class="alert-success stagger-in">
                    <p class="text-xs font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Gagal memperbarui permintaan:</p>
                    <ul class="mt-1 space-y-0.5 text-xs">
                        @foreach ($errors->all() as $err)
                            <li>&bull; {{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($pendingCount > 0)
                <div class="alert-warning stagger-in">
                    <p class="font-semibold">
                        Ada {{ $pendingCount }} permintaan yang menunggu persetujuan.
                    </p>
                </div>
            @endif

            {{-- Filter & Search Bar --}}
            @php
                $tabs = [
                    '' => ['label' => 'Semua', 'count' => $counts['all'] ?? $recentRequests->count()],
                    'pending' => ['label' => 'Menunggu', 'count' => $counts['pending'] ?? $pendingCount],
                    'approved' => ['label' => 'Disetujui', 'count' => $counts['approved'] ?? 0],
                    'rejected' => ['label' => 'Ditolak', 'count' => $counts['rejected'] ?? 0],
                ];
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($tabs as $key => $tab)
                        @php $isActive = ($statusFilter ?? '') === $key; @endphp
                        <a
                            href="{{ route('admin.app-requests.index', array_filter(['status' => $key !== '' ? $key : null, 'q' => $search ?? null])) }}"
                            class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition {{ $isActive ? 'bg-brand-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700' }}"
                        >
                            <span>{{ $tab['label'] }}</span>
                            <span class="rounded-full px-1.5 py-0.2 text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                {{ $tab['count'] }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.app-requests.index') }}" class="flex items-center gap-2">
                    @if (! empty($statusFilter))
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    <x-text-input
                        type="search"
                        name="q"
                        :value="$search ?? ''"
                        class="form-input-minimal text-xs w-56 sm:w-64"
                        placeholder="Cari aplikasi atau sekolah..."
                    />
                    <x-primary-button class="btn-minimal-primary text-xs">Cari</x-primary-button>
                    @if (! empty($search) || ! empty($statusFilter))
                        <a href="{{ route('admin.app-requests.index') }}" class="btn-minimal-secondary text-xs">Reset</a>
                    @endif
                </form>
            </div>

            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 60ms;">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-bold text-ink dark:text-slate-100">Riwayat permintaan ({{ $recentRequests->count() }})</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($recentRequests as $req)
                        <div class="px-5 py-4 transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-800/60" x-data="{ showReply: false }">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-bold text-ink dark:text-slate-100">{{ $req->name }}</p>
                                        <span class="badge-minimal shrink-0
                                            {{ match ($req->status) {
                                                'approved' => 'badge-minimal-active',
                                                'rejected' => 'badge-minimal-danger',
                                                default => 'badge-minimal-pending',
                                            } }}">
                                            {{ match ($req->status) {
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                default => 'Menunggu',
                                            } }}
                                        </span>
                                        @if ($req->priority === 'mendesak')
                                            <span class="badge-minimal badge-minimal-danger shrink-0">
                                                Mendesak
                                            </span>
                                        @elseif ($req->priority === 'tinggi')
                                            <span class="badge-minimal badge-minimal-pending shrink-0">
                                                Tinggi
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-muted dark:text-slate-500">
                                        {{ $req->school?->name ?? '—' }} · oleh {{ $req->user?->name ?? '—' }}
                                        · {{ $req->created_at->format('d M Y, H:i') }}
                                    </p>
                                    @if ($req->category || $req->priority)
                                        <p class="mt-0.5 text-[11px] text-muted dark:text-slate-500">
                                            @if ($req->category)
                                                <span class="font-medium text-slate-600 dark:text-slate-400">{{ ucfirst($req->category) }}</span>
                                                ·
                                            @endif
                                            <span>Prioritas: {{ ucfirst($req->priority ?? 'normal') }}</span>
                                            @if ($req->target_date)
                                                · Target: <span class="font-medium dark:text-slate-300">{{ $req->target_date->format('d M Y') }}</span>
                                            @endif
                                        </p>
                                    @endif
                                    @if ($req->description)
                                        <p class="mt-1.5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $req->description }}</p>
                                    @endif
                                    @if ($req->additional_notes)
                                        <p class="mt-1 text-xs text-muted italic dark:text-slate-400">Catatan sekolah: {{ $req->additional_notes }}</p>
                                    @endif
                                    @if ($req->admin_notes)
                                        <div class="mt-2 rounded-lg bg-brand-50/70 dark:bg-brand-950/40 border border-brand-200/70 dark:border-brand-800/50 px-3 py-2 text-xs text-brand-800 dark:text-brand-300">
                                            <span class="font-semibold">Balasan Admin:</span> {{ $req->admin_notes }}
                                        </div>
                                    @endif
                                </div>

                                <div class="shrink-0 flex flex-wrap items-center gap-2">
                                    @if ($req->status === 'pending')
                                        <form method="POST" action="{{ route('admin.app-requests.update-status', $req) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn-minimal-primary text-xs py-1.5 px-3">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.app-requests.update-status', $req) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn-minimal-secondary text-xs py-1.5 px-3">Tolak</button>
                                        </form>
                                    @endif
                                    <button
                                        type="button"
                                        @click="showReply = !showReply"
                                        class="btn-minimal-secondary text-xs py-1.5 px-3"
                                    >
                                        {{ $req->admin_notes ? 'Edit Balasan' : '+ Catatan / Ubah Status' }}
                                    </button>
                                </div>
                            </div>

                            {{-- Form Balasan / Catatan Admin --}}
                            <form
                                x-show="showReply"
                                x-cloak
                                method="POST"
                                action="{{ route('admin.app-requests.update-status', $req) }}"
                                class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-end gap-2.5"
                            >
                                @csrf
                                @method('PUT')
                                <div class="flex-1">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Catatan / Balasan Admin untuk Sekolah
                                    </label>
                                    <input
                                        type="text"
                                        name="admin_notes"
                                        value="{{ $req->admin_notes }}"
                                        maxlength="500"
                                        placeholder="Contoh: Aplikasi sedang dijadwalkan untuk rilis bulan depan..."
                                        class="form-input-minimal w-full text-xs"
                                    />
                                </div>
                                <div class="w-full sm:w-40">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Status
                                    </label>
                                    <select name="status" class="form-input-minimal w-full text-xs bg-white dark:bg-slate-800">
                                        <option value="pending" @selected($req->status === 'pending')>Menunggu</option>
                                        <option value="approved" @selected($req->status === 'approved')>Disetujui</option>
                                        <option value="rejected" @selected($req->status === 'rejected')>Ditolak</option>
                                    </select>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="btn-minimal-primary text-xs py-2 px-3.5 whitespace-nowrap">
                                        Simpan
                                    </button>
                                    <button type="button" @click="showReply = false" class="btn-minimal-secondary text-xs py-2 px-3">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="empty-minimal py-4 border-0 shadow-none">
                            <div class="empty-minimal-icon">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p class="empty-minimal-title">Belum ada permintaan aplikasi custom.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
