<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebAppResource;
use App\Models\WebApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $apps = WebApp::enabled()
            ->with('plans')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn (WebApp $app) => $school->hasAccessToApp($app->slug))
            ->values();

        return response()->json(WebAppResource::collection($apps));
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $app = WebApp::enabled()->where('slug', $slug)->first();

        if (! $app) {
            return response()->json(['message' => 'App not found'], 404);
        }

        if (! $school->hasAccessToApp($slug)) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json(new WebAppResource($app->loadMissing('plans')));
    }
}
