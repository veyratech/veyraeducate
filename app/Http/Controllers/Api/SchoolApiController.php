<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolResource;
use Illuminate\Http\Request;

class SchoolApiController extends Controller
{
    public function show(Request $request): SchoolResource
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        return new SchoolResource($school->loadMissing('owner'));
    }
}
