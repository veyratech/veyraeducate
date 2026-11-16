<?php

namespace App\Http\Controllers;

use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use App\Services\SchoolAppViewFinder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SchoolAppController extends Controller
{
    public function __construct(
        private readonly SchoolAppInstaller $installer,
        private readonly SchoolAppViewFinder $viewFinder
    ) {}

    public function __invoke(string $slug, string $path = ''): View|Response
    {
        $webApp = WebApp::query()->where('slug', $slug)->first();
        if ($webApp && ! $webApp->enabled) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $user = auth()->user();
        $school = $user?->school;

        if ($school && ! $school->hasAccessToApp($slug)) {
            abort(403, 'Akses ditolak. Aplikasi ini belum aktif untuk sekolah Anda.');
        }

        $appPath = $this->installer->getAppPath($slug);

        if ($appPath === null) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $this->refreshViewPaths($slug, $appPath);

        if ($path === '') {
            $viewName = 'index';
        } else {
            $viewName = $this->normalizePath($path);
        }

        $viewFile = $this->findViewFile($slug, $viewName, $appPath);

        if ($viewFile !== null) {
            $meta = $this->installer->getApp($slug);
            $data = [
                'appSlug' => $slug,
                'appName' => $meta['name'] ?? $slug,
                'appVersion' => $meta['version'] ?? null,
                'appDescription' => $meta['description'] ?? null,
            ];

            return view()->file($viewFile, $data);
        }

        return $this->serveStaticFallback($slug, $path, $appPath);
    }

    public function asset(Request $request, string $slug, string $path): Response
    {
        $appPath = $this->installer->getAppPath($slug);

        if ($appPath === null) {
            abort(404, 'Asset tidak ditemukan.');
        }

        $safePath = $this->sanitizePath($path);
        $filePath = $appPath.'/'.$safePath;

        if (! File::exists($filePath) || ! is_file($filePath)) {
            abort(404, 'Asset tidak ditemukan.');
        }

        if (! $this->isSafeAssetPath($filePath, $appPath)) {
            abort(403, 'Akses ditolak.');
        }

        return $this->fileResponse($filePath);
    }

    protected function refreshViewPaths(string $slug, string $appPath): void
    {
        $this->viewFinder->removeAppViews($slug);
        $this->viewFinder->addAppViews($slug, $appPath);
    }

    protected function findViewFile(string $slug, string $viewName, string $appPath): ?string
    {
        $appView = $this->viewFinder->findAppView($viewName, $slug);
        if ($appView !== null) {
            return $appView;
        }

        $candidates = [
            $appPath.'/'.$viewName.'.blade.php',
            $appPath.'/'.$viewName.'.php',
            $appPath.'/index.blade.php',
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) && $this->isSafeAssetPath($candidate, $appPath)) {
                return $candidate;
            }
        }

        return null;
    }

    protected function serveStaticFallback(string $slug, string $path, string $appPath): Response
    {
        if ($path === '') {
            $indexPath = $appPath.'/index.html';
            if (File::exists($indexPath) && $this->isSafeAssetPath($indexPath, $appPath)) {
                return response(File::get($indexPath), 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ]);
            }
        }

        abort(404, 'Halaman tidak ditemukan.');
    }

    protected function normalizePath(string $path): string
    {
        // Path seperti "pages/settings" menjadi "pages.settings" agar cocok
        // dengan konvensi nama view Blade (directory / file dipisah titik).
        $path = ltrim($path, '/');
        $path = preg_replace('/[^a-zA-Z0-9_\-\.\/]/', '', $path);
        $parts = array_filter(explode('/', $path));

        return implode('.', $parts);
    }

    protected function sanitizePath(string $path): string
    {
        $path = str_replace("\0", '', $path);
        $path = str_replace('\\', '/', $path);
        $parts = array_filter(
            explode('/', $path),
            fn (string $part): bool => $part !== '' && $part !== '.' && $part !== '..'
        );

        return implode('/', $parts);
    }

    protected function isSafeAssetPath(string $filePath, string $appPath): bool
    {
        $realApp = realpath($appPath);
        $realFile = realpath($filePath);

        if ($realApp === false || $realFile === false) {
            return false;
        }

        return str_starts_with($realFile, $realApp.DIRECTORY_SEPARATOR);
    }

    protected function fileResponse(string $filePath): Response
    {
        $mimeType = File::mimeType($filePath);
        $expires = strtotime('+1 year');

        return response(File::get($filePath), 200, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Cache-Control' => 'public, max-age=31536000',
            'Expires' => gmdate('D, d M Y H:i:s', $expires).' GMT',
        ]);
    }
}
