<?php

namespace Tests;

use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use ZipArchive;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $path = $this->isolatedSchoolAppPath();

        File::deleteDirectory($path);
        File::makeDirectory($path, 0755, true);
    }

    protected function tearDown(): void
    {
        if (isset($this->app) && $this->schoolAppPathIsIsolated((string) config('school-app.path', ''))) {
            File::deleteDirectory(config('school-app.path'));
        }

        parent::tearDown();
    }

    /**
     * Resolve the school-app directory used by tests, failing hard if it is not
     * the isolated path from phpunit.xml so the suite can never wipe real apps.
     */
    protected function isolatedSchoolAppPath(): string
    {
        $path = (string) config('school-app.path', '');

        if (! $this->schoolAppPathIsIsolated($path)) {
            $this->fail('school-app.path is not isolated for testing: '.$path);
        }

        return $path;
    }

    private function schoolAppPathIsIsolated(string $path): bool
    {
        return str_contains(str_replace('\\', '/', $path), 'storage/framework/testing/');
    }

    /**
     * Materialise an installed school app through the installer service, for
     * tests that only need the app to exist instead of exercising the upload.
     *
     * @param  array<string, string>  $files
     * @param  array<string, mixed>  $attributes
     */
    protected function installSchoolApp(string $slug, array $files = [], array $attributes = []): WebApp
    {
        $files = $files ?: ['index.blade.php' => '<div>App</div>'];

        $zipPath = tempnam(sys_get_temp_dir(), 'veyra-zip-').'.zip';

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }

        $zip->close();

        try {
            $this->app->make(SchoolAppInstaller::class)
                ->install(new UploadedFile($zipPath, $slug.'.zip', 'application/zip', null, true), $slug);
        } finally {
            @unlink($zipPath);
        }

        $app = WebApp::query()->where('slug', $slug)->firstOrFail();

        if ($attributes !== []) {
            $app->update($attributes);
        }

        return $app->fresh();
    }
}
