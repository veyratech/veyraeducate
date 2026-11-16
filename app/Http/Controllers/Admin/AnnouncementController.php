<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $query = Announcement::query()->latest('published_at')->latest('id');

        if ($search !== '') {
            $escaped = str_replace(['%', '_'], '\\\\$0', $search);
            $query->where(function ($q) use ($escaped) {
                $q->where('title', 'like', "%{$escaped}%")
                    ->orWhere('body', 'like', "%{$escaped}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true)->where('published_at', '<=', now());
        } elseif ($status === 'scheduled') {
            $query->where('is_active', true)->where('published_at', '>', now());
        } elseif ($status === 'draft') {
            $query->where('is_active', false);
        }

        return view('admin.announcements.index', [
            'announcements' => $query->get(),
            'search' => $search,
            'status' => $status,
            'counts' => [
                'all' => Announcement::query()->count(),
                'active' => Announcement::query()->where('is_active', true)->where('published_at', '<=', now())->count(),
                'scheduled' => Announcement::query()->where('is_active', true)->where('published_at', '>', now())->count(),
                'draft' => Announcement::query()->where('is_active', false)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Announcement::create($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Pengumuman berhasil dibuat.');
    }

    public function broadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $publishedAt = ! empty($validated['published_at'])
            ? Carbon::parse($validated['published_at'])
            : now();
        $validated['published_at'] = $publishedAt;

        Announcement::create($validated);

        $schoolUsers = School::query()
            ->with(['owner'])
            ->whereNotNull('owner_user_id')
            ->get();

        $rows = [];
        foreach ($schoolUsers as $school) {
            $owner = $school->owner;
            if (! $owner) {
                continue;
            }

            $rows[] = [
                'id' => (string) Str::uuid(),
                'type' => 'announcement',
                'notifiable_type' => \get_class($owner),
                'notifiable_id' => $owner->getKey(),
                'data' => json_encode([
                    'title' => $validated['title'],
                    'body' => $validated['body'],
                    'published_at' => $publishedAt->toIso8601String(),
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($rows)) {
            DB::table('notifications')->insert($rows);
        }

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Pengumuman berhasil disebar ke '.count($rows).' sekolah aktif dan notifikasi dikirim.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        if (empty($validated['published_at'])) {
            $validated['published_at'] = $announcement->published_at ?? now();
        }

        $announcement->update($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Pengumuman berhasil diperbarui.');
    }

    public function toggle(Announcement $announcement): RedirectResponse
    {
        $newState = ! $announcement->is_active;
        $announcement->update([
            'is_active' => $newState,
            'published_at' => $announcement->published_at ?? now(),
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Pengumuman "'.$announcement->title.'" '.($newState ? 'dipublikasikan.' : 'dinonaktifkan (Draft).'));
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Pengumuman berhasil dihapus.');
    }
}
