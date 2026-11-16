<?php

namespace Tests\Feature;

use App\Services\SchoolAppInstaller;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

class SchoolAppInstallerTest extends TestCase
{
    use RefreshDatabase;

    private function installer(): SchoolAppInstaller
    {
        return $this->app->make(SchoolAppInstaller::class);
    }

    private function makeZip(array $files, ?string $zipName = null): UploadedFile
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'veyra-zip-');
        $zipPath .= '.zip';

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }

        $zip->close();

        return new UploadedFile($zipPath, $zipName ?? basename($zipPath), 'application/zip', null, true);
    }

    /**
     * @return array<int, string>
     */
    private function installTempDirs(): array
    {
        $dir = storage_path('app/private');

        if (! is_dir($dir)) {
            return [];
        }

        return array_values(array_filter(
            scandir($dir) ?: [],
            fn (string $entry) => str_starts_with($entry, 'school-app-install-')
        ));
    }

    public function test_install_writes_package_files_and_meta(): void
    {
        $meta = $this->installer()->install($this->makeZip([
            'routes/school-app.php' => '<?php // app routes',
            'resources/views/panel.blade.php' => '<div>Panel</div>',
            'README.md' => 'Sekolah app',
        ], 'aplikasi-sekolah.zip'), 'rapor');

        $this->assertFileExists($this->isolatedSchoolAppPath().'/rapor/routes/school-app.php');
        $this->assertFileExists($this->isolatedSchoolAppPath().'/rapor/resources/views/panel.blade.php');
        $this->assertFileExists($this->isolatedSchoolAppPath().'/rapor/README.md');
        $this->assertFileExists($this->isolatedSchoolAppPath().'/rapor/'.SchoolAppInstaller::META_FILE);
        $this->assertSame(3, $meta['files']);
        $this->assertDatabaseHas('web_apps', ['slug' => 'rapor', 'name' => 'rapor']);
    }

    public function test_install_strips_single_root_folder_from_zip(): void
    {
        $this->installer()->install($this->makeZip([
            'my-app/routes/web.php' => '<?php',
            'my-app/app/Models/Student.php' => '<?php',
        ]), 'absensi');

        $this->assertFileExists($this->isolatedSchoolAppPath().'/absensi/routes/web.php');
        $this->assertFileExists($this->isolatedSchoolAppPath().'/absensi/app/Models/Student.php');
        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/absensi/my-app');
    }

    public function test_install_skips_env_file_with_warning(): void
    {
        $meta = $this->installer()->install($this->makeZip([
            '.env' => "DB_HOST=localhost\nDB_PASS=secret",
            'index.blade.php' => '<div>ok</div>',
        ]), 'test');

        $this->assertFileDoesNotExist($this->isolatedSchoolAppPath().'/test/.env');
        $this->assertFileExists($this->isolatedSchoolAppPath().'/test/index.blade.php');
        $this->assertSame(1, $meta['files']);
        $this->assertContains('.env', $meta['skipped']);
        $this->assertStringContainsString('.env', implode(' | ', $meta['schema']['warnings']));
    }

    public function test_install_rejects_zip_with_only_forbidden_files(): void
    {
        $rejected = false;

        try {
            $this->installer()->install($this->makeZip([
                '.env' => 'DB_HOST=localhost',
                'composer.lock' => '{}',
            ]), 'test');
        } catch (RuntimeException $e) {
            $rejected = true;
            $this->assertStringContainsString('hanya berisi file terlarang', $e->getMessage());
        }

        $this->assertTrue($rejected, 'Install yang hanya berisi file terlarang seharusnya ditolak.');
    }

    public function test_install_rejects_path_traversal_entries(): void
    {
        $rejected = false;

        try {
            $this->installer()->install($this->makeZip([
                '../evil.php' => '<?php echo 1;',
                'ok.txt' => 'safe',
            ]), 'test');
        } catch (RuntimeException) {
            $rejected = true;
        }

        $this->assertTrue($rejected, 'Install dengan path traversal seharusnya ditolak.');
        $this->assertFileDoesNotExist(base_path('evil.php'));
        $this->assertFileDoesNotExist($this->isolatedSchoolAppPath().'/test/evil.php');
    }

    public function test_rejected_install_leaves_no_temporary_directory(): void
    {
        $before = $this->installTempDirs();

        try {
            $this->installer()->install($this->makeZip(['../evil.php' => '<?php echo 1;']), 'test');
        } catch (RuntimeException) {
            // expected: the archive is rejected before extraction
        }

        $this->assertSame($before, $this->installTempDirs());
    }

    public function test_uninstall_removes_installed_app(): void
    {
        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->assertDirectoryExists($this->isolatedSchoolAppPath().'/rapor');

        $this->installer()->uninstall('rapor');

        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/rapor');
        $this->assertSoftDeleted('web_apps', ['slug' => 'rapor']);
    }

    public function test_install_rejects_dangerous_sql_statements(): void
    {
        $dangerous = [
            'DROP TABLE students;',
            'TRUNCATE TABLE users;',
            'ALTER TABLE schools ADD COLUMN foo VARCHAR(255);',
            'GRANT USAGE ON SCHEMA private TO authenticated;',
            'REVOKE ALL ON students FROM app_user;',
        ];

        foreach ($dangerous as $sql) {
            $rejected = false;
            try {
                $this->installer()->install($this->makeZip([
                    'setup.sql' => $sql,
                ]), 'test-danger-'.$sql);
            } catch (RuntimeException) {
                $rejected = true;
            }
            $this->assertTrue($rejected, 'SQL berbahaya harus ditolak: '.$sql);
        }
    }

    public function test_install_skips_unsafe_statements_when_not_strict(): void
    {
        config()->set('school-app.strict_schema', false);

        $meta = $this->installer()->install($this->makeZip([
            'index.blade.php' => '<div>ok</div>',
            'setup.sql' => "GRANT USAGE ON SCHEMA private TO authenticated; CREATE TABLE IF NOT EXISTS app_siswa (id INTEGER PRIMARY KEY, nama TEXT DEFAULT '-')",
        ]), 'test-grant');

        $this->assertFileExists($this->isolatedSchoolAppPath().'/test-grant/index.blade.php');
        $this->assertSame(1, $meta['schema']['statements']);
        $this->assertNotEmpty($meta['schema']['warnings']);
        $this->assertStringContainsStringIgnoringCase(
            'grant usage',
            implode(' | ', $meta['schema']['warnings'])
        );
    }

    public function test_install_skips_node_modules_and_git_trees(): void
    {
        $meta = $this->installer()->install($this->makeZip([
            'index.blade.php' => '<div>ok</div>',
            'node_modules/acorn/index.js' => 'module.exports = {};',
            'node_modules/acorn/package.json' => '{"name":"acorn"}',
            '.git/objects/pack/a.pack' => 'binary',
        ]), 'test-junk');

        $this->assertFileExists($this->isolatedSchoolAppPath().'/test-junk/index.blade.php');
        $this->assertFileDoesNotExist($this->isolatedSchoolAppPath().'/test-junk/node_modules/acorn/index.js');
        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/test-junk/.git');
        $this->assertSame(1, $meta['files']);
        $this->assertSame(2, $meta['skipped_dirs']['node_modules']);
        $this->assertSame(1, $meta['skipped_dirs']['.git']);
        $this->assertStringContainsString('node_modules', implode(' | ', $meta['schema']['warnings']));
    }

    public function test_install_allows_safe_insert_statements(): void
    {
        $this->seed(PlanSeeder::class);

        $this->installer()->install($this->makeZip([
            'setup.sql' => "INSERT INTO plans (code, label, price_idr, duration_months, duration_label, features, sort_order, is_active) VALUES ('test-plan', 'Test', 10000, 1, 'Bulanan', '[]', 99, true);",
        ]), 'test-safe');

        $this->assertDatabaseHas('plans', ['code' => 'test-plan']);
    }
}
