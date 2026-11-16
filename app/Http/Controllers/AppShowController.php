<?php

namespace App\Http\Controllers;

use App\Models\AppGuide;
use App\Models\WebApp;
use App\Services\SchoolAppInstaller;
use Illuminate\View\View;

class AppShowController extends Controller
{
    public function __construct(private readonly SchoolAppInstaller $installer) {}

    public function __invoke(string $slug): View
    {
        $webApp = WebApp::query()->with('plans')->where('slug', $slug)->first();

        if (! $webApp) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $school = auth()->user()?->school;
        $onDisk = is_dir($this->installer->appDir($slug));
        $deployed = $onDisk || $webApp->access_url !== null;

        $guides = AppGuide::query()
            ->where('web_app_id', $webApp->id)
            ->where(function ($q) use ($school) {
                $q->whereNull('school_id');
                if ($school) {
                    $q->orWhere('school_id', $school->id);
                }
            })
            ->latest()
            ->get();

        return view('apps.show', compact('webApp', 'onDisk', 'deployed', 'guides'));
    }
}
