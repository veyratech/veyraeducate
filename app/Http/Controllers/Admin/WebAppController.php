<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\Plan;
use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class WebAppController extends Controller
{
    public function __construct(private readonly SchoolAppInstaller $installer) {}

    public function index(): View
    {
        $apps = $this->installer->listApps();
        $categories = WebApp::categoryOptions();
        $icons = WebApp::iconOptions();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.web-apps.index', compact('apps', 'categories', 'icons', 'plans'));
    }

    public function show(string $slug): View
    {
        $webApp = WebApp::query()->where('slug', $slug)->with('plans')->first();
        if (! $webApp) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $onDisk = is_dir($this->installer->appDir($slug));
        $meta = $onDisk ? $this->installer->readMeta($slug) : [];
        $installedAt = $meta['installed_at'] ?? null;
        $deployed = $onDisk || $webApp->access_url !== null;

        $deployments = Deployment::query()
            ->where('web_app_id', $webApp->id)
            ->with('creator')
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.web-apps.show', compact('webApp', 'onDisk', 'meta', 'installedAt', 'deployments', 'deployed'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9\-_]+$/', 'unique:web_apps,slug'],
            'access_url' => ['nullable', 'string', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'in:akademik,keuangan,perpustakaan,kesiswaan,kepegawaian,ekskul,komunikasi,umum,lainnya'],
            'icon' => ['nullable', 'string', 'max:64'],
            'plan_ids' => ['nullable', 'array'],
            'plan_ids.*' => ['integer', 'exists:plans,id'],
        ]);

        $webApp = WebApp::query()->create([
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'access_url' => $validated['access_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? 'umum',
            'icon' => $validated['icon'] ?? 'app',
        ]);

        if ($request->has('plan_ids')) {
            $webApp->plans()->sync($request->input('plan_ids', []));
        }

        return redirect()
            ->route('admin.web-apps.index')
            ->with('status', 'Aplikasi "'.$validated['name'].'" berhasil ditambahkan.');
    }

    public function upload(Request $request): RedirectResponse
    {
        if (!$request->hasFile('app_zip')) {
            return back()->withErrors(['app_zip' => 'File ZIP aplikasi wajib diunggah.']);
        }
        $zipFile = $request->file('app_zip');
        $zipSizeMB = $zipFile->getSize() / 1024 / 1024;
        if ($zipSizeMB > 64) {
            return back()->withErrors(['app_zip' => 'Ukuran file maksimal 64MB.']);
        }
        $zipExt = strtolower(pathinfo($zipFile->getClientOriginalName(), PATHINFO_EXTENSION));
        if ($zipExt !== 'zip') {
            return back()->withErrors(['app_zip' => 'File harus berupa ZIP.']);
        }
        if (!$zipFile->isValid()) {
            return back()->withErrors(['app_zip' => 'Gagal memproses file ZIP. Coba gunakan file yang lebih kecil.']);
        }

        $slug = trim((string) $request->input('slug', ''));
        if (empty($slug) || !preg_match('/^[a-z0-9\-_]+$/', $slug)) {
            return back()->withErrors(['slug' => 'Slug harus berisi huruf kecil, angka, strip, dan underscore saja.']);
        }

        try {
            $meta = $this->installer->install($zipFile, $slug);
        } catch (RuntimeException $e) {
            return back()->withErrors(['app_zip' => $e->getMessage()]);
        }

        $webApp = WebApp::query()->where('slug', $slug)->firstOrFail();
        $schemaSummary = $this->schemaSummary($meta['schema']);

        $skippedSummary = ($meta['skipped'] ?? []) !== []
            ? ' Melewati file terlarang: '.SchoolAppInstaller::summarizeList($meta['skipped']).'.'
            : '';

        $dirSummary = '';
        foreach (($meta['skipped_dirs'] ?? []) as $dir => $count) {
            $dirSummary .= " Melewati folder {$dir} ({$count} file).";
        }

        Deployment::query()->create([
            'web_app_id' => $webApp->id,
            'slug' => $slug,
            'version' => $meta['version'],
            'status' => 'success',
            'source_file' => $meta['source'],
            'file_count' => $meta['files'],
            'size_bytes' => $meta['bytes'],
            'notes' => mb_substr($schemaSummary.$skippedSummary.$dirSummary, 0, 6000),
            'created_by' => $request->user()?->getAuthIdentifier(),
            'deployed_at' => now(),
        ]);

        $status = 'Aplikasi "'.$webApp->name.'" terpasang — '.$meta['files'].' file, '.$schemaSummary.'.'.$skippedSummary.$dirSummary;

        return redirect()
            ->route('admin.web-apps.index')
            ->with('status', $status);
    }

    /**
     * @param  array{migrations: int, statements: int, warnings: array<int, string>}  $schema
     */
    private function schemaSummary(array $schema): string
    {
        $parts = [];

        if ($schema['migrations'] > 0) {
            $parts[] = $schema['migrations'].' file migrasi';
        }

        if ($schema['statements'] > 0) {
            $parts[] = $schema['statements'].' statement SQL';
        }

        $summary = $parts === [] ? 'tanpa perubahan skema' : 'skema: '.implode(', ', $parts);

        if ($schema['warnings'] !== []) {
            $summary .= ' — peringatan: '.implode(' | ', $schema['warnings']);
        }

        return $summary;
    }

    public function toggle(string $slug): RedirectResponse
    {
        $webApp = WebApp::query()->where('slug', $slug)->first();

        if ($webApp === null && $this->installer->getAppPath($slug) === null) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $webApp ??= WebApp::query()->create([
            'slug' => $slug,
            'name' => $slug,
            'category' => 'umum',
            'icon' => 'app',
        ]);

        $enabled = ! $webApp->enabled;
        $webApp->update(['enabled' => $enabled]);

        return redirect()
            ->route('admin.web-apps.index')
            ->with('status', "Aplikasi \"{$webApp->name}\" ".($enabled ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    public function edit(string $slug): View
    {
        $webApp = WebApp::query()->where('slug', $slug)->with('plans')->first();
        if (! $webApp) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $categories = WebApp::categoryOptions();
        $icons = WebApp::iconOptions();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.web-apps.edit', compact('webApp', 'categories', 'icons', 'plans'));
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $webApp = WebApp::query()->where('slug', $slug)->first();
        if (! $webApp) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9\-_]+$/', 'unique:web_apps,slug,'.$webApp->id],
            'access_url' => ['nullable', 'string', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'in:akademik,keuangan,perpustakaan,kesiswaan,kepegawaian,ekskul,komunikasi,umum,lainnya'],
            'icon' => ['nullable', 'string', 'max:64'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'plan_ids' => ['nullable', 'array'],
            'plan_ids.*' => ['integer', 'exists:plans,id'],
        ]);

        $webApp->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'access_url' => $validated['access_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? 'umum',
            'icon' => $validated['icon'] ?? 'app',
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $webApp->plans()->sync($request->input('plan_ids', []));

        return redirect()
            ->route('admin.web-apps.show', $slug)
            ->with('status', 'Aplikasi "'.$webApp->name.'" berhasil diperbarui.');
    }

    public function uninstall(string $slug): RedirectResponse
    {
        $appName = WebApp::query()->where('slug', $slug)->value('name') ?? $slug;

        $this->installer->uninstall($slug);

        return redirect()
            ->route('admin.web-apps.index')
            ->with('status', 'Aplikasi "'.$appName.'" berhasil dihapus.');
    }
}
