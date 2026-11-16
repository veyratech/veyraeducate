<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\AppGuideController as AdminAppGuideController;
use App\Http\Controllers\Admin\AppRequestController as AdminAppRequestController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeploymentController;
use App\Http\Controllers\Admin\HealthController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\LinkAccessController;
use App\Http\Controllers\Admin\PaymentOrderController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ReferralClaimController;
use App\Http\Controllers\Admin\ScheduleMonitorController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebAppController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'throttle:auth'])->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store']);
});

Route::middleware(['auth', 'admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/sekolah', [SchoolController::class, 'index'])->name('schools.index');
    Route::get('/sekolah/lookup-sekolah/{code}', [SchoolController::class, 'lookupSekolah'])
        ->where('code', '\d{8,13}')
        ->name('schools.lookup-sekolah');
    Route::get('/sekolah/lookup-npsn/{npsn}', [SchoolController::class, 'lookupNpsn'])
        ->where('npsn', '\d{8}')
        ->name('schools.lookup-npsn');
    Route::get('/sekolah/lookup-nsm/{nsm}', [SchoolController::class, 'lookupNsm'])
        ->where('nsm', '\d{10,13}')
        ->name('schools.lookup-nsm');
    Route::post('/sekolah', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('/sekolah/{school}', [SchoolController::class, 'show'])->name('schools.show');
    Route::get('/sekolah/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
    Route::put('/sekolah/{school}', [SchoolController::class, 'update'])->name('schools.update');
    Route::post('/sekolah/{school}/langganan', [SchoolController::class, 'grantSubscription'])->name('schools.grant-subscription');
    Route::get('/sekolah/import', [SchoolController::class, 'import'])->name('schools.import');
    Route::post('/sekolah/process-import', [SchoolController::class, 'processImport'])->name('schools.process-import');

    Route::middleware(['super.admin'])->group(function () {
        Route::delete('/sekolah/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');
    });

    Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
    Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');

    Route::middleware(['super.admin'])->group(function () {
        Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/aplikasi', [WebAppController::class, 'index'])->name('web-apps.index');
    Route::get('/aplikasi/{slug}', [WebAppController::class, 'show'])->name('web-apps.show');
    Route::get('/aplikasi/{slug}/edit', [WebAppController::class, 'edit'])->name('web-apps.edit');
    Route::put('/aplikasi/{slug}', [WebAppController::class, 'update'])->name('web-apps.update');
    Route::post('/aplikasi', [WebAppController::class, 'store'])->name('web-apps.store');
    Route::post('/aplikasi/upload', [WebAppController::class, 'upload'])->name('web-apps.upload');
    Route::patch('/aplikasi/{slug}/toggle', [WebAppController::class, 'toggle'])
        ->where('slug', '[a-z0-9\-_]+')
        ->name('web-apps.toggle');
    Route::delete('/aplikasi/{slug}', [WebAppController::class, 'uninstall'])
        ->where('slug', '[a-z0-9\-_]+')
        ->name('web-apps.uninstall');

    Route::get('/link-akses', [LinkAccessController::class, 'index'])->name('link-access.index');
    Route::post('/link-akses', [LinkAccessController::class, 'store'])->name('link-access.store');
    Route::patch('/link-akses/{slug}', [LinkAccessController::class, 'update'])
        ->where('slug', '[a-z0-9\-_]+')
        ->name('link-access.update');

    Route::get('/paket', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/paket/baru', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/paket', [PlanController::class, 'store'])->name('plans.store');
    Route::get('/paket/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/paket/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/paket/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

    Route::get('/pembayaran', [PaymentOrderController::class, 'index'])->name('payments.index');
    Route::get('/pembayaran/{order}', [PaymentOrderController::class, 'show'])->name('payments.show');
    Route::post('/pembayaran/{order}/lunas', [PaymentOrderController::class, 'markPaid'])->name('payments.mark-paid');
    Route::get('/pembayaran/export', [PaymentOrderController::class, 'export'])->name('payments.export');

    Route::get('/referral', [ReferralClaimController::class, 'index'])->name('referrals.index');
    Route::patch('/referral/{school}/{claimId}', [ReferralClaimController::class, 'updateStatus'])
        ->where('claimId', '[A-Za-z0-9\-]+')
        ->name('referrals.update-status');

    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::get('/api-tokens/baru', [ApiTokenController::class, 'create'])->name('api-tokens.create');
    Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/pengaturan/hero', [SettingController::class, 'destroyHero'])->name('settings.destroy-hero');
    Route::post('/pengaturan/test-whatsapp', [SettingController::class, 'testWhatsApp'])->name('settings.test-whatsapp');

    Route::get('/database', [BackupController::class, 'index'])->name('backup.index');

    Route::middleware(['super.admin'])->group(function () {
        Route::get('/database/download', [BackupController::class, 'download'])->name('backup.download');
        Route::post('/database/optimize', [BackupController::class, 'optimize'])->name('backup.optimize');
    });

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::get('/sistem/diagnosa', [SystemController::class, 'index'])
        ->name('system.diagnose');

    Route::get('/sistem/kesehatan', [HealthController::class, 'index'])
        ->name('system.health');
    Route::get('/sistem/kesehatan/api', [HealthController::class, 'api'])
        ->name('system.health.api');

    Route::get('/sistem/jadwal', [ScheduleMonitorController::class, 'index'])
        ->name('schedule.index');
    Route::post('/sistem/jadwal/run-now', [ScheduleMonitorController::class, 'runNow'])
        ->name('schedule.run-now');

    Route::middleware(['super.admin'])->group(function () {
        Route::post('/sistem/diagnosa/run', [SystemController::class, 'diagnose'])
            ->name('system.diagnose.run');
        Route::post('/sistem/backup/run', [SystemController::class, 'runBackup'])
            ->name('system.backup.run');
    });

    Route::get('/homepage', [HomepageSectionController::class, 'index'])->name('homepage.index');
    Route::get('/homepage/baru', [HomepageSectionController::class, 'create'])->name('homepage.create');
    Route::post('/homepage', [HomepageSectionController::class, 'store'])->name('homepage.store');
    Route::get('/homepage/{section}/edit', [HomepageSectionController::class, 'edit'])->name('homepage.edit');
    Route::put('/homepage/{section}', [HomepageSectionController::class, 'update'])->name('homepage.update');
    Route::delete('/homepage/{section}', [HomepageSectionController::class, 'destroy'])->name('homepage.destroy');
    Route::patch('/homepage/{section}/toggle', [HomepageSectionController::class, 'toggle'])->name('homepage.toggle');
    Route::post('/homepage/{section}/move', [HomepageSectionController::class, 'move'])->name('homepage.move');
    Route::post('/homepage/reset-default', [HomepageSectionController::class, 'resetToDefault'])->name('homepage.reset-default');

    Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/pengumuman/baru', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/pengumuman', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/pengumuman/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/pengumuman/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::patch('/pengumuman/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::post('/pengumuman/broadcast', [AnnouncementController::class, 'broadcast'])->name('announcements.broadcast');

    Route::middleware(['super.admin'])->group(function () {
        Route::delete('/pengumuman/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    Route::get('/permintaan-aplikasi', [AdminAppRequestController::class, 'index'])->name('app-requests.index');
    Route::put('/permintaan-aplikasi/{appRequest}/status', [AdminAppRequestController::class, 'updateStatus'])
        ->name('app-requests.update-status');

    Route::get('/panduan/health', [AdminAppGuideController::class, 'healthCheck'])
        ->name('guides.health');
    Route::get('/panduan', [AdminAppGuideController::class, 'index'])->name('guides.index');
    Route::post('/panduan', [AdminAppGuideController::class, 'store'])->name('guides.store');
    Route::put('/panduan/{guide}', [AdminAppGuideController::class, 'update'])->name('guides.update');
    Route::delete('/panduan/{guide}', [AdminAppGuideController::class, 'destroy'])->name('guides.destroy');

    Route::get('/deployment', [DeploymentController::class, 'index'])->name('deployments.index');
    Route::get('/deployment/{slug}', [DeploymentController::class, 'show'])
        ->where('slug', '[a-z0-9\-_]+')
        ->name('deployments.show');

    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
});
