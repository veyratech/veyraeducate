<?php

use App\Http\Controllers\AppGuideController;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\AppShowController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\MarketingPagesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolBroadcastController;
use App\Http\Controllers\SchoolCardController;
use App\Http\Controllers\SchoolReferralController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\SubdomainHealthController;
use App\Http\Controllers\WebhookController;
use App\Models\HomepageSection;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Custom storage file serving — served directly via readfile() to avoid finfo dependency
Route::get('storage/{path}', function (string $path) {
    $cleanPath = str_replace(['..', "\0"], '', $path);

    if ($cleanPath === '') {
        abort(404, 'Empty storage path');
    }

    $fullPath = storage_path('app/public/' . $cleanPath);

    if (!file_exists($fullPath)) {
        return response()->json(['error' => 'File not found', 'path' => $cleanPath], 404);
    }

    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($fullPath);
    exit;
})->where('path', '.*')->name('storage.fallback');

Route::middleware(['https.redirect', 'throttle:global'])->group(function () {
    Route::get('/', function () {
        $sections = HomepageSection::getActiveOrdered();

        return view('welcome', compact('sections'));
    })->name('home');

    Route::get('/health', function () {
        $checks = [];

        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'ok', 'message' => 'Connected'];
        } catch (Throwable $e) {
            $checks['database'] = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        try {
            Cache::get('_health_check');
            $checks['cache'] = ['status' => 'ok', 'message' => config('cache.default', 'file')];
        } catch (Throwable $e) {
            $checks['cache'] = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        $storagePublic = storage_path('app/public');
        $storageLink = public_path('storage');
        $checks['storage'] = [
            'status' => is_dir($storageLink) || is_link($storageLink) ? 'ok' : 'warn',
            'message' => is_link($storageLink) ? 'symlink' : (is_dir($storageLink) ? 'directory' : 'missing symlink'),
        ];

        try {
            $queueConnection = config('queue.default', 'sync');
            $queueConfigured = $queueConnection !== 'sync' || config('queue.connections.sync');
            $checks['queue'] = [
                'status' => 'ok',
                'message' => "driver={$queueConnection}",
            ];
        } catch (Throwable $e) {
            $checks['queue'] = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        $allOk = collect($checks)->every(fn ($c) => $c['status'] === 'ok');

        return response()->json([
            'status' => $allOk ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $allOk ? 200 : 503);
    })->name('health.check');

    Route::get('/demo', [DemoController::class, 'show'])->name('demo');
    Route::post('/demo/login', [DemoController::class, 'login'])->middleware('throttle:auth')->name('demo.login');

    foreach ([
        'fitur' => 'pages.fitur',
        'harga' => 'pages.harga',
        'tentang' => 'pages.tentang',
        'kontak' => 'pages.kontak',
        'faq' => 'pages.faq',
        'kabar' => 'pages.kabar',
        'kebijakan-privasi' => 'pages.kebijakan-privasi',
        'syarat-ketentuan' => 'pages.syarat-ketentuan',
    ] as $slug => $view) {
        Route::view($slug, $view)->name($slug);
    }

    Route::get('/brosur', fn () => app(\App\Http\Controllers\MarketingPagesController::class)->show('brosur'))->name('brosur');
    Route::get('/proposal', fn () => app(\App\Http\Controllers\MarketingPagesController::class)->show('proposal'))->name('proposal');

    Route::middleware('auth')->group(function () {
        Route::get('/checkout/{planCode}', [CheckoutController::class, 'show'])
            ->where('planCode', '[a-z0-9\-]+')
            ->name('checkout.show');

        Route::post('/checkout', [CheckoutController::class, 'store'])
            ->middleware('throttle:checkout')
            ->name('checkout.store');

        Route::get('/tagihan', [PaymentController::class, 'history'])
            ->middleware('verified')
            ->name('pembayaran.history');

        Route::get('/pembayaran/{orderId}', [PaymentController::class, 'show'])
            ->where('orderId', '[A-Za-z0-9\-]+')
            ->name('pembayaran.show');

        Route::get('/pembayaran/{orderId}/invoice', [PaymentController::class, 'invoice'])
            ->where('orderId', '[A-Za-z0-9\-]+')
            ->name('pembayaran.invoice');

        Route::get('/pembayaran/{orderId}/bast', [PaymentController::class, 'bast'])
            ->where('orderId', '[A-Za-z0-9\-]+')
            ->name('pembayaran.bast');

        Route::post('/pembayaran/{orderId}/bukti', [PaymentController::class, 'uploadProof'])
            ->where('orderId', '[A-Za-z0-9\-]+')
            ->name('pembayaran.upload-proof');

        Route::get('/dashboard', DashboardController::class)
            ->middleware('verified')
            ->name('dashboard');

        Route::get('/apps', AppsController::class)
            ->middleware('verified')
            ->name('apps.index');

        Route::get('/apps/{slug}', AppShowController::class)
            ->middleware('verified')
            ->where('slug', '[a-z0-9\-_]+')
            ->name('apps.show');

        Route::get('/panduan', [AppGuideController::class, 'index'])
            ->middleware('verified')
            ->name('guides.index');
        Route::post('/panduan', [AppGuideController::class, 'store'])
            ->middleware('verified')
            ->name('guides.store');
        Route::get('/panduan/{guide}/preview', [AppGuideController::class, 'preview'])
            ->middleware('verified')
            ->name('guides.preview');
        Route::get('/panduan/{guide}/download', [AppGuideController::class, 'download'])
            ->middleware('verified')
            ->name('guides.download');
        Route::delete('/panduan/{guide}', [AppGuideController::class, 'destroy'])
            ->middleware('verified')
            ->name('guides.destroy');

        Route::get('/pengaturan', [SchoolSettingController::class, 'index'])
            ->middleware('verified')
            ->name('school.settings');
        Route::patch('/pengaturan/school', [SchoolSettingController::class, 'updateSchool'])
            ->middleware('verified')
            ->name('school.settings.school');
        Route::post('/pengaturan/logo', [SchoolSettingController::class, 'updateLogo'])
            ->middleware('verified')
            ->name('school.settings.logo');
        Route::patch('/pengaturan/profile', [SchoolSettingController::class, 'updateProfile'])
            ->middleware('verified')
            ->name('school.settings.profile');
        Route::patch('/pengaturan/password', [SchoolSettingController::class, 'updatePassword'])
            ->middleware('verified')
            ->name('school.settings.password');
        Route::post('/pengaturan/tim', [SchoolSettingController::class, 'storeMember'])
            ->middleware('verified')
            ->name('school.settings.team.store');
        Route::delete('/pengaturan/tim/{member}', [SchoolSettingController::class, 'deleteMember'])
            ->middleware('verified')
            ->name('school.settings.team.destroy');
        Route::get('/pengaturan/template-dapodik/{type}', [SchoolSettingController::class, 'downloadTemplate'])
            ->middleware('verified')
            ->where('type', 'siswa|guru|soal|tatatertib')
            ->name('school.settings.template-dapodik');

        Route::get('/cetak-kartu', [SchoolCardController::class, 'index'])
            ->middleware('verified')
            ->name('school.cards.index');
        Route::match(['get', 'post'], '/cetak-kartu/cetak', [SchoolCardController::class, 'print'])
            ->middleware('verified')
            ->name('school.cards.print');

        Route::get('/referral', [SchoolReferralController::class, 'index'])
            ->middleware('verified')
            ->name('school.referral.index');
        Route::post('/referral/klaim', [SchoolReferralController::class, 'claim'])
            ->middleware('verified')
            ->name('school.referral.claim');

        Route::get('/broadcast', [SchoolBroadcastController::class, 'index'])
            ->middleware('verified')
            ->name('school.broadcast.index');
        Route::post('/broadcast/kirim', [SchoolBroadcastController::class, 'send'])
            ->middleware('verified')
            ->name('school.broadcast.send');
        Route::delete('/broadcast/riwayat', [SchoolBroadcastController::class, 'clearHistory'])
            ->middleware('verified')
            ->name('school.broadcast.clear');

        Route::post('/pengaturan/tema', [SchoolSettingController::class, 'updateTheme'])
            ->middleware('verified')
            ->name('school.theme.update');

        Route::post('/pengaturan/agenda', [SchoolSettingController::class, 'storeAgenda'])
            ->middleware('verified')
            ->name('school.agenda.store');

        Route::delete('/pengaturan/agenda/{id}', [SchoolSettingController::class, 'destroyAgenda'])
            ->middleware('verified')
            ->where('id', '[A-Za-z0-9\-_]+')
            ->name('school.agenda.destroy');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/school', [ProfileController::class, 'updateSchoolIdentity'])->name('profile.school.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::post('/webhooks/midtrans', WebhookController::class)
        ->withoutMiddleware([PreventRequestForgery::class])
        ->middleware('throttle:webhook')
        ->name('webhooks.midtrans');

    // Single Sign-On (SSO) Routes
    Route::get('/sso/redirect', [SsoController::class, 'redirect'])->name('sso.redirect');
    Route::get('/sso/logout', [SsoController::class, 'logout'])->name('sso.logout');
    Route::middleware('auth')->get('/sso/launch/{slug?}', [SsoController::class, 'launch'])->name('sso.launch');
    Route::middleware('auth')->get('/api/subdomain-status', [SubdomainHealthController::class, 'check'])->name('subdomain.status');

    require __DIR__.'/seo.php';
    require __DIR__.'/auth.php';
    require __DIR__.'/school-app.php';
    require __DIR__.'/admin.php';
});
