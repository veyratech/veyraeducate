<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Database\Seeders\HomepageSectionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    /**
     * Display a listing of homepage sections.
     */
    public function index(): View
    {
        $sections = HomepageSection::query()->ordered()->get();

        return view('admin.homepage.index', compact('sections'));
    }

    /**
     * Show the form for creating a new section.
     */
    public function create(): View
    {
        $nextOrder = (HomepageSection::query()->max('order_position') ?? 0) + 1;

        return view('admin.homepage.create', compact('nextOrder'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'section_type' => ['required', 'string', 'in:custom_banner,custom_content,features,faq,cta,trust_strip,testimonials,app_showcase,workflow,ecosystem'],
            'order_position' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $content = $this->extractContentFromRequest($request, $validated['section_type']);

        $nextOrder = $validated['order_position'] ?? ((HomepageSection::query()->max('order_position') ?? 0) + 1);

        HomepageSection::query()->create([
            'key' => 'custom_'.time(),
            'title' => $validated['title'],
            'section_type' => $validated['section_type'],
            'order_position' => $nextOrder,
            'is_active' => $request->boolean('is_active', true),
            'content' => $content,
        ]);

        return redirect()->route('admin.homepage.index')
            ->with('status', 'Container baru berhasil ditambahkan ke homepage!');
    }

    /**
     * Show the form for editing the specified section.
     */
    public function edit(HomepageSection $section): View
    {
        return view('admin.homepage.edit', compact('section'));
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, HomepageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'order_position' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $content = $this->extractContentFromRequest($request, $section->section_type, $section->content ?? []);

        $section->update([
            'title' => $validated['title'],
            'order_position' => (int) $validated['order_position'],
            'is_active' => $request->boolean('is_active'),
            'content' => $content,
        ]);

        return redirect()->route('admin.homepage.index')
            ->with('status', "Container '{$section->title}' berhasil diperbarui!");
    }

    /**
     * Remove the specified section from storage.
     */
    public function destroy(HomepageSection $section): RedirectResponse
    {
        $title = $section->title;
        $section->delete();

        return redirect()->route('admin.homepage.index')
            ->with('status', "Container '{$title}' berhasil dihapus dari homepage.");
    }

    /**
     * Toggle active status of a section.
     */
    public function toggle(HomepageSection $section): RedirectResponse
    {
        $section->update([
            'is_active' => ! $section->is_active,
        ]);

        $statusText = $section->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.homepage.index')
            ->with('status', "Container '{$section->title}' berhasil {$statusText}.");
    }

    /**
     * Move section position up or down.
     */
    public function move(Request $request, HomepageSection $section): RedirectResponse
    {
        $direction = $request->input('direction', 'up');

        if ($direction === 'up') {
            $neighbor = HomepageSection::query()
                ->where('order_position', '<', $section->order_position)
                ->orderByDesc('order_position')
                ->first();
        } else {
            $neighbor = HomepageSection::query()
                ->where('order_position', '>', $section->order_position)
                ->orderBy('order_position')
                ->first();
        }

        if ($neighbor) {
            $currentPos = $section->order_position;
            $neighborPos = $neighbor->order_position;

            // If same positions somehow, enforce difference
            if ($currentPos === $neighborPos) {
                $neighborPos = $direction === 'up' ? $currentPos - 1 : $currentPos + 1;
            }

            $section->update(['order_position' => $neighborPos]);
            $neighbor->update(['order_position' => $currentPos]);
        }

        return redirect()->route('admin.homepage.index')
            ->with('status', "Urutan container '{$section->title}' berhasil dipindahkan.");
    }

    /**
     * Reset sections to default seed data.
     */
    public function resetToDefault(): RedirectResponse
    {
        (new HomepageSectionSeeder)->run();

        return redirect()->route('admin.homepage.index')
            ->with('status', 'Susunan container homepage berhasil dipulihkan ke pengaturan default!');
    }

    /**
     * Extract and parse content fields based on section type.
     *
     * @param  array<string, mixed>  $existing
     * @return array<string, mixed>
     */
    private function extractContentFromRequest(Request $request, string $sectionType, array $existing = []): array
    {
        $content = $existing;

        // Handle section-level image/illustration upload safely without requiring ext-fileinfo
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $destination = storage_path('app/public/homepage');
                if (! is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                $filename = $file->hashName();
                $file->move($destination, $filename);
                $content['image_url'] = '/storage/homepage/'.$filename;
            }
        } elseif ($request->filled('image_url_manual')) {
            $content['image_url'] = $request->input('image_url_manual');
        }

        if ($request->boolean('remove_image')) {
            unset($content['image_url']);
        }

        if ($request->has('image_position')) {
            $content['image_position'] = $request->input('image_position', 'right');
        }

        // Handle Background customization (applicable to any section)
        if ($request->has('bg_type')) {
            $content['bg_type'] = $request->input('bg_type', 'default');
        }
        if ($request->has('bg_color')) {
            $content['bg_color'] = $request->input('bg_color', '#0f172a');
        }
        if ($request->has('bg_overlay')) {
            $content['bg_overlay'] = $request->input('bg_overlay', 'dark-60');
        }
        if ($request->has('text_mode')) {
            $content['text_mode'] = $request->input('text_mode', 'auto');
        }

        // Handle custom background image upload safely without ext-fileinfo
        if ($request->hasFile('bg_image_file')) {
            $bgFile = $request->file('bg_image_file');
            if ($bgFile->isValid()) {
                $bgDestination = storage_path('app/public/homepage/bg');
                if (! is_dir($bgDestination)) {
                    mkdir($bgDestination, 0755, true);
                }
                $bgFilename = $bgFile->hashName();
                $bgFile->move($bgDestination, $bgFilename);
                $content['bg_image_url'] = '/storage/homepage/bg/'.$bgFilename;
            }
        } elseif ($request->filled('bg_image_url_manual')) {
            $content['bg_image_url'] = $request->input('bg_image_url_manual');
        }

        if ($request->boolean('remove_bg_image')) {
            unset($content['bg_image_url']);
            if (($content['bg_type'] ?? '') === 'custom_image') {
                $content['bg_type'] = 'default';
            }
        }

        switch ($sectionType) {
            case 'hero':
                $content['lead'] = $request->input('lead', $content['lead'] ?? '');
                $content['highlight'] = $request->input('highlight', $content['highlight'] ?? '');
                $content['tail'] = $request->input('tail', $content['tail'] ?? '');
                $content['description'] = $request->input('description', $content['description'] ?? '');
                $content['primary_btn_text'] = $request->input('primary_btn_text', $content['primary_btn_text'] ?? 'Daftar sekolah');
                $content['primary_btn_url'] = $request->input('primary_btn_url', $content['primary_btn_url'] ?? '/register');
                $content['secondary_btn_text'] = $request->input('secondary_btn_text', $content['secondary_btn_text'] ?? 'Lihat demo');
                $content['secondary_btn_url'] = $request->input('secondary_btn_url', $content['secondary_btn_url'] ?? '/demo');
                $content['show_banner'] = $request->boolean('show_banner', $content['show_banner'] ?? true);
                break;

            case 'custom_banner':
                $content['badge'] = $request->input('badge', $content['badge'] ?? '');
                $content['title'] = $request->input('title_text') ?: $request->input('banner_title', $request->input('title', $content['title'] ?? ''));
                $content['subtitle'] = $request->input('subtitle', $content['subtitle'] ?? '');
                $content['theme'] = $request->input('theme', $content['theme'] ?? 'brand'); // brand, dark, emerald, gold
                $content['btn_text'] = $request->input('btn_text', $content['btn_text'] ?? '');
                $content['btn_url'] = $request->input('btn_url', $content['btn_url'] ?? '');
                break;

            case 'custom_content':
                $content['badge'] = $request->input('badge', $content['badge'] ?? '');
                $content['title'] = $request->input('title_text') ?: $request->input('heading', $request->input('title', $content['title'] ?? ''));
                $content['subtitle'] = $request->input('subtitle', $content['subtitle'] ?? '');
                $content['body_html'] = $request->input('body_html', $content['body_html'] ?? '');
                $content['btn_text'] = $request->input('btn_text', $content['btn_text'] ?? '');
                $content['btn_url'] = $request->input('btn_url', $content['btn_url'] ?? '');
                $content['theme'] = $request->input('theme', $content['theme'] ?? 'light');
                break;

            case 'features':
                $content['badge'] = $request->input('badge', 'Fitur');
                $content['title'] = $request->input('title', 'Fitur Unggulan');
                $content['subtitle'] = $request->input('subtitle', '');
                if ($request->has('items') && is_array($request->input('items'))) {
                    $items = array_values(array_filter($request->input('items'), fn ($item) => ! empty($item['title'])));
                    if ($request->hasFile('item_images')) {
                        $itemDest = storage_path('app/public/homepage/items');
                        if (! is_dir($itemDest)) {
                            mkdir($itemDest, 0755, true);
                        }
                        foreach ($request->file('item_images') as $iIdx => $itemFile) {
                            if ($itemFile && $itemFile->isValid() && isset($items[$iIdx])) {
                                $iFilename = $itemFile->hashName();
                                $itemFile->move($itemDest, $iFilename);
                                $items[$iIdx]['image_url'] = '/storage/homepage/items/'.$iFilename;
                            }
                        }
                    }
                    $content['items'] = $items;
                }
                break;

            case 'workflow':
                $content['badge'] = $request->input('badge', 'Cara kerja');
                $content['title'] = $request->input('title', '');
                $content['subtitle'] = $request->input('subtitle', '');
                if ($request->has('steps') && is_array($request->input('steps'))) {
                    $steps = array_values(array_filter($request->input('steps'), fn ($step) => ! empty($step['title'])));
                    if ($request->hasFile('step_images')) {
                        $stepDest = storage_path('app/public/homepage/steps');
                        if (! is_dir($stepDest)) {
                            mkdir($stepDest, 0755, true);
                        }
                        foreach ($request->file('step_images') as $sIdx => $stepFile) {
                            if ($stepFile && $stepFile->isValid() && isset($steps[$sIdx])) {
                                $sFilename = $stepFile->hashName();
                                $stepFile->move($stepDest, $sFilename);
                                $steps[$sIdx]['image_url'] = '/storage/homepage/steps/'.$sFilename;
                            }
                        }
                    }
                    $content['steps'] = $steps;
                }
                break;

            case 'ecosystem':
                $content['badge'] = $request->input('badge', 'Ekosistem');
                $content['title'] = $request->input('title', '');
                $content['subtitle'] = $request->input('subtitle', '');
                if ($request->has('items') && is_array($request->input('items'))) {
                    $items = array_values(array_filter($request->input('items'), fn ($item) => ! empty($item['title'])));
                    if ($request->hasFile('item_images')) {
                        $itemDest = storage_path('app/public/homepage/items');
                        if (! is_dir($itemDest)) {
                            mkdir($itemDest, 0755, true);
                        }
                        foreach ($request->file('item_images') as $iIdx => $itemFile) {
                            if ($itemFile && $itemFile->isValid() && isset($items[$iIdx])) {
                                $iFilename = $itemFile->hashName();
                                $itemFile->move($itemDest, $iFilename);
                                $items[$iIdx]['image_url'] = '/storage/homepage/items/'.$iFilename;
                            }
                        }
                    }
                    $content['items'] = $items;
                }
                break;

            case 'pricing':
                $content['badge'] = $request->input('badge', 'Harga');
                $content['title'] = $request->input('title', 'Paket langganan transparan');
                $content['subtitle'] = $request->input('subtitle', '');
                break;

            case 'faq':
                $content['badge'] = $request->input('badge', 'FAQ');
                $content['title'] = $request->input('title', 'Pertanyaan yang sering diajukan');
                $content['subtitle'] = $request->input('subtitle', '');
                if ($request->has('items') && is_array($request->input('items'))) {
                    $content['items'] = array_values(array_filter($request->input('items'), fn ($item) => ! empty($item['q'])));
                }
                break;

            case 'cta':
                $content['badge'] = $request->input('badge', 'Siap mulai');
                $content['title'] = $request->input('title', '');
                $content['subtitle'] = $request->input('subtitle', '');
                $content['btn_text'] = $request->input('btn_text', 'Daftar sekolah');
                $content['btn_url'] = $request->input('btn_url', '/register');
                $content['wa_text'] = $request->input('wa_text', 'Konsultasi WhatsApp');
                $content['wa_url'] = $request->input('wa_url', 'https://wa.me/6285707074778');
                break;

            case 'trust_strip':
                if ($request->has('items') && is_array($request->input('items'))) {
                    $items = array_values(array_filter($request->input('items'), fn ($item) => ! empty($item['title'])));
                    if ($request->hasFile('item_images')) {
                        $itemDest = storage_path('app/public/homepage/items');
                        if (! is_dir($itemDest)) {
                            mkdir($itemDest, 0755, true);
                        }
                        foreach ($request->file('item_images') as $iIdx => $itemFile) {
                            if ($itemFile && $itemFile->isValid() && isset($items[$iIdx])) {
                                $iFilename = $itemFile->hashName();
                                $itemFile->move($itemDest, $iFilename);
                                $items[$iIdx]['image_url'] = '/storage/homepage/items/'.$iFilename;
                            }
                        }
                    }
                    $content['items'] = $items;
                }
                break;

            case 'testimonials':
                $content['badge'] = $request->input('badge', 'Testimoni');
                $content['title'] = $request->input('title', 'Dipercaya oleh puluhan sekolah & yayasan di Indonesia');
                $content['subtitle'] = $request->input('subtitle', '');
                $rawTestimonials = $request->input('testimonials', $request->input('items', []));
                if (is_array($rawTestimonials)) {
                    $testimonials = array_values(array_filter($rawTestimonials, fn ($item) => ! empty($item['quote']) || ! empty($item['name'])));
                    if ($request->hasFile('testimonial_avatars')) {
                        $avatarDest = storage_path('app/public/homepage/testimonials');
                        if (! is_dir($avatarDest)) {
                            mkdir($avatarDest, 0755, true);
                        }
                        foreach ($request->file('testimonial_avatars') as $tIdx => $avatarFile) {
                            if ($avatarFile && $avatarFile->isValid() && isset($testimonials[$tIdx])) {
                                $aFilename = $avatarFile->hashName();
                                $avatarFile->move($avatarDest, $aFilename);
                                $testimonials[$tIdx]['avatar_url'] = '/storage/homepage/testimonials/'.$aFilename;
                            }
                        }
                    }
                    $content['items'] = $testimonials;
                    $content['testimonials'] = $testimonials;
                }
                break;

            case 'app_showcase':
                $content['badge'] = $request->input('badge', 'Katalog Aplikasi');
                $content['title'] = $request->input('title', 'Ragam Aplikasi Siap Tanam untuk Sekolah Anda');
                $content['subtitle'] = $request->input('subtitle', '');
                $content['source_mode'] = $request->input('source_mode', 'custom');
                if ($request->has('items') && is_array($request->input('items'))) {
                    $items = array_values(array_filter($request->input('items'), fn ($item) => ! empty($item['name']) || ! empty($item['title'])));
                    // Normalize 'title' to 'name' if provided
                    foreach ($items as &$it) {
                        if (empty($it['name']) && ! empty($it['title'])) {
                            $it['name'] = $it['title'];
                        }
                    }
                    unset($it);

                    if ($request->hasFile('item_images')) {
                        $appDest = storage_path('app/public/homepage/apps');
                        if (! is_dir($appDest)) {
                            mkdir($appDest, 0755, true);
                        }
                        foreach ($request->file('item_images') as $iIdx => $appFile) {
                            if ($appFile && $appFile->isValid() && isset($items[$iIdx])) {
                                $aFilename = $appFile->hashName();
                                $appFile->move($appDest, $aFilename);
                                $items[$iIdx]['image_url'] = '/storage/homepage/apps/'.$aFilename;
                            }
                        }
                    }
                    $content['items'] = $items;
                }
                break;

            default:
                // Merge any additional simple inputs
                foreach ($request->except(['_token', '_method', 'title', 'order_position', 'is_active', 'section_type', 'image']) as $key => $val) {
                    if (is_scalar($val)) {
                        $content[$key] = $val;
                    }
                }
                break;
        }

        return $content;
    }
}
