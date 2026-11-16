<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AppGuide;
use App\Models\AppRequest;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\WebApp;
use App\Services\CbtStatsService;
use App\Services\SchoolAppInstaller;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly SchoolAppInstaller $installer,
        private readonly CbtStatsService $cbtStatsService
    ) {}

    public function __invoke(): View
    {
        $user = auth()->user();
        $school = $user?->school;

        if (! $school && $user) {
            if ($user->school_id) {
                $school = School::find($user->school_id);
            }

            if (! $school) {
                $school = School::where('owner_user_id', $user->id)->first();
            }

            if ($school && $user->school_id !== $school->id) {
                $user->forceFill(['school_id' => $school->id])->save();
                $user->setRelation('school', $school);
            }
        }

        $subscription = $school?->subscriptions()->with('plan')->latest('id')->first();
        $active = $school?->hasActiveSubscription() ? $school->activeSubscription()->first() : null;

        $announcements = Announcement::query()
            ->published()
            ->ordered()
            ->limit(5)
            ->get();

        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $lastOrder = $school?->paymentOrders()->with('plan')->latest('id')->first();

        $daysRemaining = null;
        $subscriptionProgress = null;

        if ($active?->ends_at) {
            $endsAt = $active->ends_at->getTimestamp();
            $startsAt = $active->starts_at?->getTimestamp() ?? $endsAt;

            $daysRemaining = (int) max(0, ceil(($endsAt - now()->getTimestamp()) / 86400));

            if ($endsAt > $startsAt) {
                $subscriptionProgress = (int) round(min(100, max(0, (now()->getTimestamp() - $startsAt) / ($endsAt - $startsAt) * 100)));
            }
        }

        $availableApps = WebApp::enabled()
            ->with('plans')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (WebApp $app): bool => $this->isDeployed($app))
            ->values();

        $installedSlugs = $school ? $school->webApps->pluck('slug')->toArray() : [];

        $myApps = collect();
        if ($school) {
            $myApps = $availableApps->filter(function (WebApp $app) use ($school, $installedSlugs): bool {
                return in_array($app->slug, $installedSlugs, true) || $school->hasAccessToApp($app->slug);
            })->values();
        }

        $stats = [
            'accessible_apps_count' => $myApps->count(),
            'available_apps_count' => $availableApps->count(),
            'pending_requests_count' => $school ? AppRequest::where('school_id', $school->id)->pending()->count() : 0,
            'total_guides_count' => AppGuide::count(),
        ];

        $profileCompletion = [
            'percentage' => 0,
            'completed' => 0,
            'total' => 5,
            'missing' => [],
        ];

        if ($school) {
            $checks = [
                ['label' => 'Nama Sekolah', 'valid' => ! empty($school->name), 'route' => route('school.settings')],
                ['label' => 'NPSN Resmi', 'valid' => ! empty($school->npsn), 'route' => route('school.settings')],
                ['label' => 'Kota / Wilayah', 'valid' => ! empty($school->city), 'route' => route('school.settings')],
                ['label' => 'Kontak / Telepon', 'valid' => ! empty($school->phone) || ! empty($school->email), 'route' => route('school.settings')],
                ['label' => 'Logo Sekolah', 'valid' => ! empty($school->logo_path), 'route' => route('school.settings', ['tab' => 'logo'])],
            ];

            $completed = 0;
            $missing = [];
            foreach ($checks as $item) {
                if ($item['valid']) {
                    $completed++;
                } else {
                    $missing[] = $item;
                }
            }

            $profileCompletion = [
                'percentage' => (int) round(($completed / count($checks)) * 100),
                'completed' => $completed,
                'total' => count($checks),
                'missing' => $missing,
            ];
        }

        $recentAppRequests = $school
            ? AppRequest::where('school_id', $school->id)->latest('id')->take(3)->get()
            : collect();

        $topGuides = AppGuide::with('webApp')
            ->orderByDesc('download_count')
            ->latest('id')
            ->take(3)
            ->get();

        $systemHealth = [
            'status' => 'online',
            'uptime' => '99.98%',
            'last_access' => $school?->last_app_access_at ? $school->last_app_access_at->diffForHumans() : 'Belum ada aktivitas',
            'registered_users' => $school ? $school->users()->count() : 1,
            'version' => 'v2.4 LTS',
            'server_region' => 'ID-JKT (Cloud Dedicated)',
        ];

        $cbtStats = $this->cbtStatsService->getSchoolStats($school);

        $upcomingAgendas = $this->resolveUpcomingAgendas($school, $active, $cbtStats);

        $appCategories = $myApps->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('dashboard', compact(
            'school',
            'subscription',
            'active',
            'announcements',
            'plans',
            'lastOrder',
            'daysRemaining',
            'subscriptionProgress',
            'availableApps',
            'installedSlugs',
            'myApps',
            'stats',
            'profileCompletion',
            'recentAppRequests',
            'topGuides',
            'systemHealth',
            'upcomingAgendas',
            'appCategories',
            'cbtStats',
        ));
    }

    private function isDeployed(WebApp $app): bool
    {
        return $app->access_url !== null || $this->installer->getAppPath($app->slug, $app) !== null;
    }

    /**
     * Build dynamic upcoming agendas from school custom agendas, CBT active exams, subscription milestones, and defaults.
     *
     * @param  array<string, mixed>  $cbtStats
     * @return array<int, array{id: ?string, title: string, date: string, raw_date: string, days_left: int, category: string, badge_color: string, is_custom: bool}>
     */
    private function resolveUpcomingAgendas(?School $school, ?Subscription $active, array $cbtStats = []): array
    {
        $items = [];
        $today = now()->startOfDay();

        $badgeColors = [
            'CBT & Ujian' => 'blue',
            'Akademik' => 'emerald',
            'Rapat & Dinas' => 'amber',
            'Pemeliharaan' => 'purple',
        ];

        $cbtActiveExams = is_array($cbtStats['active_exams'] ?? null) ? $cbtStats['active_exams'] : [];
        foreach (array_slice($cbtActiveExams, 0, 2) as $cbtExam) {
            if (empty($cbtExam['title'])) {
                continue;
            }

            try {
                $targetDate = ! empty($cbtExam['start_time'])
                    ? Carbon::parse((string) $cbtExam['start_time'])->startOfDay()
                    : $today->copy();
            } catch (\Throwable) {
                $targetDate = $today->copy();
            }

            $daysLeft = (int) max(0, $today->diffInDays($targetDate, false));
            $items[] = [
                'id' => null,
                'title' => 'Ujian CBT: '.(string) $cbtExam['title'],
                'date' => $targetDate->format('d M Y'),
                'raw_date' => $targetDate->toDateString(),
                'days_left' => $daysLeft,
                'category' => 'CBT & Ujian',
                'badge_color' => 'blue',
                'is_custom' => false,
            ];
        }

        $customAgendas = is_array($school?->meta['agendas'] ?? null) ? $school->meta['agendas'] : [];
        foreach ($customAgendas as $custom) {
            if (empty($custom['title']) || empty($custom['date'])) {
                continue;
            }

            try {
                $targetDate = Carbon::parse($custom['date'])->startOfDay();
            } catch (\Throwable) {
                continue;
            }

            $daysLeft = (int) $today->diffInDays($targetDate, false);
            if ($daysLeft < 0) {
                continue;
            }

            $category = (string) ($custom['category'] ?? 'Akademik');
            $items[] = [
                'id' => $custom['id'] ?? null,
                'title' => (string) $custom['title'],
                'date' => $targetDate->format('d M Y'),
                'raw_date' => $targetDate->toDateString(),
                'days_left' => $daysLeft,
                'category' => $category,
                'badge_color' => $badgeColors[$category] ?? 'emerald',
                'is_custom' => true,
            ];
        }

        if ($active?->ends_at) {
            $endsDate = $active->ends_at->copy()->startOfDay();
            $daysLeft = (int) max(0, $today->diffInDays($endsDate, false));
            if ($daysLeft <= 60) {
                $items[] = [
                    'id' => null,
                    'title' => 'Pembaruan Paket '.($active->plan?->label ?? 'Sekolah'),
                    'date' => $endsDate->format('d M Y'),
                    'raw_date' => $endsDate->toDateString(),
                    'days_left' => $daysLeft,
                    'category' => 'Pemeliharaan',
                    'badge_color' => 'purple',
                    'is_custom' => false,
                ];
            }
        }

        $defaults = [
            [
                'id' => null,
                'title' => 'Backup Otomatis Database Sekolah',
                'date' => $today->copy()->addDays(2)->format('d M Y'),
                'raw_date' => $today->copy()->addDays(2)->toDateString(),
                'days_left' => 2,
                'category' => 'Pemeliharaan',
                'badge_color' => 'purple',
                'is_custom' => false,
            ],
            [
                'id' => null,
                'title' => 'Asesmen & Ujian CBT Semester '.($school?->meta['semester'] ?? 'Aktif'),
                'date' => $today->copy()->addDays(5)->format('d M Y'),
                'raw_date' => $today->copy()->addDays(5)->toDateString(),
                'days_left' => 5,
                'category' => 'CBT & Ujian',
                'badge_color' => 'blue',
                'is_custom' => false,
            ],
            [
                'id' => null,
                'title' => 'Sinkronisasi Rapor & Nilai Akhir',
                'date' => $today->copy()->addDays(12)->format('d M Y'),
                'raw_date' => $today->copy()->addDays(12)->toDateString(),
                'days_left' => 12,
                'category' => 'Akademik',
                'badge_color' => 'emerald',
                'is_custom' => false,
            ],
        ];

        foreach ($defaults as $defaultItem) {
            if (count($items) >= 4) {
                break;
            }
            $items[] = $defaultItem;
        }

        usort($items, fn (array $a, array $b): int => $a['days_left'] <=> $b['days_left']);

        return array_slice($items, 0, 5);
    }
}
