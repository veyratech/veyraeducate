<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupCommand extends Command
{
    protected $signature = 'backup:run {--days=30 : Number of days to keep backups}
                            {--only-db : Backup database only (skip school-app folder)}
                            {--only-apps : Backup school-app folder only}';

    protected $description = 'Backup database dan school-app folder, rotasi backup lama.';

    public function handle(): int
    {
        $keepDays = (int) $this->option('days');
        $onlyDb = $this->option('only-db');
        $onlyApps = $this->option('only-apps');

        $this->info('[ '.now()->format('Y-m-d H:i:s').' ] Mulai backup...');

        if (! $onlyApps) {
            $this->backupDatabase();
        }

        if (! $onlyDb) {
            $this->backupSchoolApps();
        }

        $this->rotateBackups($keepDays);

        $this->info('Backup selesai.');

        return self::SUCCESS;
    }

    private function backupDatabase(): void
    {
        $this->line('  [Database] Memulai backup...');

        $driver = DB::connection()->getDriverName();
        $database = DB::connection()->getDatabaseName();
        $filename = 'backup-db-'.now()->format('Y-m-d_His').'.sql';
        $path = storage_path('backups/'.$filename);

        if (! is_dir(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }

        if ($driver === 'mysql') {
            $mysqldump = getenv('MYSQLDUMP') ?: 'mysqldump';
            $result = shell_exec(sprintf(
                '"%s" --single-transaction --quick --lock-tables=false "%s" > "%s" 2>&1',
                $mysqldump,
                escapeshellarg($database),
                escapeshellarg($path)
            ));
            if (! is_file($path) || filesize($path) < 100) {
                $this->warn('  mysqldump tidak tersedia atau gagal, menggunakan fallback PDO.');
                $this->dumpViaPdo($path, $driver, $database);
            }
        } else {
            $this->dumpViaPdo($path, $driver, $database);
        }

        if (is_file($path)) {
            $size = round(filesize($path) / 1024, 2);
            $this->line("  [Database] OK — {$filename} ({$size} KB)");
        } else {
            $this->warn('  [Database] GAGAL — file tidak ter-create.');
        }
    }

    private function backupSchoolApps(): void
    {
        $this->line('  [School Apps] Memulai backup...');

        $baseDir = config('school-app.path', base_path('school-app'));
        $filename = 'backup-apps-'.now()->format('Y-m-d_His').'.zip';
        $path = storage_path('backups/'.$filename);

        if (! is_dir(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }

        if (! is_dir($baseDir)) {
            $this->line('  [School Apps] Folder tidak ditemukan, dilewati.');

            return;
        }

        $zip = new \ZipArchive;
        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->warn('  [School Apps] GAGAL — tidak bisa membuat ZIP.');

            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $count = 0;
        foreach ($iterator as $file) {
            $relativePath = str_replace($baseDir.DIRECTORY_SEPARATOR, '', $file->getPathname());
            if ($file->isFile()) {
                $zip->addFile($file->getPathname(), $relativePath);
                $count++;
            }
        }

        $zip->close();

        if (is_file($path)) {
            $size = round(filesize($path) / 1024, 2);
            $this->line("  [School Apps] OK — {$filename} ({$size} KB, {$count} files)");
        }
    }

    private function dumpViaPdo(string $path, string $driver, string $database): void
    {
        $pdo = DB::connection()->getPdo();
        $handle = fopen($path, 'w');

        if ($handle === false) {
            $this->warn('  Database backup: GAGAL — tidak bisa membuka file untuk ditulis.');

            return;
        }

        fwrite($handle, "-- Veyra Educate Database Backup\n");
        fwrite($handle, "-- Driver: {$driver} | Database: {$database}\n");
        fwrite($handle, '-- Generated: '.now()->toDateTimeString()."\n\n");

        $tables = match ($driver) {
            'mysql' => DB::select('SHOW TABLES'),
            'sqlite' => DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"),
            default => [],
        };

        foreach ($tables as $tableObj) {
            $tableName = $driver === 'sqlite' ? $tableObj->name : reset((array) $tableObj);

            fwrite($handle, "-- Table: {$tableName}\n");

            if ($driver === 'mysql') {
                $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (! empty($create)) {
                    fwrite($handle, (array) $create[0]['Create Table'].";\n\n");
                }
            } elseif ($driver === 'sqlite') {
                $meta = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
                if (! empty($meta) && $meta[0]->sql) {
                    fwrite($handle, $meta[0]->sql.";\n\n");
                }
            }

            $rows = DB::table($tableName)->cursor();
            foreach ($rows as $row) {
                $cols = array_keys((array) $row);
                $quotedCols = array_map(fn ($c) => ($driver === 'sqlite' ? '"' : '`').$c.($driver === 'sqlite' ? '"' : '`'), $cols);
                $vals = [];
                foreach ((array) $row as $val) {
                    if ($val === null) {
                        $vals[] = 'NULL';
                    } elseif (is_numeric($val) && ! is_string($val)) {
                        $vals[] = $val;
                    } else {
                        $vals[] = $pdo->quote((string) $val);
                    }
                }
                fwrite($handle, "INSERT INTO `{$tableName}` (".implode(', ', $quotedCols).') VALUES ('.implode(', ', $vals).");\n");
            }

            fwrite($handle, "\n");
        }

        fclose($handle);
        $this->line('  Database (PDO fallback): '.round(filesize($path) / 1024, 2).' KB');
    }

    private function rotateBackups(int $days): void
    {
        $this->line('  [Rotasi] Membersihkan backup lama...');

        $backupDir = storage_path('backups');
        if (! is_dir($backupDir)) {
            return;
        }

        $deleted = 0;
        foreach (scandir($backupDir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = $backupDir.'/'.$file;
            if (is_file($fullPath) && filemtime($fullPath) < now()->subDays($days)->timestamp) {
                unlink($fullPath);
                $deleted++;
            }
        }

        $this->line("  [Rotasi] {$deleted} backup lama dihapus (lebih dari {$days} hari).");
    }
}
