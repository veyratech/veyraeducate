<?php

namespace Tests\Feature;

use App\Models\Deployment;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use ZipArchive;

class AdminAppUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }

        $this->tempFiles = [];

        parent::tearDown();
    }

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);

        return User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    /**
     * @param  array<string, string>  $files
     */
    private function zipFile(array $files, string $name = 'app.zip'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'veyra-upload-').'.zip';
        $this->tempFiles[] = $path;

        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($files as $file => $contents) {
            $zip->addFromString($file, $contents);
        }

        $zip->close();

        return new UploadedFile($path, $name, 'application/zip', null, true);
    }

    public function test_guest_cannot_upload_app(): void
    {
        $this->post(route('admin.web-apps.upload'), ['slug' => 'ujian'])
            ->assertRedirect('/admin/login');
    }

    public function test_school_user_forbidden_on_upload(): void
    {
        $this->seed(PlanSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.web-apps.upload'), ['slug' => 'ujian'])
            ->assertForbidden();
    }

    public function test_admin_can_upload_zip_app(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile(['index.blade.php' => '<div>Ujian</div>']),
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertFileExists($this->isolatedSchoolAppPath().'/ujian/index.blade.php');
        $this->assertDatabaseHas('web_apps', ['slug' => 'ujian', 'name' => 'ujian']);

        $deployment = Deployment::query()->where('slug', 'ujian')->firstOrFail();
        $this->assertSame('success', $deployment->status);
        $this->assertSame($admin->id, $deployment->created_by);
        $this->assertSame(1, $deployment->file_count);
        $this->assertNotNull($deployment->deployed_at);
    }

    public function test_upload_rejects_file_that_is_not_a_zip(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => UploadedFile::fake()->createWithContent('app.zip', 'ini bukan file zip'),
            ])
            ->assertSessionHasErrors('app_zip');

        $this->assertDatabaseMissing('web_apps', ['slug' => 'ujian']);
        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/ujian');
    }

    public function test_upload_rejects_invalid_slug(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'Rapat Besar',
                'app_zip' => $this->zipFile(['index.blade.php' => '<div>App</div>']),
            ])
            ->assertSessionHasErrors('slug');

        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/Rapat Besar');
    }

    public function test_upload_skips_env_file_with_warning(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile([
                    'index.blade.php' => '<div>App</div>',
                    '.env' => 'APP_KEY=rahasia',
                ]),
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $status = session('status');
        $this->assertStringContainsString('Melewati file terlarang', $status);
        $this->assertStringContainsString('.env', $status);

        $this->assertFileExists($this->isolatedSchoolAppPath().'/ujian/index.blade.php');
        $this->assertFileDoesNotExist($this->isolatedSchoolAppPath().'/ujian/.env');
        $this->assertDatabaseHas('web_apps', ['slug' => 'ujian']);
    }

    public function test_upload_rejects_zip_with_only_forbidden_files(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile([
                    '.env' => 'APP_KEY=rahasia',
                ]),
            ])
            ->assertSessionHasErrors('app_zip');

        $message = session('errors')->first('app_zip');
        $this->assertStringContainsString('hanya berisi file terlarang', $message);

        $this->assertDatabaseMissing('web_apps', ['slug' => 'ujian']);
        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/ujian');
    }

    public function test_upload_aborts_when_sql_fails(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile([
                    'index.blade.php' => '<div>App</div>',
                    'schema.sql' => 'BUKAN QUERY YANG VALID',
                ]),
            ])
            ->assertSessionHasErrors('app_zip');

        $this->assertStringContainsString('Skema database gagal', session('errors')->first('app_zip'));
        $this->assertDatabaseMissing('web_apps', ['slug' => 'ujian']);
        $this->assertDirectoryDoesNotExist($this->isolatedSchoolAppPath().'/ujian');
    }

    public function test_upload_skips_unsafe_sql_when_not_strict(): void
    {
        config()->set('school-app.strict_schema', false);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile([
                    'index.blade.php' => '<div>App</div>',
                    'schema.sql' => "GRANT USAGE ON SCHEMA private TO authenticated; CREATE TABLE IF NOT EXISTS ujian_peserta (id INTEGER PRIMARY KEY, nama TEXT DEFAULT '-')",
                ]),
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertStringContainsString('query tidak diizinkan', session('status'));
        $this->assertTrue(Schema::hasTable('ujian_peserta'));
        $this->assertFileExists($this->isolatedSchoolAppPath().'/ujian/index.blade.php');
    }

    public function test_upload_keeps_notes_small_for_many_skipped_files(): void
    {
        $files = ['index.blade.php' => '<div>App</div>'];
        for ($i = 0; $i < 60; $i++) {
            $files["junk{$i}.zzzunknown"] = 'x';
        }

        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile($files),
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertStringContainsString('file terlarang', session('status'));

        $deployment = Deployment::query()->where('slug', 'ujian')->firstOrFail();
        $this->assertLessThanOrEqual(6000, mb_strlen((string) $deployment->notes));
    }

    public function test_upload_builds_schema_from_sql_file(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'ujian',
                'app_zip' => $this->zipFile([
                    'index.blade.php' => '<div>App</div>',
                    'schema.sql' => 'CREATE TABLE IF NOT EXISTS ujian_peserta (id INTEGER PRIMARY KEY, nama TEXT)',
                ]),
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertTrue(Schema::hasTable('ujian_peserta'));
        $this->assertFileExists($this->isolatedSchoolAppPath().'/ujian/index.blade.php');
        $this->assertStringContainsString('statement SQL', session('status'));
    }

    public function test_upload_builds_schema_from_laravel_migrations(): void
    {
        $admin = $this->admin();

        $migration = <<<'PHP'
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                public function up(): void
                {
                    Schema::create('ujian_siswa', function (Blueprint $table) {
                        $table->id();
                        $table->string('nama');
                    });
                }

                public function down(): void
                {
                    Schema::dropIfExists('ujian_siswa');
                }
            };
            PHP;

        $zip = fn (): UploadedFile => $this->zipFile([
            'index.blade.php' => '<div>App</div>',
            'database/migrations/2026_01_01_000001_create_ujian_siswa_table.php' => $migration,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), ['slug' => 'ujian', 'app_zip' => $zip()])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertTrue(Schema::hasTable('ujian_siswa'));
        $this->assertStringContainsString('file migrasi', session('status'));

        // Re-upload runs the migration path again without failing on "table already exists".
        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), ['slug' => 'ujian', 'app_zip' => $zip()])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Deployment::query()->where('slug', 'ujian')->count());
    }

    public function test_reupload_keeps_custom_app_name(): void
    {
        $admin = $this->admin();
        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->post(route('admin.web-apps.upload'), [
                'slug' => 'rapor',
                'app_zip' => $this->zipFile(['index.blade.php' => '<div>Versi baru</div>']),
            ])
            ->assertRedirect(route('admin.web-apps.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('web_apps', ['slug' => 'rapor', 'name' => 'Rapor Digital']);
        $this->assertSame(1, Deployment::query()->where('slug', 'rapor')->count());
    }

    public function test_admin_can_view_installed_app_detail(): void
    {
        $admin = $this->admin();
        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $this->actingAs($admin)
            ->get(route('admin.web-apps.show', 'rapor'))
            ->assertOk()
            ->assertSee('Rapor Digital');
    }
}
