<x-admin-layout title="Dashboard — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Platform</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Dashboard</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-site space-y-6">

            {{-- Greeting + hero --}}
            @php
                $openTasks = collect([
                    ['count' => $pendingOrders, 'label' => 'Verifikasi pembayaran'],
                    ['count' => $pendingAppRequests, 'label' => 'Permintaan aplikasi'],
                    ['count' => $pendingReferralClaims ?? 0, 'label' => 'Klaim komisi referral'],
                    ['count' => $incompleteProfiles, 'label' => 'Profil belum lengkap'],
                ])->sum(fn($t) => $t['count']);

                $greeting = match (true) {
                    now()->hour < 11 => 'Selamat pagi',
                    now()->hour < 15 => 'Selamat siang',
                    default => 'Selamat sore',
                };

                $activePct = $schoolsCount > 0 ? min(100, (int) round($activeSubs / $schoolsCount * 100)) : 0;
            @endphp

            <div class="admin-hero">
                <div class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 rounded-full bg-brand-500/20 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-8 -left-8 h-36 w-36 rounded-full bg-gold-500/10 blur-2xl" aria-hidden="true"></div>

                <div class="relative flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Dashboard admin</p>
                        <h1 class="mt-1 text-lg font-extrabold tracking-tight text-white sm:text-xl">
                            {{ $greeting }}, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-1 text-xs text-slate-400">
                            @if ($openTasks > 0)
                                {{ $openTasks }} tugas menunggu tindakan Anda.
                            @else
                                Semua tugas sudah beres. Kerja bagus.
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="glass-pill inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse" aria-hidden="true"></span>
                            {{ $activeSubs }} sekolah aktif
                        </span>
                        <a href="{{ route('admin.payments.index') }}" class="rounded-lg border border-white/10 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/20 transition backdrop-blur-sm">
                            Verifikasi pembayaran
                        </a>
                        <a href="{{ route('admin.schools.index') }}" class="rounded-lg border border-white/10 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/20 transition backdrop-blur-sm">
                            Kelola sekolah
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stat cards --}}
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Total Sekolah --}}
                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-brand-100 text-brand-700 dark:bg-brand-950/40 dark:text-brand-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-16 0H3m2-9h.01M12 12h.01M17 12h.01M8 12H7.99M12 16h.01"/></svg>
                    </div>
                    <p class="stat-label">Total Sekolah</p>
                    <p class="stat-value" x-data="{ val: 0 }" x-init="const final = {{ $schoolsCount }}; const start = performance.now(); const step = (now) => { const progress = Math.min(1, (now - start) / 800); this.val = Math.round(final * (1 - Math.pow(1 - progress, 3))); if (progress < 1) requestAnimationFrame(step); }; requestAnimationFrame(step);" x-text="val">{{ number_format($schoolsCount, 0, ',', '.') }}</p>
                    <p class="stat-change neutral">{{ $registrationsThisMonth }} pendaftaran bulan ini</p>
                </div>

                {{-- Langganan Aktif --}}
                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="stat-label">Langganan Aktif</p>
                    <p class="stat-value" x-data="{ val: 0 }" x-init="const final = {{ $activeSubs }}; const start = performance.now(); const step = (now) => { const progress = Math.min(1, (now - start) / 800); this.val = Math.round(final * (1 - Math.pow(1 - progress, 3))); if (progress < 1) requestAnimationFrame(step); }; requestAnimationFrame(step);" x-text="val">{{ number_format($activeSubs, 0, ',', '.') }}</p>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $activePct }}%"></div>
                    </div>
                    <p class="stat-change neutral">{{ $activePct }}% dari semua sekolah</p>
                </div>

                {{-- Order Pending --}}
                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-gold-100 text-gold-700 dark:bg-gold-950/40 dark:text-gold-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="stat-label">Order Pending</p>
                    <p class="stat-value">{{ $pendingOrders }}</p>
                    <p class="stat-change {{ $pendingOrders > 0 ? 'negative' : 'positive' }}">
                        @if ($pendingOrders > 0)
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Perlu verifikasi · {{ $totalOrders }} total
                        @else
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Tidak ada antrean
                        @endif
                    </p>
                </div>

                {{-- Terbayar Bulan Ini --}}
                <div class="card-minimal stat-card-minimal">
                    <div class="stat-icon bg-violet-100 text-violet-700 dark:bg-violet-950/40 dark:text-violet-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="stat-label">Terbayar Bulan Ini</p>
                    <p class="stat-value">Rp {{ number_format($paidThisMonth, 0, ',', '.') }}</p>
                    @php
                        $revenueLastFull = $revenueByMonth[4] ?? null;
                        $revenueDeltaPct = null;
                        if ($revenueLastFull && isset($revenueByMonth[3]) && $revenueByMonth[3]['amount'] > 0) {
                            $revenueDeltaPct = (int) round(($revenueLastFull['amount'] - $revenueByMonth[3]['amount']) / $revenueByMonth[3]['amount'] * 100);
                        }
                    @endphp
                    @if ($revenueDeltaPct !== null)
                        <p class="stat-change {{ $revenueDeltaPct >= 0 ? 'positive' : 'negative' }}">
                            {{ $revenueDeltaPct >= 0 ? '+' : '' }}{{ $revenueDeltaPct }}% vs bulan lalu
                        </p>
                    @elseif ($revenueLastFull)
                        <p class="stat-change neutral">Bulan lalu Rp {{ number_format($revenueLastFull['amount'], 0, ',', '.') }}</p>
                    @else
                        <p class="stat-change neutral">Belum ada data</p>
                    @endif
                </div>

            </div>

            {{-- Fokus hari ini --}}
            @php
                $proofCount = $pendingProofOrders ?? 0;
                $refClaimCount = $pendingReferralClaims ?? 0;
                $tasks = [
                    [
                        'label' => 'Verifikasi pembayaran pending',
                        'count' => $pendingOrders,
                        'meta' => $proofCount > 0 ? $proofCount . ' bukti transfer siap diverifikasi' : 'Order menunggu konfirmasi lunas',
                        'href' => $proofCount > 0 ? route('admin.payments.index', ['status' => 'proof']) : route('admin.payments.index'),
                        'badge' => $pendingOrders > 0 ? 'badge-minimal-pending' : 'badge-minimal-active',
                        'badgeLabel' => $pendingOrders > 0 ? $pendingOrders . ' pending' : 'Tidak ada',
                        'iconBg' => $pendingOrders > 0 ? 'bg-gold-100 text-gold-700 dark:bg-gold-950/30 dark:text-gold-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
                    ],
                    [
                        'label' => 'Tinjau permintaan aplikasi',
                        'count' => $pendingAppRequests,
                        'meta' => 'Permintaan sekolah menunggu persetujuan',
                        'href' => route('admin.app-requests.index'),
                        'badge' => $pendingAppRequests > 0 ? 'badge-minimal-draft' : 'badge-minimal-active',
                        'badgeLabel' => $pendingAppRequests > 0 ? $pendingAppRequests . ' menunggu' : 'Semua aman',
                        'iconBg' => $pendingAppRequests > 0 ? 'bg-violet-100 text-violet-700 dark:bg-violet-950/30 dark:text-violet-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
                    ],
                    [
                        'label' => 'Klaim komisi referral',
                        'count' => $refClaimCount,
                        'meta' => 'Pengajuan pencairan komisi 10% sekolah',
                        'href' => route('admin.referrals.index', ['status' => 'pending']),
                        'badge' => $refClaimCount > 0 ? 'badge-minimal-pending' : 'badge-minimal-active',
                        'badgeLabel' => $refClaimCount > 0 ? $refClaimCount . ' klaim' : 'Semua selesai',
                        'iconBg' => $refClaimCount > 0 ? 'bg-brand-100 text-brand-700 dark:bg-brand-950/30 dark:text-brand-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
                    ],
                    [
                        'label' => 'Lengkapi profil sekolah',
                        'count' => $incompleteProfiles,
                        'meta' => 'NPSN atau kota belum diisi',
                        'href' => route('admin.schools.index'),
                        'badge' => $incompleteProfiles > 0 ? 'badge-minimal-pending' : 'badge-minimal-active',
                        'badgeLabel' => $incompleteProfiles > 0 ? $incompleteProfiles . ' belum lengkap' : 'Semua lengkap',
                        'iconBg' => $incompleteProfiles > 0 ? 'bg-gold-100 text-gold-700 dark:bg-gold-950/30 dark:text-gold-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
                    ],
                ];
            @endphp

            @if ($openTasks > 0)
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="eyebrow">Fokus hari ini</p>
                        <span class="badge-minimal badge-minimal-pending">{{ $openTasks }} terbuka</span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($tasks as $task)
                            <a href="{{ $task['href'] }}" class="card-minimal flex items-center gap-3 p-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $task['iconBg'] }}">
                                    @if ($task['count'] > 0)
                                        <span class="text-sm font-bold">{{ $task['count'] }}</span>
                                    @else
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-ink dark:text-slate-100 truncate">{{ $task['label'] }}</p>
                                    <p class="text-[11px] text-muted dark:text-slate-400 truncate">{{ $task['meta'] }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Charts + Activity row --}}
            <div class="grid gap-3 lg:grid-cols-3">

                {{-- Revenue Chart --}}
                <div class="card-minimal lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                        <div>
                            <p class="text-sm font-semibold text-ink dark:text-slate-100">Pendapatan</p>
                            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Tren pembayaran lunas 6 bulan terakhir</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 dark:bg-brand-950/50 border border-brand-200/60 dark:border-brand-800/50 px-3 py-1 text-xs font-bold text-brand-700 dark:text-brand-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                            Rp {{ number_format($revenueThisMonth, 0, ',', '.') }} bulan ini
                        </span>
                    </div>
                    <div class="px-5 py-5">
                        @php
                            $amounts = $revenueByMonth->pluck('amount')->toArray() + [0];
                            $maxRevenue = max(max($amounts), 1);
                        @endphp
                        <div class="relative h-44 flex items-end gap-2.5 sm:gap-4 pt-6">
                            {{-- Subtle horizontal gridlines --}}
                            <div class="pointer-events-none absolute inset-x-0 top-6 bottom-6 flex flex-col justify-between" aria-hidden="true">
                                <div class="border-b border-dashed border-slate-100 dark:border-slate-800/80 w-full"></div>
                                <div class="border-b border-dashed border-slate-100 dark:border-slate-800/80 w-full"></div>
                                <div class="border-b border-dashed border-slate-100 dark:border-slate-800/80 w-full"></div>
                            </div>

                            @foreach ($revenueByMonth as $month)
                                @php
                                    $height = $month['amount'] > 0 ? max(8, ($month['amount'] / $maxRevenue) * 100) : 6;
                                    $isCurrentMonth = $loop->last;
                                @endphp
                                <div class="group relative z-10 flex flex-1 flex-col items-center h-full justify-end gap-1.5">
                                    {{-- Interactive Hover Tooltip --}}
                                    <div class="pointer-events-none absolute -top-1 opacity-0 group-hover:opacity-100 group-hover:-translate-y-1 transition-all duration-150 z-20 whitespace-nowrap rounded-lg bg-slate-900 dark:bg-white px-2.5 py-1 text-[10px] font-bold text-white dark:text-slate-900 shadow-lg">
                                        {{ $month['label'] }}: Rp {{ number_format($month['amount'], 0, ',', '.') }}
                                    </div>

                                    <span class="text-[10px] font-bold text-ink dark:text-slate-200 tabular-nums">
                                        {{ $month['amount'] > 0 ? number_format($month['amount'] / 1000, 0, ',', '.') . 'k' : '0' }}
                                    </span>

                                    <div class="w-full flex-1 flex items-end">
                                        <div
                                            class="w-full rounded-t-lg transition-all duration-500 group-hover:brightness-110 {{ $month['amount'] > 0 ? ($isCurrentMonth ? 'bg-gradient-to-t from-brand-600 to-sky-400 shadow-sm shadow-brand-500/25' : 'bg-gradient-to-t from-brand-600/85 to-brand-400/85 dark:from-brand-600/75 dark:to-brand-400/75') : 'bg-slate-100 dark:bg-slate-800' }}"
                                            style="height: {{ $height }}%"
                                        ></div>
                                    </div>

                                    <span class="text-[10px] font-semibold {{ $isCurrentMonth ? 'text-brand-600 dark:text-brand-400' : 'text-muted dark:text-slate-500' }}">
                                        {{ explode(' ', $month['label'])[0] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card-minimal p-4">
                    <p class="mb-3 text-sm font-semibold text-ink dark:text-slate-100">Akses Cepat</p>
                    <div class="space-y-1.5">
                        <a href="{{ route('admin.schools.index') }}" class="group flex items-center gap-3 rounded-lg p-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-950/40 dark:text-brand-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-16 0H3m2-9h.01M12 12h.01M17 12h.01M8 12H7.99M12 16h.01"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink dark:text-slate-100 truncate">Kelola Sekolah</p>
                                <p class="text-[11px] text-muted dark:text-slate-400">{{ $schoolsCount }} terdaftar</p>
                            </div>
                            <svg class="h-3.5 w-3.5 shrink-0 text-muted opacity-0 transition group-hover:opacity-100 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="group flex items-center gap-3 rounded-lg p-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gold-100 text-gold-700 dark:bg-gold-950/30 dark:text-gold-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink dark:text-slate-100 truncate">Pembayaran</p>
                                <p class="text-[11px] text-muted dark:text-slate-400">{{ $pendingOrders }} order pending</p>
                            </div>
                            <svg class="h-3.5 w-3.5 shrink-0 text-muted opacity-0 transition group-hover:opacity-100 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('admin.app-requests.index') }}" class="group flex items-center gap-3 rounded-lg p-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-700 dark:bg-violet-950/30 dark:text-violet-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink dark:text-slate-100 truncate">Permintaan App</p>
                                <p class="text-[11px] text-muted dark:text-slate-400">{{ $pendingAppRequests }} menunggu</p>
                            </div>
                            <svg class="h-3.5 w-3.5 shrink-0 text-muted opacity-0 transition group-hover:opacity-100 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('admin.plans.index') }}" class="group flex items-center gap-3 rounded-lg p-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink dark:text-slate-100 truncate">Kelola Paket</p>
                                <p class="text-[11px] text-muted dark:text-slate-400">Atur paket langganan</p>
                            </div>
                            <svg class="h-3.5 w-3.5 shrink-0 text-muted opacity-0 transition group-hover:opacity-100 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-3 rounded-lg p-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-950/30 dark:text-sky-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-ink dark:text-slate-100 truncate">Pengguna</p>
                                <p class="text-[11px] text-muted dark:text-slate-400">{{ $totalSchoolUsers }} pengguna sekolah</p>
                            </div>
                            <svg class="h-3.5 w-3.5 shrink-0 text-muted opacity-0 transition group-hover:opacity-100 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Recent Orders Table --}}
            <div class="card-minimal overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                    <div>
                        <p class="text-sm font-semibold text-ink dark:text-slate-100">Order Terbaru</p>
                        <p class="mt-0.5 text-xs text-muted dark:text-slate-400">{{ $recentOrders->count() }} order terakhir</p>
                    </div>
                    <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 transition-colors">
                        Lihat semua →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-minimal w-full">
                        <thead>
                            <tr>
                                <th>Sekolah</th>
                                <th>Paket</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td class="table-cell">
                                        <div class="flex items-center gap-2">
                                            <span class="avatar-minimal avatar-minimal-sm avatar-minimal-brand">{{ strtoupper(substr($order->school?->name ?? 'P', 0, 1)) }}</span>
                                            <span class="text-sm font-medium text-ink dark:text-slate-200">{{ $order->school?->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="table-cell table-cell-muted">{{ $order->plan?->label ?? '—' }}</td>
                                    <td class="table-cell font-medium text-ink dark:text-slate-200">{{ $order->formatAmount() }}</td>
                                    <td class="table-cell">
                                        <span class="badge-minimal {{ match ($order->status->value) {
                                            'paid' => 'badge-minimal-active',
                                            'pending' => 'badge-minimal-pending',
                                            default => 'badge-minimal-inactive',
                                        } }}">{{ $order->status->label() }}</span>
                                    </td>
                                    <td class="table-cell table-cell-muted text-xs">{{ optional($order->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="table-cell text-center py-10">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-ink dark:text-slate-200">Belum ada transaksi pembayaran</p>
                                            <p class="text-xs text-muted dark:text-slate-400 max-w-xs">Order langganan dari sekolah akan otomatis tercatat dan tampil di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
