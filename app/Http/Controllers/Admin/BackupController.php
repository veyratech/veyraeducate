<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * Tampilkan halaman status database dan cadangan data.
     */
    public function index(Request $request)
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $databaseName = (string) $connection->getDatabaseName();

        $tables = [];
        $totalSizeFormatted = '0 KB';
        $totalRows = 0;

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $rawTables = DB::select('
                SELECT 
                    table_name AS `name`,
                    table_rows AS `rows`,
                    (data_length + index_length) AS `size_bytes`
                FROM information_schema.tables
                WHERE table_schema = ?
                ORDER BY table_name ASC
            ', [$databaseName]);

            $totalBytes = 0;
            foreach ($rawTables as $t) {
                $bytes = (int) ($t->size_bytes ?? 0);
                $totalBytes += $bytes;
                $rows = (int) ($t->rows ?? 0);
                $totalRows += $rows;
                $tables[] = [
                    'name' => (string) $t->name,
                    'rows' => $rows,
                    'size' => $this->formatBytes($bytes),
                ];
            }
            $totalSizeFormatted = $this->formatBytes($totalBytes);
        } else {
            // SQLite or fallback
            $rawTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name ASC");
            foreach ($rawTables as $t) {
                $count = DB::table($t->name)->count();
                $totalRows += $count;
                $tables[] = [
                    'name' => (string) $t->name,
                    'rows' => $count,
                    'size' => '-',
                ];
            }
            $totalSizeFormatted = 'SQLite Active';
        }

        return view('admin.backup.index', [
            'driver' => $driver,
            'databaseName' => $databaseName,
            'tables' => $tables,
            'totalTables' => count($tables),
            'totalRows' => $totalRows,
            'totalSize' => $totalSizeFormatted,
        ]);
    }

    /**
     * Unduh file cadangan SQL lengkap (.sql) via HTTP streaming.
     */
    public function download(): StreamedResponse
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $databaseName = (string) $connection->getDatabaseName();
        $filename = 'backup-veyraeducate-'.now()->format('Y-m-d_His').'.sql';

        return response()->streamDownload(function () use ($connection, $driver, $databaseName) {
            $pdo = $connection->getPdo();
            $appName = (string) config('app.name', 'Veyra Educate');
            $date = now()->toDateTimeString();

            echo "-- ========================================================\n";
            echo "-- {$appName} Database Backup\n";
            echo "-- Driver: {$driver} | Database: {$databaseName}\n";
            echo "-- Created at: {$date}\n";
            echo "-- ========================================================\n\n";

            if ($driver === 'mysql' || $driver === 'mariadb') {
                echo "SET FOREIGN_KEY_CHECKS=0;\n";
                echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
                echo "SET time_zone = '+00:00';\n\n";

                $tables = DB::select('SHOW TABLES');

                foreach ($tables as $t) {
                    $tableName = (array) $t;
                    $table = reset($tableName);

                    echo "--\n-- Table structure for table `{$table}`\n--\n";
                    echo "DROP TABLE IF EXISTS `{$table}`;\n";

                    $create = $connection->select("SHOW CREATE TABLE `{$table}`");
                    if (! empty($create)) {
                        $createArray = (array) $create[0];
                        $createSql = $createArray['Create Table'] ?? '';
                        echo $createSql.";\n\n";
                    }

                    echo "--\n-- Dumping data for table `{$table}`\n--\n";
                    $count = $connection->table($table)->count();
                    if ($count > 0) {
                        $orderCol = $this->getPrimaryKeyOrFirstColumn($connection, $table);
                        $connection->table($table)->orderBy($orderCol)->chunk(250, function ($rows) use ($table, $pdo) {
                            $lines = [];
                            foreach ($rows as $row) {
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
                                $lines[] = '('.implode(', ', $vals).')';
                            }
                            if (! empty($lines)) {
                                echo "INSERT INTO `{$table}` VALUES\n".implode(",\n", $lines).";\n\n";
                            }
                        });
                    }
                }

                echo "SET FOREIGN_KEY_CHECKS=1;\n";
                echo "-- Backup completed successfully.\n";
            } else {
                // SQLite dump logic
                $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name ASC");
                foreach ($tables as $t) {
                    $table = $t->name;
                    echo "--\n-- Table: {$table}\n--\n";
                    echo "DROP TABLE IF EXISTS \"{$table}\";\n";
                    echo $t->sql.";\n\n";

                    $rows = DB::table($table)->get();
                    if ($rows->isNotEmpty()) {
                        foreach ($rows as $row) {
                            $cols = array_keys((array) $row);
                            $quotedCols = array_map(fn ($c) => "\"{$c}\"", $cols);
                            $vals = [];
                            foreach ((array) $row as $val) {
                                if ($val === null) {
                                    $vals[] = 'NULL';
                                } else {
                                    $vals[] = $pdo->quote((string) $val);
                                }
                            }
                            echo "INSERT INTO \"{$table}\" (".implode(', ', $quotedCols).') VALUES ('.implode(', ', $vals).");\n";
                        }
                        echo "\n";
                    }
                }
                echo "-- SQLite dump completed.\n";
            }
        }, $filename, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Optimasi performa tabel database (OPTIMIZE TABLE / VACUUM).
     */
    public function optimize(): RedirectResponse
    {
        $driver = DB::connection()->getDriverName();

        try {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $tables = DB::select('SHOW TABLES');
                foreach ($tables as $t) {
                    $arr = (array) $t;
                    $table = reset($arr);
                    DB::statement("OPTIMIZE TABLE `{$table}`");
                }
            } elseif ($driver === 'sqlite') {
                if (DB::transactionLevel() === 0) {
                    DB::statement('VACUUM');
                }
            }

            return back()->with('status', 'Seluruh tabel database berhasil dioptimasi dan dibersihkan dari indeks usang.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal melakukan optimasi tabel: '.$e->getMessage());
        }
    }

    private function getPrimaryKeyOrFirstColumn($connection, string $table): string
    {
        try {
            $cols = $connection->select("SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY'");
            if (! empty($cols)) {
                return $cols[0]->Column_name;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 'id';
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), 2).' '.$units[$i];
    }
}
