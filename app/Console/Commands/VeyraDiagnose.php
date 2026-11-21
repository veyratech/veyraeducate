<?php

namespace App\Console\Commands;

use App\Models\Deployment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VeyraDiagnose extends Command
{
    protected $signature = 'veyra:diagnose';

    protected $description = 'Periksa kondisi sistem Veyra Educate (DB, cache, storage, env, deployment)';

    public function handle(): int
    {
        $this->newLine();
        $this->info('=== Veyra Educate — Diagnostic Check ===');
        $this->newLine();

        $exitCode = 0;

        // 1. PHP version
        $this->section('PHP');
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.3.0', '>=');
        $this->outputCheck('Versi PHP', $phpVersion, $phpOk);
        $this->outputCheck('Laravel', app()->version(), true);
        if (! $phpOk) {
            $this->error('  PHP 8.3+ wajib untuk Laravel 13!');
            $exitCode = 1;
        }

        // 2. Database
        $this->section('Database');
        try {
            $pdo = DB::connection()->getPdo();
            $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $this->outputCheck('Koneksi', "OK ({$driver})", true);
            $dbName = config('database.connections.'.config('database.default').'.database');
            $this->line("  Database : {$dbName}");
            $this->line('  Host     : '.config('database.connections.'.config('database.default').'.host'));
        } catch (\Throwable $e) {
            $this->error('Koneksi DB GAGAL: '.$e->getMessage());
            $exitCode = 1;
        }

        // 3. Cache
        $this->section('Cache');
        try {
            Cache::put('_diag_test', 1, 10);
            $val = Cache::get('_diag_test');
            Cache::forget('_diag_test');
            $driver = config('cache.default');
            $this->outputCheck('Cache', "OK ({$driver})", $val === 1);
        } catch (\Throwable $e) {
            $this->error('Cache error: '.$e->getMessage());
        }

        // 4. Storage
        $this->section('Storage');
        $storagePaths = [
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'storage/logs' => storage_path('logs'),
            'storage/app/public' => storage_path('app/public'),
            'public/storage' => public_path('storage'),
        ];

        foreach ($storagePaths as $label => $path) {
            $exists = is_dir($path);
            $writable = $exists ? is_writable($path) : false;
            $status = $exists && $writable ? 'ok' : ($exists ? 'read-only' : 'missing');
            $icon = $status === 'ok' ? 'OK' : ($status === 'read-only' ? '!' : 'MISS');
            $this->line("  [{$icon}] {$label}: {$status}");
            if ($status === 'missing' && str_contains($label, 'public/storage')) {
                $this->warn('  -> Jalankan: php artisan storage:link');
            }
            if ($status === 'read-only') {
                $this->warn("  -> Folder tidak writable: {$path}");
            }
        }

        // 5. Environment
        $this->section('Environment');
        $appEnv = config('app.env');
        $appKeySet = ! empty(config('app.key'));
        $this->outputCheck('APP_ENV', $appEnv, true);
        $this->outputCheck('APP_KEY', $appKeySet ? 'set' : 'KOSONG', $appKeySet);
        if (! $appKeySet) {
            $this->error('  Jalankan: php artisan key:generate');
            $exitCode = 1;
        }

        $midtransKey = $this->getEnvValue('MIDTRANS_SERVER_KEY');
        $this->outputCheck('MIDTRANS_SERVER_KEY', $midtransKey ? 'set' : 'kosong', (bool) $midtransKey);

        $ssoKey = $this->getEnvValue('SSO_SECRET_KEY');
        $this->outputCheck('SSO_SECRET_KEY', $ssoKey ? 'set' : 'kosong', (bool) $ssoKey);

        // 6. School apps on disk
        $this->section('School Apps');
        $appBase = config('school-app.path', base_path('school-app'));
        if (is_dir($appBase)) {
            $apps = array_filter(scandir($appBase), fn ($e) => $e !== '.' && $e !== '..' && ! str_starts_with($e, '.'));
            if ($apps === []) {
                $this->info('  Belum ada aplikasi sekolah yang terpasang.');
            } else {
                foreach ($apps as $slug) {
                    $metaPath = $appBase.'/'.$slug.'./install-meta.json';
                    $meta = is_file($metaPath) ? json_decode((string) file_get_contents($metaPath), true) : [];
                    $version = $meta['version'] ?? null;
                    $this->line("  [OK] {$slug}".($version ? " (v{$version})" : ''));
                }
            }
        } else {
            $this->warn("  Folder app belum ada: {$appBase}");
        }

        // 7. Recent deployments
        $this->section('Deployments');
        try {
            $deployCount = Deployment::count();
            $latest = $deployCount > 0 ? Deployment::latest('id')->first() : null;
            $this->line("  Total deployment : {$deployCount}");
            if ($latest) {
                $this->line("  Terakhir          : {$latest->slug} ({$latest->status}) — {$latest->deployed_at->diffForHumans()}");
            }
        } catch (\Throwable $e) {
            $this->error('Gagal membaca deployments: '.$e->getMessage());
        }

        $this->newLine();
        if ($exitCode === 0) {
            $this->info('Semua pemeriksaan lulus.');
        } else {
            $this->error('Ada masalah yang perlu diperbaiki — lihat di atas.');
        }
        $this->newLine();

        return $exitCode;
    }

    protected function section(string $title): void
    {
        $this->newLine();
        $this->info("------ {$title} ------");
    }

    protected function outputCheck(string $name, string $value, bool $ok): void
    {
        $icon = $ok ? 'OK' : 'FAIL';
        $color = $ok ? 'green' : 'red';
        $this->line("  [{$icon}] {$name}: <{$color}>{$value}</>{$color}>");
        if (! $ok) {
            $this->error("  {$name} bermasalah!");
        }
    }

    /**
     * Read an environment variable directly from .env file, bypassing Laravel's env()
     * helper which returns null when config cache is enabled.
     */
    protected function getEnvValue(string $key): ?string
    {
        $path = base_path('.env');
        if (! is_file($path)) {
            return null;
        }

        foreach (file($path) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (preg_match('/^'.preg_quote($key, '/').'\s*=\s*(.+)/', $line, $matches)) {
                $value = trim($matches[1], '"\'');

                return $value !== '' ? $value : null;
            }
        }

        return null;
    }
}
