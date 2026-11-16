<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $announcements = Announcement::published()
            ->ordered()
            ->where(function ($query) use ($school) {
                $query->whereNull('target_roles')
                    ->orWhereJsonContains('target_roles', $school->owner?->role ?? 'school');
            })
            ->get();

        return response()->json(AnnouncementResource::collection($announcements));
    }
}
