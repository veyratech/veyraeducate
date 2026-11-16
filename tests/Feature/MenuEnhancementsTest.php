<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\AppGuide;
use App\Models\AppRequest;
use App\Models\AuditLog;
use App\Models\Deployment;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WebApp;
use App\SubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_app_request_status_and_admin_notes(): void
    {
        $admin = User::factory()->admin()->create();
        $school = School::factory()->create(['name' => 'SMAN 1 Nusantara']);
        $schoolUser = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
        ]);

        $appRequest = AppRequest::query()->create([
            'school_id' => $school->id,
            'user_id' => $schoolUser->id,
            'name' => 'Sistem Absensi Wajah',
            'description' => 'Membutuhkan aplikasi absensi pengenalan wajah terintegrasi WA.',
            'category' => 'kesiswaan',
            'priority' => 'tinggi',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.app-requests.update-status', $appRequest), [
            'status' => 'approved',
            'admin_notes' => 'Siap dikembangkan pada rilis bulan depan.',
        ]);

        $response->assertRedirect(route('admin.app-requests.index'));

        $appRequest->refresh();
        $this->assertSame('approved', $appRequest->status);
        $this->assertSame('Siap dikembangkan pada rilis bulan depan.', $appRequest->admin_notes);

        $filterResponse = $this->actingAs($admin)->get(route('admin.app-requests.index', [
            'status' => 'approved',
            'q' => 'Absensi Wajah',
        ]));

        $filterResponse->assertOk()
            ->assertSee('Sistem Absensi Wajah')
            ->assertSee('Siap dikembangkan pada rilis bulan depan.');
    }

    public function test_school_can_cancel_pending_app_request_but_not_approved_request(): void
    {
        $school = School::factory()->create();
        $schoolUser = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
        ]);

        $pendingRequest = AppRequest::query()->create([
            'school_id' => $school->id,
            'user_id' => $schoolUser->id,
            'name' => 'Modul Koperasi Siswa',
            'description' => 'Aplikasi pencatatan transaksi koperasi sekolah harian.',
            'category' => 'keuangan',
            'priority' => 'normal',
            'status' => 'pending',
        ]);

        $approvedRequest = AppRequest::query()->create([
            'school_id' => $school->id,
            'user_id' => $schoolUser->id,
            'name' => 'ModulInventaris',
            'description' => 'Aplikasi inventaris sarana dan prasarana sekolah.',
            'category' => 'lainnya',
            'priority' => 'normal',
            'status' => 'approved',
        ]);

        $this->actingAs($schoolUser)
            ->delete(route('school-app.requests.destroy', $pendingRequest))
            ->assertRedirect(route('school-app.requests.index'));

        $this->assertSoftDeleted('app_requests', ['id' => $pendingRequest->id]);

        $this->actingAs($schoolUser)
            ->delete(route('school-app.requests.destroy', $approvedRequest))
            ->assertRedirect(route('school-app.requests.index'));

        $this->assertNotSoftDeleted('app_requests', ['id' => $approvedRequest->id]);
    }

    public function test_admin_can_edit_user_and_reset_password(): void
    {
        $admin = User::factory()->admin()->create();
        $school = School::factory()->create(['name' => 'SMK Negeri 2 Teknologi']);
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@smkn2.sch.id',
            'role' => User::ROLE_SCHOOL,
            'school_id' => null,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Budi Santoso, S.Kom',
            'email' => 'budi.operator@smkn2.sch.id',
            'phone' => '081234567890',
            'role' => User::ROLE_OPERATOR,
            'school_id' => $school->id,
            'password' => 'passwordbaru123',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Budi Santoso, S.Kom', $user->name);
        $this->assertSame('budi.operator@smkn2.sch.id', $user->email);
        $this->assertSame(User::ROLE_OPERATOR, $user->role);
        $this->assertSame($school->id, $user->school_id);
        $this->assertTrue(Hash::check('passwordbaru123', $user->password));
    }

    public function test_admin_can_grant_free_trial_and_manual_subscription_to_school(): void
    {
        $admin = User::factory()->admin()->create();
        $school = School::factory()->create(['name' => 'SMP Harapan Bangsa']);
        $plan = Plan::factory()->create([
            'code' => '1-tahun',
            'label' => 'Paket 1 Tahun',
            'duration_months' => 12,
            'is_active' => true,
            'is_default' => true,
        ]);
        $app = WebApp::factory()->create([
            'slug' => 'cbtonline',
            'name' => 'CBT Online',
            'enabled' => true,
            'access_url' => 'https://cbt.example.com',
        ]);

        $trialResponse = $this->actingAs($admin)->post(route('admin.schools.grant-subscription', $school), [
            'mode' => 'trial',
            'plan_id' => $plan->id,
            'trial_days' => 14,
            'notes' => 'Trial sosialisasi dinas',
        ]);

        $trialResponse->assertRedirect(route('admin.schools.show', $school));
        $this->assertTrue($school->fresh()->hasActiveSubscription());
        $this->assertTrue($school->fresh()->hasAccessToApp('cbtonline'));

        $planResponse = $this->actingAs($admin)->post(route('admin.schools.grant-subscription', $school), [
            'mode' => 'plan',
            'plan_id' => $plan->id,
            'duration_months' => 6,
            'notes' => 'Perpanjangan semester ganjil',
        ]);

        $planResponse->assertRedirect(route('admin.schools.show', $school));
        $activeSub = $school->fresh()->activeSubscription()->first();
        $this->assertNotNull($activeSub);
        $this->assertTrue($activeSub->ends_at->greaterThan(now()->addMonths(5)));
    }

    public function test_school_user_can_filter_apps_hub_and_see_guides_on_app_detail(): void
    {
        $school = School::factory()->create();
        $schoolUser = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
        ]);

        $cbtApp = WebApp::factory()->create([
            'slug' => 'cbt-ujian',
            'name' => 'CBT Ujian Sekolah',
            'category' => 'akademik',
            'enabled' => true,
            'access_url' => 'https://cbt.example.com',
        ]);

        $bosApp = WebApp::factory()->create([
            'slug' => 'spj-bos',
            'name' => 'Laporan SPJ BOS',
            'category' => 'keuangan',
            'enabled' => true,
            'access_url' => 'https://bos.example.com',
        ]);

        AppGuide::query()->create([
            'web_app_id' => $cbtApp->id,
            'user_id' => $schoolUser->id,
            'title' => 'Panduan Proktor CBT Ujian',
            'version' => 'v1.0',
            'file_path' => 'guides/cbt.pdf',
            'file_name' => 'cbt.pdf',
            'file_size' => 2048,
            'file_type' => 'pdf',
        ]);

        $hubResponse = $this->actingAs($schoolUser)->get(route('apps.index', [
            'category' => 'akademik',
            'q' => 'CBT',
        ]));

        $hubResponse->assertOk()
            ->assertSee('CBT Ujian Sekolah')
            ->assertDontSee('Laporan SPJ BOS');

        $detailResponse = $this->actingAs($schoolUser)->get(route('apps.show', $cbtApp->slug));

        $detailResponse->assertOk()
            ->assertSee('CBT Ujian Sekolah')
            ->assertSee('Panduan Proktor CBT Ujian');
    }

    public function test_school_broadcast_logs_can_be_saved_and_cleared(): void
    {
        $school = School::factory()->create(['name' => 'SMA Negeri 3 Unggulan']);
        $schoolUser = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
            'phone' => '081234567890',
        ]);

        $this->actingAs($schoolUser)
            ->get(route('school.broadcast.index'))
            ->assertOk()
            ->assertSee('SMA Negeri 3 Unggulan');

        $sendResponse = $this->actingAs($schoolUser)->post(route('school.broadcast.send'), [
            'title' => 'Pengumuman Gladi Bersih CBT',
            'message' => 'Mohon seluruh siswa hadir tepat waktu pukul 07.30 WIB.',
            'recipient_type' => 'parents',
            'send_method' => 'wa_link',
            'phone' => '081234567890',
        ]);

        $sendResponse->assertRedirect();
        $logs = $school->fresh()->meta['broadcast_logs'] ?? [];
        $this->assertCount(1, $logs);
        $this->assertSame('Pengumuman Gladi Bersih CBT', $logs[0]['title']);
        $this->assertSame('Mohon seluruh siswa hadir tepat waktu pukul 07.30 WIB.', $logs[0]['message']);

        $this->actingAs($schoolUser)
            ->delete(route('school.broadcast.clear'))
            ->assertRedirect(route('school.broadcast.index'));

        $this->assertEmpty($school->fresh()->meta['broadcast_logs'] ?? []);
    }

    public function test_admin_plan_default_switching_and_deployment_filtering(): void
    {
        $admin = User::factory()->admin()->create();

        $oldDefault = Plan::factory()->create([
            'code' => 'semester',
            'label' => 'Paket Semester',
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.plans.store'), [
            'code' => 'tahunan-baru',
            'label' => 'Paket Tahunan Baru',
            'price_idr' => 1100000,
            'duration_months' => 12,
            'duration_label' => '1 Tahun',
            'sort_order' => 1,
            'is_active' => 1,
            'is_default' => 1,
        ])->assertRedirect();

        $this->assertFalse($oldDefault->fresh()->is_default);
        $this->assertTrue(Plan::where('code', 'tahunan-baru')->firstOrFail()->is_default);

        $appA = WebApp::factory()->create(['slug' => 'app-a', 'name' => 'Aplikasi A']);
        $appB = WebApp::factory()->create(['slug' => 'app-b', 'name' => 'Aplikasi B']);

        Deployment::query()->create([
            'web_app_id' => $appA->id,
            'slug' => 'app-a',
            'version' => '1.0.0',
            'status' => 'success',
            'created_by' => $admin->id,
        ]);

        Deployment::query()->create([
            'web_app_id' => $appB->id,
            'slug' => 'app-b',
            'version' => '2.0.0',
            'status' => 'failed',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.deployments.index', ['app' => $appA->id, 'status' => 'success']))
            ->assertOk()
            ->assertSee('1.0.0')
            ->assertDontSee('2.0.0');

        $this->assertGreaterThan(0, AuditLog::where('action', 'plan_created')->count());
    }

    public function test_sso_launch_tracks_last_app_access_and_admin_dashboard_counts_all_roles(): void
    {
        $school = School::factory()->create([
            'name' => 'SMA Negeri 8 Digital',
            'last_app_access_at' => null,
            'meta' => [
                'referral_claims' => [
                    ['id' => 'CLM-TEST1', 'amount' => 110000, 'status' => 'Sedang Diproses'],
                ],
            ],
        ]);

        $schoolUser = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
            'last_login_at' => now(),
        ]);

        $operatorUser = User::factory()->create([
            'role' => User::ROLE_OPERATOR,
            'school_id' => $school->id,
            'last_login_at' => now(),
        ]);

        $plan = Plan::factory()->create(['is_active' => true]);
        Subscription::query()->create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
            'activated_at' => now(),
        ]);

        WebApp::factory()->create([
            'slug' => 'cbtonline',
            'name' => 'CBT Online',
            'enabled' => true,
            'access_url' => 'https://cbt.example.com',
        ]);

        $this->actingAs($schoolUser)
            ->get(route('sso.launch', 'cbtonline'))
            ->assertRedirect();

        $this->assertNotNull($school->fresh()->last_app_access_at);

        $admin = User::factory()->admin()->create();
        $dashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboardResponse->assertOk()
            ->assertViewHas('totalSchoolUsers', 3)
            ->assertViewHas('pendingReferralClaims', 1);
    }

    public function test_checkout_midtrans_sends_discounted_gross_amount_and_upload_proof_switches_to_manual(): void
    {
        Storage::fake('public');
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);

        $capturedGrossAmount = null;
        Http::fake(function ($request) use (&$capturedGrossAmount) {
            $data = $request->data();
            $capturedGrossAmount = $data['transaction_details']['gross_amount'] ?? null;

            return Http::response(['token' => 'snap-token-123'], 201);
        });

        $referrerSchool = School::factory()->create(['name' => 'SMK Referrer']);
        $referrerUser = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $referrerSchool->id,
        ]);

        $plan = Plan::factory()->create([
            'code' => 'paket-tahunan',
            'label' => 'Paket 1 Tahun',
            'price_idr' => 1100000,
            'is_active' => true,
        ]);

        $referredSchool = School::factory()->create([
            'meta' => ['referred_by_school_id' => $referrerSchool->id],
        ]);

        $referredSub = Subscription::query()->create([
            'school_id' => $referredSchool->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
        ]);

        PaymentOrder::query()->create([
            'order_id' => 'VEY-PAID-REF1',
            'subscription_id' => $referredSub->id,
            'school_id' => $referredSchool->id,
            'user_id' => $referrerUser->id,
            'plan_id' => $plan->id,
            'amount_idr' => 1100000,
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $this->assertSame(110000, $referrerSchool->getAvailableReferralBalance());

        $this->actingAs($referrerUser)->post(route('checkout.store'), [
            'plan_code' => 'paket-tahunan',
            'school_name' => 'SMK Referrer',
            'school_email' => 'info@smkreferrer.sch.id',
            'school_phone' => '08123456789',
            'payment_method' => 'midtrans',
            'use_referral_balance' => 1,
        ])->assertRedirect();

        $this->assertSame(990000, $capturedGrossAmount);

        $latestOrder = PaymentOrder::query()->latest('id')->firstOrFail();
        $this->assertSame(990000, $latestOrder->amount_idr);

        $proofFile = UploadedFile::fake()->create('resi_bos.pdf', 200, 'application/pdf');
        $this->actingAs($referrerUser)->post(route('pembayaran.upload-proof', $latestOrder->order_id), [
            'proof' => $proofFile,
            'bank_sender' => 'Bank Jatim',
            'notes' => 'Transfer Giro BOS',
        ])->assertRedirect();

        $latestOrder->refresh();
        $this->assertSame('manual_transfer', $latestOrder->payment_method);
        $this->assertSame(PaymentStatus::Pending, $latestOrder->status);
        $this->assertNotNull($latestOrder->proof_path);
    }
}
