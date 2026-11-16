<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionApiController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $subscription = $school->activeSubscription()->first()
            ?? $school->subscriptions()->with('plan')->latest('id')->first();

        if (! $subscription) {
            return response()->json(['message' => 'No subscription found'], 404);
        }

        return response()->json(new SubscriptionResource($subscription->loadMissing('plan')));
    }
}
