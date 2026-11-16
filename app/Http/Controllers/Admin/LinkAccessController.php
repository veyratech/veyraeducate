<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkAccessController extends Controller
{
    public function __construct(private readonly SchoolAppInstaller $installer) {}

    public function index(): View
    {
        $apps = $this->installer->listApps();
        $categories = WebApp::categoryOptions();

        return view('admin.link-access.index', compact('apps', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9\-_]+$/', 'unique:web_apps,slug'],
            'access_url' => ['nullable', 'string', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['nullable', 'string', 'in:akademik,keuangan,perpustakaan,kesiswaan,kepegawaian,ekskul,komunikasi,umum,lainnya'],
            'icon' => ['nullable', 'string', 'max:64'],
        ], [
            'slug.unique' => 'Slug ini sudah digunakan. Gunakan slug lain yang unik.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, tanda hubung (-), dan garis bawah (_).',
        ]);

        $webApp = WebApp::query()->create([
            'slug' => $validated['slug'],
            'name' => $validated['name'],
            'access_url' => $validated['access_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? 'umum',
            'icon' => $validated['icon'] ?? 'app',
        ]);

        AuditLog::record('link_access_created', $webApp, [
            'slug' => $webApp->slug,
            'name' => $webApp->name,
            'access_url' => $webApp->access_url,
        ]);

        return redirect()
            ->route('admin.link-access.index')
            ->with('status', 'Link akses "'.$validated['name'].'" berhasil ditambahkan.');
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $webApp = WebApp::query()->where('slug', $slug)->first();
        $onDisk = is_dir($this->installer->appDir($slug));

        if ($webApp === null && ! $onDisk) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $validated = $request->validate([
            'access_url' => ['nullable', 'string', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $accessUrl = $validated['access_url'] ?? null;

        if ($accessUrl === null && ! $onDisk) {
            return back()->withErrors([
                'access_url' => 'Link tujuan wajib diisi karena aplikasi ini tidak ada di server.',
            ]);
        }

        $webApp ??= WebApp::query()->create([
            'slug' => $slug,
            'name' => $slug,
            'category' => 'umum',
            'icon' => 'app',
        ]);

        $payload = ['access_url' => $accessUrl];
        if ($request->has('description')) {
            $payload['description'] = $validated['description'] ?: null;
        }

        $webApp->update($payload);

        AuditLog::record('link_access_updated', $webApp, [
            'slug' => $webApp->slug,
            'access_url' => $accessUrl,
        ]);

        return redirect()
            ->route('admin.link-access.index')
            ->with('status', 'URL akses "'.$webApp->name.'" diperbarui.');
    }
}
