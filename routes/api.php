<?php

use App\Http\Controllers\Api\AnnouncementApiController;
use App\Http\Controllers\Api\AppApiController;
use App\Http\Controllers\Api\MidtransWebhookController;
use App\Http\Controllers\Api\SchoolApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/school', [SchoolApiController::class, 'show']);
    Route::get('/apps', [AppApiController::class, 'index']);
    Route::get('/apps/{slug}', [AppApiController::class, 'show']);
    Route::get('/subscription', [SubscriptionApiController::class, 'show']);
    Route::get('/announcements', [AnnouncementApiController::class, 'index']);
});

Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])
    ->withoutMiddleware([PreventRequestForgery::class]);
