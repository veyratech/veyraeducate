<?php

namespace App\Http\Controllers;

use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppsController extends Controller
{
    public function __construct(private readonly SchoolAppInstaller $installer) {}

    public function __invoke(Request $request): View
    {
        $school = auth()->user()?->school;
        $search = trim((string) $request->query('q', ''));
        $selectedCategory = trim((string) $request->query('category', ''));

        $allDeployedApps = WebApp::enabled()
            ->with('plans')
            ->withCount([
                'guides' => fn ($q) => $q->where(function ($sub) use ($school) {
                    $sub->whereNull('school_id');
                    if ($school) {
                        $sub->orWhere('school_id', $school->id);
                    }
                }),
            ])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (WebApp $app): bool => $this->isDeployed($app))
            ->values();

        $categories = $allDeployedApps
            ->map(fn (WebApp $app) => $app->category ?: 'Umum')
            ->unique()
            ->sort()
            ->values();

        $availableApps = $allDeployedApps
            ->when($selectedCategory !== '', function ($collection) use ($selectedCategory) {
                return $collection->filter(
                    fn (WebApp $app): bool => strcasecmp($app->category ?: 'Umum', $selectedCategory) === 0
                );
            })
            ->when($search !== '', function ($collection) use ($search) {
                $needle = mb_strtolower($search);

                return $collection->filter(function (WebApp $app) use ($needle): bool {
                    return str_contains(mb_strtolower((string) $app->name), $needle)
                        || str_contains(mb_strtolower((string) $app->description), $needle)
                        || str_contains(mb_strtolower((string) $app->category), $needle)
                        || str_contains(mb_strtolower((string) $app->slug), $needle);
                });
            })
            ->values();

        $installedSlugs = $school ? $school->webApps->pluck('slug')->toArray() : [];

        return view('apps.index', compact(
            'availableApps',
            'installedSlugs',
            'school',
            'categories',
            'search',
            'selectedCategory',
        ));
    }

    /**
     * An app is deployed when its files live on the server or it points at an
     * external access URL. Draft records never reach the App Hub.
     */
    private function isDeployed(WebApp $app): bool
    {
        return $app->access_url !== null || $this->installer->getAppPath($app->slug, $app) !== null;
    }
}
