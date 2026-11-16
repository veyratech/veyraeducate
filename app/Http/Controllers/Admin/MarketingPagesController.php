<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingPagesController extends Controller
{
    public function index(): View
    {
        $pages = MarketingPage::query()->orderBy('slug')->get();

        return view('admin.marketing-pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.marketing-pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9\-]+$/'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'hero_image' => ['nullable', 'url'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        MarketingPage::create([
            'slug' => $validated['slug'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'eyebrow' => $validated['eyebrow'] ?? '',
            'hero_image' => $validated['hero_image'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.marketing-pages.index')
            ->with('status', 'Halaman marketing berhasil dibuat.');
    }

    public function edit(MarketingPage $marketingPage): View
    {
        return view('admin.marketing-pages.edit', compact('marketingPage'));
    }

    public function update(Request $request, MarketingPage $marketingPage): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'hero_image' => ['nullable', 'url'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $marketingPage->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'eyebrow' => $validated['eyebrow'] ?? '',
            'hero_image' => $validated['hero_image'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.marketing-pages.index')
            ->with('status', "Halaman '{$marketingPage->slug}' berhasil diperbarui.");
    }

    public function destroy(MarketingPage $marketingPage): RedirectResponse
    {
        $title = $marketingPage->slug;
        $marketingPage->delete();

        return redirect()
            ->route('admin.marketing-pages.index')
            ->with('status', "Halaman '{$title}' berhasil dihapus.");
    }
}
