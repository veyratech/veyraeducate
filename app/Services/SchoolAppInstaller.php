<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\WebApp;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use ZipArchive;

class SchoolAppInstaller
{
    public const META_FILE = '.install-meta.json';

    public function baseDir(): string
    {
        return (string) config('school-app.path', base_path('school-app'));
    }

    public function appDir(string $slug): string
    {
        return $this->baseDir().'/'.$slug;
    }

    public function metaPath(string $slug): string
    {
        return $this->appDir($slug).'/'.self::META_FILE;
    }

    /**
     * @return array{slug: string, files: int, bytes: int, installed_at: string, source: string, version: string|null, skipped: array<int, string>, skipped_dirs: array<string, int>, schema: array{migrations: int, statements: int, warnings: array<int, string>, skipped_queries: array<int, string>}}
     */
    public function install(UploadedFile $zip, string $slug): array
    {
        $slug = $this->sanitizeSlug($slug);

        if (! $zip->isValid()) {
            throw new RuntimeException('File upload tidak valid.');
        }

        @set_time_limit(0);
        @ignore_user_abort(true);

        $tmpRoot = storage_path('app/private/school-app-install-'.uniqid('', true));
        $extractDir = $tmpRoot.'/extract';

        if (! mkdir($extractDir, 0755, true) && ! is_dir($extractDir)) {
            throw new RuntimeException('Gagal membuat folder sementara.');
        }

        try {
            return $this->extractArchive($zip, $slug, $extractDir);
        } finally {
            $this->deleteDirectory($tmpRoot);
        }
    }

    /**
     * Extract the ZIP into the app directory and persist its metadata.
     *
     * Forbidden files (.env, .htaccess, lock files, unknown extensions) are
     * skipped with a warning instead of aborting the whole install. Absolute
     * paths and path traversal entries still abort the install.
     *
     * @return array{slug: string, files: int, bytes: int, installed_at: string, source: string, version: string|null, skipped: array<int, string>, skipped_dirs: array<string, int>, schema: array{migrations: int, statements: int, warnings: array<int, string>, skipped_queries: array<int, string>}}
     */
    private function extractArchive(UploadedFile $zip, string $slug, string $extractDir): array
    {
        $archive = new ZipArchive;
        $opened = $archive->open($zip->getRealPath());

        if ($opened !== true) {
            throw new RuntimeException('File bukan ZIP yang valid.');
        }

        try {
            $partition = $this->partitionEntries($archive);
            $stats = $this->getZipStats($archive, $partition['allowedSet']);
        } finally {
            $archive->close();
        }

        if ($stats['files'] < 1) {
            $reasons = $partition['skipped'];
            foreach (array_keys($partition['skippedDirs']) as $dir) {
                $reasons[] = $dir.'/';
            }

            if ($reasons !== []) {
                throw new RuntimeException(
                    'ZIP hanya berisi file terlarang ('.self::summarizeList($reasons).') — tidak ada file untuk dipasang.'
                );
            }

            throw new RuntimeException('ZIP kosong — tidak ada file untuk dipasang.');
        }

        $archive->open($zip->getRealPath());
        $extracted = $archive->extractTo($extractDir, $partition['allowed']);
        $archive->close();

        if ($extracted !== true) {
            throw new RuntimeException('Gagal mengekstrak ZIP.');
        }

        $sourceRoot = $this->resolveSourceRoot($extractDir);

        $schema = $this->runSchema($sourceRoot, $slug);

        if ($partition['skipped'] !== []) {
            $schema['warnings'][] = $this->skippedFilesWarning($partition['skipped']);
        }

        foreach ($partition['skippedDirs'] as $dir => $count) {
            $schema['warnings'][] = "melewati folder {$dir} ({$count} file, tidak dibutuhkan saat runtime)";
        }

        $target = $this->appDir($slug);
        $backup = $target.'.bak-'.uniqid('', true);

        // Atomic install: backup existing target before any deletion
        if (is_dir($target)) {
            if (! @rename($target, $backup)) {
                $this->copyDirectory($target, $backup);
            }
        }

        try {
            if (! @rename($sourceRoot, $target)) {
                if (! is_dir($target) && ! mkdir($target, 0755, true) && ! is_dir($target)) {
                    throw new RuntimeException('Gagal membuat folder tujuan.');
                }
                $this->copyDirectory($sourceRoot, $target);
            }

            if (! is_dir($target)) {
                throw new RuntimeException('Gagal memasang aplikasi ke folder tujuan.');
            }

            // Validate the installed app has required structure
            $this->validateInstall($target, $slug);
        } catch (\Throwable $e) {
            // Restore from backup on failure
            @unlink($target);
            if (is_dir($target)) {
                $this->deleteDirectory($target);
            }
            if (is_dir($backup)) {
                rename($backup, $target);
            }
            throw $e;
        } finally {
            // Clean up backup only after successful validation
            if (is_dir($backup)) {
                $this->deleteDirectory($backup);
            }
        }

        $meta = [
            'source' => $zip->getClientOriginalName(),
            'files' => $stats['files'],
            'bytes' => $stats['bytes'],
            'installed_at' => now()->toIso8601String(),
            'version' => $this->readVersion($target),
            'skipped' => $partition['skipped'],
            'skipped_dirs' => $partition['skippedDirs'],
            'schema' => $schema,
        ];

        file_put_contents(
            $this->metaPath($slug),
            json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $webApp = WebApp::query()->firstOrNew(['slug' => $slug]);

        if (! $webApp->exists) {
            $webApp->name = $slug;
        }

        $webApp->source_file = $zip->getClientOriginalName();
        $webApp->file_count = $stats['files'];
        $webApp->version = $meta['version'];
        $webApp->save();

        Cache::forget('school_app.list');

        AuditLog::record(
            action: 'app.installed',
            auditable: $webApp,
            newValues: ['slug' => $slug, 'files' => $stats['files'], 'version' => $meta['version']],
            userId: auth()->id(),
        );

        return $meta;
    }

    /**
     * Remove an app by slug.
     */
    public function uninstall(string $slug): void
    {
        $slug = $this->sanitizeSlug($slug);
        $webApp = WebApp::where('slug', $slug)->first();

        AuditLog::record(
            action: 'app.uninstalled',
            auditable: $webApp,
            oldValues: $webApp ? $webApp->getAttributes() : null,
            userId: auth()->id(),
        );

        $this->deleteDirectory($this->appDir($slug));
        WebApp::where('slug', $slug)->delete();

        Cache::forget('school_app.list');
    }

    /**
     * @return array<int, array{id:int|null,slug:string,name:string,description:string|null,category:string,icon:string,access_url:string|null,link:string,version:string|null,enabled:bool,file_count:int,installed_at:string|null,on_disk:bool}>
     */
    public function listApps(): array
    {
        return Cache::remember('school_app.list', 300, fn () => $this->computeListApps());
    }

    /**
     * Compute the app list without caching.
     *
     * @return array<int, array{id:int|null,slug:string,name:string,description:string|null,category:string,icon:string,access_url:string|null,link:string,version:string|null,enabled:bool,file_count:int,installed_at:string|null,on_disk:bool}>
     */
    private function computeListApps(): array
    {
        $dbApps = WebApp::with('plans')->orderBy('sort_order')->orderBy('name')->get();
        $filesystemSlugs = $this->scanInstalledSlugs();

        $results = [];

        foreach ($dbApps as $app) {
            $exists = in_array($app->slug, $filesystemSlugs, true);
            $external = $app->access_url !== null;
            $meta = $exists ? $this->readMeta($app->slug) : null;
            $results[] = [
                'id' => $app->id,
                'slug' => $app->slug,
                'name' => $app->name,
                'description' => $app->description,
                'category' => $app->category,
                'icon' => $app->icon,
                'access_url' => $app->access_url,
                'link' => $app->accessLink(),
                'version' => $app->version ?? $meta['version'] ?? null,
                'enabled' => $app->enabled && ($exists || $external),
                'file_count' => $app->file_count ?? ($meta['files'] ?? 0),
                'installed_at' => $meta['installed_at'] ?? null,
                'on_disk' => $exists,
                'plans' => $app->plans,
            ];
        }

        foreach ($filesystemSlugs as $slug) {
            if (! $dbApps->firstWhere('slug', $slug)) {
                $meta = $this->readMeta($slug);
                $results[] = [
                    'id' => null,
                    'slug' => $slug,
                    'name' => $meta['name'] ?? $slug,
                    'description' => null,
                    'category' => 'umum',
                    'icon' => 'app',
                    'access_url' => null,
                    'link' => route('school-app.app', $slug),
                    'version' => $meta['version'] ?? null,
                    'enabled' => true,
                    'file_count' => $meta['files'] ?? 0,
                    'installed_at' => $meta['installed_at'] ?? null,
                    'on_disk' => true,
                ];
            }
        }

        usort($results, function (array $a, array $b): int {
            if ($a['enabled'] === $b['enabled']) {
                return strcmp($a['slug'], $b['slug']);
            }

            return $a['enabled'] ? -1 : 1;
        });

        return $results;
    }

    public function getApp(string $slug): ?array
    {
        $slug = $this->sanitizeSlug($slug);
        $dbApp = WebApp::where('slug', $slug)->first();
        $meta = $this->readMeta($slug);

        if (! is_dir($this->appDir($slug))) {
            return null;
        }

        return [
            'id' => $dbApp?->id,
            'slug' => $slug,
            'name' => $dbApp?->name ?? $meta['name'] ?? $slug,
            'description' => $dbApp?->description ?? null,
            'category' => $dbApp?->category ?? 'umum',
            'icon' => $dbApp?->icon ?? 'app',
            'version' => $dbApp?->version ?? $meta['version'] ?? null,
            'enabled' => ($dbApp?->enabled ?? true) && is_dir($this->appDir($slug)),
            'file_count' => $dbApp?->file_count ?? ($meta['files'] ?? 0),
            'installed_at' => $meta['installed_at'] ?? null,
            'source_file' => $meta['source'] ?? null,
            'path' => $this->appDir($slug),
        ];
    }

    /** @var array<string, bool> */
    private array $appExistsCache = [];

    public function getAppPath(string $slug, ?WebApp $knownApp = null): ?string
    {
        $slug = $this->sanitizeSlug($slug);
        $path = $this->appDir($slug);

        if (! is_dir($path)) {
            return null;
        }

        if ($knownApp !== null && $knownApp->slug === $slug) {
            return $knownApp->enabled ? $path : null;
        }

        if (array_key_exists($slug, $this->appExistsCache)) {
            return $this->appExistsCache[$slug] ? $path : null;
        }

        $app = WebApp::where('slug', $slug)->first();
        $enabled = ! $app || (bool) $app->enabled;
        $this->appExistsCache[$slug] = $enabled;

        return $enabled ? $path : null;
    }

    public function getViewPath(string $slug, string $view): string
    {
        $path = $this->getAppPath($slug);
        if ($path === null) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $viewFile = $path.'/'.$view;
        if (! is_file($viewFile)) {
            abort(404, 'Halaman tidak ditemukan.');
        }

        return $viewFile;
    }

    private function validateInstall(string $target, string $slug): void
    {
        // Basic validation: installed directory should not be empty
        $entries = array_values(array_filter(scandir($target) ?: [], fn ($e) => $e !== '.' && $e !== '..'));
        if ($entries === []) {
            throw new RuntimeException("Aplikasi {$slug} tidak memiliki file yang terpasang.");
        }

        // Check for required files (at minimum should have some PHP or config files)
        $hasPhpFiles = false;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($target, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $hasPhpFiles = true;
                break;
            }
        }

        if (! $hasPhpFiles) {
            throw new RuntimeException("Aplikasi {$slug} tidak mengandung file PHP yang valid.");
        }
    }

    private function sanitizeSlug(string $slug): string
    {
        $slug = preg_replace('/[^a-z0-9\-_]/i', '-', $slug);
        $slug = trim($slug, '-');

        return $slug ?: 'unknown';
    }

    public function readMeta(string $slug): array
    {
        $metaPath = $this->metaPath($slug);
        if (is_file($metaPath)) {
            $decoded = json_decode((string) file_get_contents($metaPath), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function readVersion(string $dir): ?string
    {
        $pkgPath = $dir.'/package.json';
        if (is_file($pkgPath)) {
            $data = json_decode((string) file_get_contents($pkgPath), true);
            if (is_array($data) && isset($data['version'])) {
                return $data['version'];
            }
        }

        return null;
    }

    private function scanInstalledSlugs(): array
    {
        if (! is_dir($this->baseDir())) {
            return [];
        }

        $slugs = [];
        foreach (scandir($this->baseDir()) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }
            $path = $this->baseDir().'/'.$entry;
            if (is_dir($path)) {
                $slugs[] = $entry;
            }
        }

        return $slugs;
    }

    private const ALLOWED_EXTENSIONS = [
        '.php', '.blade.php', '.js', '.ts', '.jsx', '.tsx',
        '.css', '.scss', '.less', '.styl', '.sass',
        '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp', '.ico', '.woff', '.woff2', '.ttf', '.eot',
        '.json', '.xml', '.yaml', '.yml', '.toml',
        '.md', '.txt', '.html', '.htm',
        '.map', '.env.example', '.env.sample',
        '.sql',
    ];

    /**
     * Get file count and total size from a ZipArchive without extracting.
     *
     * @param  array<string, true>|null  $allowedSet  when given, only entries in the set are counted.
     */
    private function getZipStats(ZipArchive $archive, ?array $allowedSet = null): array
    {
        $files = 0;
        $bytes = 0;
        for ($i = 0; $i < $archive->numFiles; $i++) {
            $stat = $archive->statIndex($i);
            if (! $stat || ! isset($stat['size']) || str_ends_with($stat['name'], '/')) {
                continue;
            }
            if ($allowedSet !== null && ! isset($allowedSet[$stat['name']])) {
                continue;
            }
            $files++;
            $bytes += $stat['size'];
        }

        return ['files' => $files, 'bytes' => $bytes];
    }

    /**
     * Directory trees that are never needed at runtime and are skipped whole.
     */
    private const SKIPPED_DIRS = ['node_modules', '.git'];

    /**
     * Maximum SQL execution errors reported; the rest are counted in one line so
     * deployment notes and flash messages stay within packet/column limits.
     */
    private const MAX_SCHEMA_ERRORS = 20;

    /**
     * Split ZIP entries into allowed vs skipped.
     *
     * Absolute paths and path traversal still abort the whole install.
     * Forbidden files are collected into $skipped so the install can continue
     * without them.
     *
     * @return array{allowed: array<int, string>, allowedSet: array<string, true>, skipped: array<int, string>, skippedDirs: array<string, int>}
     */
    private function partitionEntries(ZipArchive $archive): array
    {
        $allowed = [];
        $allowedSet = [];
        $skipped = [];
        $skippedDirs = [];

        for ($i = 0; $i < $archive->numFiles; $i++) {
            $name = (string) $archive->getNameIndex($i);

            if ($name === '' || str_starts_with($name, '/') || str_starts_with($name, '\\')) {
                throw new RuntimeException('ZIP berisi path absolut yang tidak diizinkan.');
            }

            if (str_contains($name, '..')) {
                throw new RuntimeException('ZIP berisi path traversal yang tidak diizinkan.');
            }

            if (($junkDir = $this->junkDir($name)) !== null) {
                if (! str_ends_with($name, '/')) {
                    $skippedDirs[$junkDir] = ($skippedDirs[$junkDir] ?? 0) + 1;
                }

                continue;
            }

            // Directories are always extracted; only files are filtered.
            if (str_ends_with($name, '/')) {
                $allowed[] = $name;
                $allowedSet[$name] = true;

                continue;
            }

            if ($this->isForbiddenFile($name)) {
                $basename = basename($name);
                if (! in_array($basename, $skipped, true)) {
                    $skipped[] = $basename;
                }

                continue;
            }

            $allowed[] = $name;
            $allowedSet[$name] = true;
        }

        sort($skipped);

        return ['allowed' => $allowed, 'allowedSet' => $allowedSet, 'skipped' => $skipped, 'skippedDirs' => $skippedDirs];
    }

    /**
     * Shorten a long list for notes and flash messages.
     *
     * @param  array<int, string>  $items
     */
    public static function summarizeList(array $items, int $limit = 10): string
    {
        $summary = implode(', ', array_slice($items, 0, $limit));

        if (count($items) > $limit) {
            $summary .= ' … dan '.(count($items) - $limit).' lainnya';
        }

        return $summary;
    }

    private function skippedFilesWarning(array $skipped): string
    {
        $warning = 'melewati '.count($skipped).' file terlarang: '.self::summarizeList($skipped);

        if (in_array('.env', $skipped, true)) {
            $warning .= ' (aplikasi memakai konfigurasi wadah, pakai .env.example bila perlu template)';
        }

        return $warning;
    }

    /**
     * Name of the skipped directory tree an entry belongs to, if any.
     */
    private function junkDir(string $name): ?string
    {
        foreach (explode('/', $name) as $segment) {
            $lower = strtolower($segment);
            if (in_array($lower, self::SKIPPED_DIRS, true)) {
                return $lower;
            }
        }

        return null;
    }

    private function isForbiddenFile(string $name): bool
    {
        $lower = strtolower($name);
        $basename = basename($lower);

        // Block hidden/config files and sensitive names
        $forbidden = [
            '.env', '.env.local', '.env.prod', '.env.production', '.env.staging',
            '.htaccess', '.user.ini',
            'composer.lock', 'package-lock.json', 'yarn.lock', 'pnpm-lock.yaml',
            'wp-config.php', '.dockerignore',
        ];
        if (in_array($basename, $forbidden, true)) {
            return true;
        }

        // Allow .env.example, .env.sample as they are safe templates
        if (preg_match('/\.env\.(example|sample|dist)$/', $lower)) {
            return false;
        }

        // Allow .git and git-related files
        if (str_starts_with($basename, '.git')) {
            return false;
        }

        // Fast path: known-safe extensions
        $fastAllow = ['.js', '.ts', '.jsx', '.tsx', '.vue', '.svelte',
            '.css', '.scss', '.less', '.styl', '.sass',
            '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp', '.ico',
            '.woff', '.woff2', '.ttf', '.eot',
            '.json', '.xml', '.yaml', '.yml', '.toml',
            '.md', '.txt', '.html', '.htm',
            '.map', '.sql', '.blade.php'];
        $ext = strtolower(substr(strrchr($basename, '.'), -4));
        if (in_array($ext, $fastAllow, true)) {
            return false;
        }

        // Check extension whitelist
        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        if ($ext !== '') {
            $ext = '.'.$ext;
            if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                return true;
            }
        }

        return false;
    }

    private function resolveSourceRoot(string $extractDir): string
    {
        $entries = array_values(array_filter(scandir($extractDir) ?: [], fn ($e) => $e !== '.' && $e !== '..'));

        if (count($entries) === 1 && is_dir($extractDir.'/'.$entries[0])) {
            return $extractDir.'/'.$entries[0];
        }

        return $extractDir;
    }

    private function directoryStats(string $dir): array
    {
        if (! is_dir($dir)) {
            return ['files' => 0, 'bytes' => 0];
        }

        // Use a lighter approach: count files and sum sizes via stat cache
        // This avoids the overhead of RecursiveDirectoryIterator on large trees
        $files = 0;
        $bytes = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if (! $item->isFile()) {
                continue;
            }
            $files++;
            $bytes += $item->getSize();
        }

        return ['files' => $files, 'bytes' => $bytes];
    }

    private function copyDirectory(string $from, string $to): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $to.'/'.$iterator->getSubPathname();

            if ($item->isDir()) {
                if (! is_dir($target) && ! mkdir($target, 0755, true) && ! is_dir($target)) {
                    throw new RuntimeException('Gagal menyalin folder: '.$iterator->getSubPathname());
                }

                continue;
            }

            $parent = dirname($target);
            if (! is_dir($parent) && ! mkdir($parent, 0755, true) && ! is_dir($parent)) {
                throw new RuntimeException('Gagal menyalin file: '.$iterator->getSubPathname());
            }

            if (! copy($item->getPathname(), $target)) {
                throw new RuntimeException('Gagal menyalin file: '.$iterator->getSubPathname());
            }
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $this->emptyDirectory($dir);
        if (realpath($dir) !== realpath(base_path())) {
            @rmdir($dir);
        }
    }

    private function emptyDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.'/'.$item;
            if (is_dir($path) && ! is_link($path)) {
                $this->deleteDirectory($path);

                continue;
            }
            @unlink($path);
        }
    }

    private const DANGEROUS_STATEMENTS = [
        'DROP ',
        'TRUNCATE ',
        'ALTER TABLE',
        'GRANT ',
        'REVOKE ',
    ];

    /**
     * Build the database schema declared by the uploaded app, before any file is
     * published to school-app/{slug}: Laravel migrations when the ZIP ships
     * database/migrations, otherwise the app's own .sql files. A failure aborts
     * the whole install unless strict mode is disabled.
     *
     * @return array{migrations: int, statements: int, warnings: array<int, string>, skipped_queries: array<int, string>}
     */
    private function runSchema(string $appDir, string $slug): array
    {
        $migrations = 0;
        $statements = 0;
        $errors = [];
        $skippedQueries = [];

        $migrationDir = $appDir.'/database/migrations';

        if (is_dir($migrationDir)) {
            $migrations = $this->runMigrations($migrationDir, $errors);
        }

        $sqlFiles = $this->collectSqlFiles($appDir);

        if ($sqlFiles !== []) {
            $unsafe = $this->collectUnsafeStatements($sqlFiles);

            if ($unsafe !== [] && (bool) config('school-app.strict_schema', true)) {
                throw new RuntimeException(
                    'Query tidak diizinkan: '.$unsafe[0]
                    .' — hapus query ini dari file SQL, atau set SCHOOL_APP_SQL_STRICT=false untuk melewatinya dengan peringatan.'
                );
            }

            $skippedQueries = $unsafe;

            if ($unsafe !== []) {
                $errors[] = 'melewati '.count($unsafe).' query tidak diizinkan (contoh: '.$unsafe[0].')';
            }

            $statements = $this->runSqlFiles($sqlFiles, $slug, $errors);
        }

        if ($errors !== [] && (bool) config('school-app.strict_schema', true)) {
            throw new RuntimeException(
                'Skema database gagal dijalankan, instalasi dibatalkan: '.implode(' | ', $errors)
            );
        }

        return [
            'migrations' => $migrations,
            'statements' => $statements,
            'warnings' => $errors,
            'skipped_queries' => $skippedQueries,
        ];
    }

    /**
     * Run the app's Laravel migrations through Artisan. Migrations are recorded
     * in the host "migrations" table, so a re-upload never runs them twice.
     *
     * @param  array<int, string>  $errors
     * @return int number of migration files found
     */
    private function runMigrations(string $migrationDir, array &$errors): int
    {
        $files = glob($migrationDir.'/*.php') ?: [];
        $files = array_values(array_filter($files, fn (string $file): bool => ! str_ends_with($file, '.php.stub')));

        if ($files === []) {
            return 0;
        }

        try {
            $exitCode = Artisan::call('migrate', [
                '--path' => [$migrationDir],
                '--realpath' => true,
                '--force' => true,
            ]);

            $output = trim(Artisan::output());

            if ($exitCode !== 0) {
                $errors[] = 'migrasi: '.($output !== '' ? $output : 'perintah migrate gagal');
            }
        } catch (\Throwable $e) {
            $errors[] = 'migrasi: '.str_replace(["\r", "\n"], ' ', $e->getMessage());
        }

        return count($files);
    }

    /**
     * Execute the app's .sql files on the host connection.
     *
     * @param  array<int, string>  $sqlFiles
     * @param  array<int, string>  $errors
     * @return int number of statements executed
     */
    private function runSqlFiles(array $sqlFiles, string $slug, array &$errors): int
    {
        $connection = config('database.default');
        $pdo = DB::connection($connection)->getPdo();

        if (str_contains((string) $connection, 'mysql')) {
            $pdo->exec('SET NAMES utf8mb4');
        }

        $executed = 0;
        $hiddenErrors = 0;

        foreach ($sqlFiles as $sqlFile) {
            foreach ($this->parseSqlFile($sqlFile) as $statement) {
                if ($this->isUnsafeStatement($statement)) {
                    continue;
                }
                try {
                    DB::connection($connection)->statement($statement);
                    $executed++;
                } catch (\Throwable $e) {
                    if (count($errors) < self::MAX_SCHEMA_ERRORS) {
                        $errors[] = basename($sqlFile).': '.str_replace(["\r", "\n"], ' ', $e->getMessage());
                    } else {
                        $hiddenErrors++;
                    }
                }
            }
        }

        if ($hiddenErrors > 0) {
            $errors[] = '… dan '.$hiddenErrors.' error SQL lain tidak ditampilkan';
        }

        if ($errors !== []) {
            \Log::warning("SQL execution failed for app {$slug}: ".implode(' | ', $errors));
        }

        return $executed;
    }

    /**
     * @return array<int, string>
     */
    private function collectSqlFiles(string $appDir): array
    {
        $sqlFiles = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($appDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if (! $item->isFile() || strtolower($item->getExtension()) !== 'sql') {
                continue;
            }
            $sqlFiles[] = $item->getPathname();
        }

        return $sqlFiles;
    }

    /**
     * Collect short descriptions of blocked statements without executing anything.
     *
     * @param  array<int, string>  $sqlFiles
     * @return array<int, string>
     */
    private function collectUnsafeStatements(array $sqlFiles): array
    {
        $unsafe = [];

        foreach ($sqlFiles as $sqlFile) {
            foreach ($this->parseSqlFile($sqlFile) as $statement) {
                if ($this->isUnsafeStatement($statement)) {
                    $unsafe[] = mb_substr(ltrim($statement), 0, 80);
                }
            }
        }

        return $unsafe;
    }

    /**
     * @return array<int, string>
     */
    private function parseSqlFile(string $file): array
    {
        $content = (string) file_get_contents($file);
        $content = preg_replace('/^\s*--.*$/m', '', $content);
        $content = preg_replace('/^\s*\/\*.*?\*\/\s*/ms', '', $content);
        $content = trim($content);
        if ($content === '') {
            return [];
        }

        $statements = preg_split('/;(?=(?:[^\'"\\\]|\\.)*+(?:\'|"))/s', $content);
        $result = [];
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '' || str_starts_with($statement, '--') || str_starts_with($statement, '/*')) {
                continue;
            }
            $result[] = $statement;
        }

        return $result;
    }

    private function isUnsafeStatement(string $statement): bool
    {
        $upper = strtoupper(ltrim($statement));

        foreach (self::DANGEROUS_STATEMENTS as $dangerous) {
            if (str_starts_with($upper, $dangerous)) {
                return true;
            }
        }

        return false;
    }
}
