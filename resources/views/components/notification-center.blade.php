@php
    $user = auth()->user();
    $school = $user?->school;
    $activeSub = $school?->activeSubscription;
    $notifList = [];

    // 1. Subscription reminder (if active subscriber)
    if ($activeSub && $activeSub->ends_at) {
        $daysLeft = (int) max(0, ceil(($activeSub->ends_at->getTimestamp() - now()->getTimestamp()) / 86400));
        if ($daysLeft <= 14) {
            $notifList[] = [
                'id' => 'notif-sub-' . $activeSub->id . '-' . $daysLeft,
                'category' => 'urgent',
                'title' => $daysLeft <= 3 ? 'Perhatian: Masa Langganan Segera Berakhir!' : 'Pengingat Perpanjangan Paket',
                'message' => 'Masa aktif paket ' . ($activeSub->plan?->label ?? 'Sekolah') . ' tersisa ' . $daysLeft . ' hari. Perpanjang agar modul CBT & rapor tetap lancar.',
                'time' => $daysLeft . ' hari tersisa',
                'badge' => $daysLeft <= 3 ? 'Kritis' : 'Pengingat',
                'badgeClass' => $daysLeft <= 3 ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300',
                'icon' => '👑',
                'iconBg' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                'url' => route('checkout.show', 'super'),
            ];
        }
    }

    // 2. Published Announcements from Admin
    try {
        $announcements = \App\Models\Announcement::published()->ordered()->take(4)->get();
        foreach ($announcements as $ann) {
            $notifList[] = [
                'id' => 'notif-ann-' . $ann->id,
                'category' => 'announcement',
                'title' => $ann->title,
                'message' => Str::limit(strip_tags($ann->body), 85),
                'time' => $ann->published_at ? $ann->published_at->diffForHumans() : 'Baru saja',
                'badge' => 'Broadcast',
                'badgeClass' => 'bg-brand-100 text-brand-800 dark:bg-brand-950/70 dark:text-brand-300',
                'icon' => '📢',
                'iconBg' => 'bg-brand-100 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300',
                'url' => route('dashboard'),
            ];
        }
    } catch (\Throwable $e) {}

    // 3. System Status / Welcome Tip
    $notifList[] = [
        'id' => 'notif-sys-uptime',
        'category' => 'system',
        'title' => 'Sistem Veyra Beroperasi Normal',
        'message' => 'Server CBT, server database raport, dan sinkronisasi presensi berjalan lancar.',
        'time' => 'Live',
        'badge' => 'Normal',
        'badgeClass' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300',
        'icon' => '⚡',
        'iconBg' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
        'url' => route('apps.index'),
    ];
@endphp

<div
    data-notifications="{{ json_encode($notifList) }}"
    x-data="{
        open: false,
        activeTab: 'all',
        notifications: [],
        readIds: JSON.parse(localStorage.getItem('veyra_read_notifs') || '[]'),
        init() {
            try {
                this.notifications = JSON.parse(this.$el.dataset.notifications || '[]');
            } catch (e) {
                this.notifications = [];
            }
        },
        
        get unreadCount() {
            return this.notifications.filter(n => !this.readIds.includes(n.id)).length;
        },

        get filteredNotifications() {
            if (this.activeTab === 'urgent') {
                return this.notifications.filter(n => n.category === 'urgent');
            }
            if (this.activeTab === 'announcement') {
                return this.notifications.filter(n => n.category === 'announcement');
            }
            return this.notifications;
        },

        toggleCenter() {
            if (window.vibrateHaptic) window.vibrateHaptic(10);
            this.open = !this.open;
        },

        markAllAsRead() {
            if (window.vibrateHaptic) window.vibrateHaptic(15);
            this.readIds = this.notifications.map(n => n.id);
            localStorage.setItem('veyra_read_notifs', JSON.stringify(this.readIds));
        },

        openItem(item) {
            if (!this.readIds.includes(item.id)) {
                this.readIds.push(item.id);
                localStorage.setItem('veyra_read_notifs', JSON.stringify(this.readIds));
            }
            this.open = false;
            if (item.url) {
                window.location.href = item.url;
            }
        }
    }"
    @click.outside="open = false"
    class="relative inline-block"
>
    {{-- Bell Icon Trigger Button --}}
    <button
        type="button"
        @click="toggleCenter()"
        class="relative inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-100 transition cursor-pointer"
        aria-label="Pusat Notifikasi"
        :title="unreadCount > 0 ? (unreadCount + ' notifikasi baru') : 'Pusat Notifikasi'"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Badge Dot or Counter --}}
        <template x-if="unreadCount > 0">
            <span class="absolute top-1 right-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white shadow-sm ring-2 ring-white dark:ring-slate-900 animate-pulse">
                <span x-text="unreadCount"></span>
            </span>
        </template>
    </button>

    {{-- Notification Dropdown Card --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="absolute right-0 top-full mt-2 w-80 sm:w-96 rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 shadow-2xl border border-slate-200/90 dark:border-slate-800 ring-1 ring-black/10 transition-all z-50 overflow-hidden flex flex-col max-h-[80vh]"
        x-cloak
    >
        {{-- Card Header --}}
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-900/60 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h4 class="text-sm font-extrabold text-slate-800 dark:text-slate-100">Notifikasi</h4>
                <template x-if="unreadCount > 0">
                    <span class="rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 px-2 py-0.5 text-[10px] font-extrabold uppercase" x-text="unreadCount + ' Baru'"></span>
                </template>
            </div>
            <button
                type="button"
                x-show="unreadCount > 0"
                @click="markAllAsRead()"
                class="text-[11px] font-semibold text-brand-600 dark:text-brand-400 hover:underline transition cursor-pointer"
            >
                Tandai semua dibaca
            </button>
        </div>

        {{-- Filter Chips --}}
        <div class="flex items-center gap-1.5 px-4 py-2 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold bg-white dark:bg-slate-900 shrink-0">
            <button
                type="button"
                @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xs' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'"
                class="rounded-xl px-2.5 py-1 transition cursor-pointer"
            >
                Semua
            </button>
            <button
                type="button"
                @click="activeTab = 'urgent'"
                :class="activeTab === 'urgent' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xs' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'"
                class="rounded-xl px-2.5 py-1 transition cursor-pointer"
            >
                Penting
            </button>
            <button
                type="button"
                @click="activeTab = 'announcement'"
                :class="activeTab === 'announcement' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xs' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'"
                class="rounded-xl px-2.5 py-1 transition cursor-pointer"
            >
                Pengumuman
            </button>
        </div>

        {{-- Notification Items List --}}
        <div class="overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 max-h-[55vh]">
            <template x-for="item in filteredNotifications" :key="item.id">
                <div
                    @click="openItem(item)"
                    :class="!readIds.includes(item.id) ? 'bg-brand-50/40 dark:bg-brand-950/20' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40'"
                    class="p-3.5 sm:p-4 transition cursor-pointer flex items-start gap-3 group relative"
                >
                    {{-- Unread Blue Dot --}}
                    <span
                        x-show="!readIds.includes(item.id)"
                        class="absolute top-4 left-1.5 h-2 w-2 rounded-full bg-brand-600 dark:bg-brand-400"
                    ></span>

                    {{-- Icon Container --}}
                    <div
                        :class="item.iconBg || 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-base shadow-xs group-hover:scale-105 transition-transform"
                        x-text="item.icon"
                    ></div>

                    <div class="min-w-0 flex-1 leading-snug">
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-brand-600 dark:group-hover:text-brand-400" x-text="item.title"></span>
                            <span class="shrink-0 text-[10px] text-slate-400 dark:text-slate-500" x-text="item.time"></span>
                        </div>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 line-clamp-2 leading-relaxed" x-text="item.message"></p>
                        <div class="mt-2 flex items-center justify-between">
                            <span
                                :class="item.badgeClass || 'bg-slate-100 text-slate-600'"
                                class="rounded-md px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wider"
                                x-text="item.badge"
                            ></span>
                            <span class="text-[10px] font-semibold text-brand-600 dark:text-brand-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                Buka →
                            </span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredNotifications.length === 0">
                <div class="py-10 px-4 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Tidak ada notifikasi baru</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Semua sistem dan pemberitahuan telah dibaca.</p>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 bg-slate-50/80 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800/80 text-center">
            <a href="{{ route('dashboard') }}" @click="open = false" class="text-[11px] font-bold text-slate-600 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 transition">
                Buka Dashboard Sekolah
            </a>
        </div>
    </div>
</div>
