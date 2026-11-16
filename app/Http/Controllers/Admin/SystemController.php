<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function diagnose(Request $request): JsonResponse
    {
        $output = Artisan::call('veyra:diagnose');

        return response()->json([
            'output' => Artisan::output(),
            'status' => Artisan::exitCode(),
        ]);
    }

    public function runBackup(Request $request): JsonResponse
    {
        $keepDays = (int) $request->input('days', 30);

        Artisan::call('backup:run', ['--days' => $keepDays]);

        return response()->json([
            'output' => Artisan::output(),
            'status' => Artisan::exitCode(),
        ]);
    }

    public function index(): View
    {
        return view('admin.system.diagnose');
    }
}
