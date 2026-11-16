<?php

namespace App\Services;

use Illuminate\View\FileViewFinder;

class SchoolAppViewFinder extends FileViewFinder
{
    protected static string $appPrefix = 'app.';

    /** @var array<string, string> */
    protected array $appViews = [];

    public function getAppViews(): array
    {
        return $this->appViews;
    }

    public function addAppView(string $slug, string $viewName, string $path): void
    {
        $this->appViews[$slug.'.'.strtoupper($viewName)] = $path;
    }

    public function addAppViews(string $slug, string $appDir): void
    {
        $viewPaths = $this->scanAppViews($appDir, $slug);
        foreach ($viewPaths as $viewName => $viewPath) {
            $this->addAppView($slug, $viewName, $viewPath);
        }
    }

    public function removeAppViews(string $slug): void
    {
        $keys = array_filter(
            array_keys($this->appViews),
            fn (string $key) => str_starts_with($key, $slug.'.')
        );
        foreach ($keys as $key) {
            unset($this->appViews[$key]);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function scanAppViews(string $appDir, string $slug): array
    {
        $views = [];
        if (! is_dir($appDir)) {
            return $views;
        }

        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($appDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        ) as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $relativePath = $this->getRelativePath($file->getPathname(), $appDir);
            if (! str_ends_with($relativePath, '.blade.php')) {
                continue;
            }
            $viewName = $this->normalizeViewName($relativePath);
            $views[$viewName] = $file->getPathname();
        }

        return $views;
    }

    protected function getRelativePath(string $fullPath, string $basePath): string
    {
        $normalizedFull = str_replace('\\', '/', $fullPath);
        $normalizedBase = str_replace('\\', '/', $basePath);
        if (str_starts_with($normalizedFull, $normalizedBase.'/')) {
            return substr($normalizedFull, strlen($normalizedBase) + 1);
        }

        return $normalizedFull;
    }

    protected function normalizeViewName(string $relativePath): string
    {
        $path = str_replace('\\', '/', $relativePath);
        $path = preg_replace('/\.blade\.php$/', '', $path);

        if ($path === 'index' || $path === '') {
            return 'index';
        }

        $parts = explode('/', $path);
        if (end($parts) === 'index') {
            array_pop($parts);
        }

        return implode('.', $parts);
    }

    /**
     * Get the fully qualified path to the view.
     */
    public function findAppView(string $name, string $slug): ?string
    {
        $key = $slug.'.'.strtoupper($name);
        if (isset($this->appViews[$key])) {
            return $this->appViews[$key];
        }

        return null;
    }

    /**
     * Check if a view exists for the given slug.
     */
    public function hasAppView(string $name, string $slug): bool
    {
        return $this->findAppView($name, $slug) !== null;
    }

    /**
     * Get all views available for an app slug.
     *
     * @return array<string, string>
     */
    public function getAppViewList(string $slug): array
    {
        return array_filter(
            $this->appViews,
            fn (string $key) => str_starts_with($key, $slug.'.'),
            ARRAY_FILTER_USE_KEY
        );
    }
}
