<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\FailedJob;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HealthController extends Controller
{
    public function index(): View
    {
        $health = $this->getHealthStatus();

        return view('admin.system.health', [
            'health' => $health,
            'dbOk' => $health['components']['database']['ok'],
            'cacheOk' => $health['components']['cache']['ok'],
            'queueOk' => $health['components']['queue']['ok'],
            'storageOk' => $health['components']['storage']['ok'],
            'midtransOk' => $health['components']['midtrans']['ok'],
            'ssoOk' => $health['components']['sso']['ok'],
            'appCount' => $health['components']['school_apps']['count'],
            'deployLast' => $health['deploy_last'],
            'scheduleTasks' => $health['schedule_tasks'],
        ]);
    }

    public function api(Request $request): JsonResponse
    {
        $health = $this->getHealthStatus();

        return response()->json($health);
    }

    private function getHealthStatus(): array
    {
        $components = [];

        // Database
        try {
            $pdo = DB::connection()->getPdo();
            $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $dbName = config('database.connections.'.config('database.default').'.database');
            $tableCount = (int) DB::selectOne('SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE()')->cnt;
            $components['database'] = [
                'ok' => true,
                'label' => 'Database',
                'detail' => "{$driver} · {$dbName} · {$tableCount} tabel",
            ];
        } catch (\Throwable $e) {
            $components['database'] = [
                'ok' => false,
                'label' => 'Database',
                'detail' => 'Koneksi gagal: '.$e->getMessage(),
            ];
        }

        // Cache
        try {
            Cache::put('_health_test', 1, 10);
            $val = Cache::get('_health_test');
            Cache::forget('_health_test');
            $driver = config('cache.default');
            $components['cache'] = [
                'ok' => $val === 1,
                'label' => 'Cache',
                'detail' => $val === 1 ? "OK ({$driver})" : "Gagal tulis/baca ({$driver})",
            ];
        } catch (\Throwable $e) {
            $components['cache'] = [
                'ok' => false,
                'label' => 'Cache',
                'detail' => $e->getMessage(),
            ];
        }

        // Queue
        try {
            $failed = FailedJob::count();
            $hasQueue = config('queue.default') !== 'sync';
            $components['queue'] = [
                'ok' => $hasQueue,
                'label' => 'Queue',
                'detail' => $hasQueue
                    ? "Aktif ({$failed} failed job)"
                    : 'Sync mode (tidak ada worker)',
            ];
        } catch (\Throwable $e) {
            $components['queue'] = [
                'ok' => false,
                'label' => 'Queue',
                'detail' => $e->getMessage(),
            ];
        }

        // Storage
        $storagePaths = [
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'storage/logs' => storage_path('logs'),
            'storage/app/public' => storage_path('app/public'),
            'public/storage' => public_path('storage'),
        ];
        $allStorageOk = true;
        $storageDetails = [];
        foreach ($storagePaths as $label => $path) {
            $exists = is_dir($path);
            $writable = $exists ? is_writable($path) : false;
            $status = $exists && $writable ? 'ok' : ($exists ? 'read-only' : 'missing');
            if ($status !== 'ok') {
                $allStorageOk = false;
            }
            $storageDetails[] = sprintf('%s (%s)', $label, $status);
        }
        $components['storage'] = [
            'ok' => $allStorageOk,
            'label' => 'Storage',
            'detail' => implode(', ', $storageDetails),
        ];

        // Midtrans
        $midtransKey = ! empty(env('MIDTRANS_SERVER_KEY'));
        $components['midtrans'] = [
            'ok' => $midtransKey,
            'label' => 'Midtrans',
            'detail' => $midtransKey ? 'API key tersedia' : 'Belum dikonfigurasi di .env',
        ];

        // SSO
        $ssoKey = ! empty(env('SSO_SECRET_KEY'));
        $ssoUrl = env('SSO_CBT_URL', '');
        $components['sso'] = [
            'ok' => $ssoKey,
            'label' => 'SSO',
            'detail' => $ssoKey
                ? "Secret configured · target: {$ssoUrl}"
                : 'SSO_SECRET_KEY belum diisi',
        ];

        // School Apps on disk
        $appBase = config('school-app.path', base_path('school-app'));
        $appCount = 0;
        $appList = [];
        if (is_dir($appBase)) {
            $items = array_filter(scandir($appBase), fn ($e) => $e !== '.' && $e !== '..' && ! str_starts_with($e, '.'));
            $appCount = count($items);
            foreach (array_slice($items, 0, 5) as $slug) {
                $metaPath = $appBase.'/'.$slug.'./install-meta.json';
                $meta = is_file($metaPath) ? json_decode((string) file_get_contents($metaPath), true) : [];
                $version = $meta['version'] ?? null;
                $appList[] = $slug.($version ? " v{$version}" : '');
            }
        }
        $components['school_apps'] = [
            'ok' => true,
            'label' => 'School Apps',
            'count' => $appCount,
            'list' => $appList,
            'detail' => $appCount > 0
                ? "{$appCount} aplikasi terpasang: ".implode(', ', $appList).(count($items) > 5 ? ' ...' : '')
                : 'Belum ada aplikasi sekolah yang ditanam',
        ];

        // PHP / Laravel version
        $components['runtime'] = [
            'ok' => true,
            'label' => 'Runtime',
            'detail' => 'PHP '.PHP_VERSION.' · Laravel '.app()->version(),
        ];

        $overallOk = collect($components)->every(fn ($c) => $c['ok']);

        // Schedule tasks summary
        $scheduleTasks = $this->gatherScheduleSummary();

        // Latest deployment
        $deployLast = Deployment::latest('deployed_at')->first();

        return [
            'overall_ok' => $overallOk,
            'overall_label' => $overallOk ? 'Sehat' : 'Ada Peringatan',
            'components' => $components,
            'deploy_last' => $deployLast ? [
                'slug' => $deployLast->slug,
                'status' => $deployLast->status,
                'deployed_at' => $deployLast->deployed_at?->diffForHumans(),
            ] : null,
            'schedule_tasks' => $scheduleTasks,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Ringkasan tugas terjadwal: command + status terakhir.
     *
     * @return array<int, array{command: string, description: string, last_run: string|null, next_run: string}>
     */
    private function gatherScheduleSummary(): array
    {
        $tasks = [];
        $scheduler = app(Schedule::class);

        foreach ($scheduler->events() as $event) {
            $command = method_exists($event, 'getSummaryForDisplay')
                ? $event->getSummaryForDisplay()
                : ($event->command ?? '*');
            $description = $event->description ?? '';
            $lastRunKey = 'schedule.last_run.'.md5($command);
            $lastRunRaw = Cache::get($lastRunKey);
            $lastRun = null;
            if ($lastRunRaw) {
                try {
                    $lastRun = Carbon::parse($lastRunRaw)->diffForHumans();
                } catch (\Throwable) {
                    $lastRun = null;
                }
            }
            $nextRun = '—';
            try {
                $next = $event->nextRun();
                if ($next instanceof CarbonInterface) {
                    $nextRun = $next->diffForHumans();
                }
            } catch (\Throwable) {
            }
            $tasks[] = [
                'command' => $command,
                'description' => $description,
                'last_run' => $lastRun,
                'next_run' => $nextRun,
            ];
        }

        return $tasks;
    }
}
