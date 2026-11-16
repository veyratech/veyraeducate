<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Models\WebApp;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeploymentController extends Controller
{
    public function index(Request $request): View
    {
        $selectedApp = trim((string) $request->query('app', ''));
        $selectedStatus = trim((string) $request->query('status', ''));

        $query = Deployment::query()
            ->with(['webApp', 'creator'])
            ->latest('id');

        if ($selectedApp !== '') {
            if (is_numeric($selectedApp)) {
                $query->where('web_app_id', (int) $selectedApp);
            } else {
                $query->where('slug', $selectedApp);
            }
        }

        if (in_array($selectedStatus, ['pending', 'deploying', 'success', 'failed'], true)) {
            $query->where('status', $selectedStatus);
        }

        $deployments = $query->limit(50)->get();
        $apps = WebApp::query()->orderBy('name')->get();

        return view('admin.deployments.index', compact('deployments', 'apps', 'selectedApp', 'selectedStatus'));
    }

    public function show(string $slug): View
    {
        $webApp = WebApp::query()->where('slug', $slug)->first();
        if (! $webApp) {
            abort(404, 'Aplikasi tidak ditemukan.');
        }

        $deployments = Deployment::query()
            ->where('web_app_id', $webApp->id)
            ->with('creator')
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.deployments.show', compact('webApp', 'deployments'));
    }
}
