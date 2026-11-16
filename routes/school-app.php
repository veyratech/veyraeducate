<?php

use App\Http\Controllers\AppRequestController;
use App\Http\Controllers\SchoolAppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute aplikasi sekolah — multi-app
|--------------------------------------------------------------------------
*/

// Prioritas rute spesifik harus di atas wildcard agar '/requests' tidak tertangkap slug
Route::middleware(['auth', 'verified', 'throttle:global'])->prefix('apps/sekolah')->name('school-app.')->group(function () {
    Route::get('/requests', [AppRequestController::class, 'index'])
        ->name('requests.index');
    Route::post('/requests', [AppRequestController::class, 'store'])
        ->name('requests.store');
    Route::delete('/requests/{appRequest}', [AppRequestController::class, 'destroy'])
        ->name('requests.destroy');
});

Route::middleware(['auth', 'verified', 'subscription.active', 'throttle:school-app'])->prefix('apps/sekolah')->name('school-app.')->group(function () {
    // Rute aset harus didaftarkan sebelum wildcard '/{slug}/{path?}' agar tidak tertimpa
    Route::get('/{slug}/asset/{path}', [SchoolAppController::class, 'asset'])
        ->where('slug', '[a-z0-9\-_]+')
        ->where('path', '[a-zA-Z0-9\-_./]+')
        ->name('app.asset');

    Route::get('/{slug}/{path?}', SchoolAppController::class)
        ->middleware('track.app.access')
        ->where('slug', '[a-z0-9\-_]+')
        ->where('path', '[a-zA-Z0-9\-_/\.]+')
        ->name('app');
});
