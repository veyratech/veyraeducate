<x-admin-layout title="{{ $school->name }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Detail sekolah</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">{{ $school->name }}</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    NPSN {{ $school->npsn ?: '—' }} · {{ $school->city ?: '—' }} ·
                    <a href="{{ route('admin.schools.index') }}" class="link-muted">← Kembali</a>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.schools.edit', $school) }}" class="btn-minimal-secondary text-sm">Edit sekolah</a>
                <form method="POST" action="{{ route('admin.schools.destroy', $school) }}" onsubmit="return confirm('Hapus sekolah beserta data terkait?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-minimal-danger text-sm">Hapus</button>
                </form>
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

            @if (session('error'))
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Gagal menyimpan perubahan lisensi:</p>
                    <ul class="mt-1 space-y-0.5 text-xs">
                        @foreach ($errors->all() as $err)
                            <li>&bull; {{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bento-grid stagger-in">
                <div class="card-minimal p-5 bento-item">
                    <p class="stat-label">Kontak &amp; Alamat</p>
                    <p class="mt-2 text-sm font-semibold text-ink dark:text-slate-100">{{ $school->email }}</p>
                    <p class="text-sm text-muted dark:text-slate-400">{{ $school->phone ?: '—' }}</p>
                    @if ($school->address)
                        <p class="mt-1 text-xs text-muted dark:text-slate-500 line-clamp-2">{{ $school->address }}</p>
                    @endif
                </div>
                <div class="card-minimal p-5 bento-item">
                    <p class="stat-label">Operator &amp; Kepsek</p>
                    <p class="mt-2 text-sm font-semibold text-ink dark:text-slate-100">{{ $school->owner?->name ?? '—' }}</p>
                    <p class="text-xs text-muted dark:text-slate-400">{{ $school->owner?->email ?? '—' }}</p>
                    @if ($school->headmaster || !empty($school->meta['headmaster_name']))
                        <p class="mt-1.5 text-xs font-medium text-brand-700 dark:text-brand-400">
                            Kepsek: {{ $school->headmaster ?: ($school->meta['headmaster_name'] ?? '—') }}
                        </p>
                    @endif
                </div>
                <div class="card-minimal p-5 bento-item">
                    <p class="stat-label">Status langganan</p>
                    @if ($school->hasActiveSubscription())
                        @php $active = $school->activeSubscription()->first(); @endphp
                        <p class="mt-2 text-sm font-bold text-brand-800 dark:text-brand-400">{{ $active?->plan?->label }}</p>
                        <p class="text-xs text-muted dark:text-slate-500">s/d {{ optional($active?->ends_at)->format('d M Y') }}</p>
                    @else
                        <span class="badge-minimal badge-minimal-inactive mt-2">Tidak aktif</span>
                    @endif
                </div>
                <div class="card-minimal p-5 bento-item">
                    <p class="stat-label">Aktivitas &amp; Referral</p>
                    <p class="mt-2 text-sm font-semibold text-ink dark:text-slate-100">
                        @if ($school->last_app_access_at)
                            Terakhir diakses: {{ $school->last_app_access_at->format('d M Y H:i') }}
                            <span class="text-xs text-muted font-normal ml-1 dark:text-slate-500">({{ $school->last_app_access_at->diffForHumans() }})</span>
                        @else
                            Belum ada akses aplikasi
                        @endif
                    </p>
                    <p class="mt-1.5 text-xs font-mono text-muted dark:text-slate-400">
                        Kode Referral: <strong class="text-ink dark:text-slate-200">{{ $school->getReferralCode() }}</strong>
                    </p>
                </div>
            </div>

            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 60ms;">
                <div class="border-b border-slate-200 dark:border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-bold text-ink dark:text-slate-100">Pengguna</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($users as $user)
                        <div class="flex items-center justify-between gap-3 px-5 py-3 transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <div class="flex items-center gap-3">
                                <span class="avatar-minimal-sm avatar-minimal-sky">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-ink dark:text-slate-100">{{ $user->name }}</p>
                                    <p class="text-xs text-muted dark:text-slate-500">{{ $user->email }} · {{ $user->role }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-minimal py-4 border-0 shadow-none">
                            <div class="empty-minimal-icon avatar-minimal-sky">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <p class="empty-minimal-title">Belum ada pengguna.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 120ms;" x-data="{ grantOpen: false, grantMode: 'trial' }">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 px-5 py-4">
                    <div>
                        <h3 class="text-sm font-bold text-ink dark:text-slate-100">Langganan &amp; Lisensi</h3>
                        <p class="text-xs text-muted dark:text-slate-400">Riwayat langganan atau berikan Free Trial / aktivasi paket manual untuk sekolah ini</p>
                    </div>
                    <button
                        type="button"
                        @click="grantOpen = !grantOpen"
                        class="btn-minimal-primary text-xs py-1.5 px-3"
                    >
                        + Beri Free Trial / Aktivasi Paket
                    </button>
                </div>

                {{-- Form Aktivasi Manual / Free Trial --}}
                <div x-show="grantOpen" x-cloak class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 px-5 py-4">
                    <form method="POST" action="{{ route('admin.schools.grant-subscription', $school) }}" class="space-y-4">
                        @csrf
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="grantMode = 'trial'"
                                :class="grantMode === 'trial' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                            >
                                🎁 Berikan Free Trial (Hari)
                            </button>
                            <button
                                type="button"
                                @click="grantMode = 'plan'"
                                :class="grantMode === 'plan' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                class="rounded-lg px-3 py-1.5 text-xs font-bold transition"
                            >
                                ⚡ Aktivasi / Perpanjang Paket (Bulan)
                            </button>
                            <input type="hidden" name="mode" :value="grantMode">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-ink dark:text-slate-200 mb-1">Paket Lisensi</label>
                                <select name="plan_id" class="form-input-minimal w-full text-xs bg-white dark:bg-slate-800">
                                    @foreach ($plans ?? [] as $plan)
                                        <option value="{{ $plan->id }}" @selected($plan->is_default)>
                                            {{ $plan->label }} ({{ $plan->formatPrice() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="grantMode === 'trial'">
                                <label class="block text-xs font-semibold text-ink dark:text-slate-200 mb-1">Durasi Free Trial</label>
                                <select name="trial_days" class="form-input-minimal w-full text-xs bg-white dark:bg-slate-800">
                                    <option value="7">7 Hari</option>
                                    <option value="14" selected>14 Hari (Rekomendasi)</option>
                                    <option value="30">30 Hari (1 Bulan)</option>
                                    <option value="60">60 Hari (2 Bulan)</option>
                                </select>
                            </div>

                            <div x-show="grantMode === 'plan'" x-cloak>
                                <label class="block text-xs font-semibold text-ink dark:text-slate-200 mb-1">Durasi Aktif (Bulan)</label>
                                <select name="duration_months" class="form-input-minimal w-full text-xs bg-white dark:bg-slate-800">
                                    <option value="6">6 Bulan (1 Semester)</option>
                                    <option value="12" selected>12 Bulan (1 Tahun Anggaran)</option>
                                    <option value="24">24 Bulan (2 Tahun)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-ink dark:text-slate-200 mb-1">Catatan Admin (Opsional)</label>
                                <input type="text" name="notes" maxlength="255" placeholder="Contoh: Trial demo sosialisasi MKKS" class="form-input-minimal w-full text-xs" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <button type="button" @click="grantOpen = false" class="btn-minimal-secondary text-xs py-1.5 px-3">Batal</button>
                            <button type="submit" class="btn-minimal-primary text-xs py-1.5 px-4">
                                Aktifkan Sekarang &amp; Buka Akses Aplikasi
                            </button>
                        </div>
                    </form>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($subscriptions as $sub)
                        <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3 transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <div>
                                <p class="text-sm font-semibold text-ink dark:text-slate-100">{{ $sub->plan?->label }}</p>
                                <p class="text-xs text-muted dark:text-slate-500">
                                    {{ optional($sub->starts_at)->format('d M Y') ?? '—' }}
                                    →
                                    {{ optional($sub->ends_at)->format('d M Y') ?? '—' }}
                                </p>
                            </div>
                            <span class="badge-minimal {{ $sub->status->value === 'active' ? 'badge-minimal-active' : 'badge-minimal-inactive' }}">
                                {{ $sub->status->label() }}
                            </span>
                        </div>
                    @empty
                        <div class="empty-minimal py-4 border-0 shadow-none">
                            <div class="empty-minimal-icon">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="empty-minimal-title">Belum ada langganan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 180ms;">
                <div class="border-b border-slate-200 dark:border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-bold text-ink dark:text-slate-100">Order pembayaran</h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($orders as $order)
                        <a href="{{ route('admin.payments.show', $order) }}" class="flex items-center justify-between gap-3 px-5 py-3 transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <div>
                                <p class="font-mono text-xs font-semibold text-ink dark:text-slate-200">{{ $order->order_id }}</p>
                                <p class="text-xs text-muted dark:text-slate-500">{{ $order->plan?->label }} · {{ $order->formatAmount() }}</p>
                            </div>
                            <span class="badge-minimal {{ $order->status->value === 'paid' ? 'badge-minimal-active' : 'badge-minimal-inactive' }}">
                                {{ $order->status->label() }}
                            </span>
                        </a>
                    @empty
                        <div class="empty-minimal py-4 border-0 shadow-none">
                            <div class="empty-minimal-icon">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <p class="empty-minimal-title">Belum ada order.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

