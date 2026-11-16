<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WebApp;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanWebAppAccessTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);

        return User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_admin_can_assign_plans_to_web_app(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);

        $planSemester = Plan::findByCode('semester');
        $planSuper = Plan::findByCode('super');

        $webApp = WebApp::create([
            'slug' => 'cbt-exam',
            'name' => 'CBT Online Exam',
            'category' => 'akademik',
            'icon' => 'academic-cap',
            'access_url' => 'https://cbt.example.com',
            'enabled' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.web-apps.update', $webApp->slug), [
            'name' => 'CBT Online Exam Pro',
            'slug' => 'cbt-exam',
            'category' => 'akademik',
            'icon' => 'academic-cap',
            'plan_ids' => [$planSuper->id],
        ]);

        $response->assertRedirect(route('admin.web-apps.show', $webApp->slug));
        $this->assertTrue($webApp->fresh()->plans->contains($planSuper->id));
        $this->assertFalse($webApp->fresh()->plans->contains($planSemester->id));
        $this->assertDatabaseHas('plan_web_apps', [
            'plan_id' => $planSuper->id,
            'web_app_id' => $webApp->id,
        ]);
    }

    public function test_admin_can_assign_web_apps_to_plan(): void
    {
        $admin = $this->admin();
        $this->seed(PlanSeeder::class);

        $planSuper = Plan::findByCode('super');

        $app1 = WebApp::create([
            'slug' => 'perpus',
            'name' => 'Perpustakaan Digital',
            'enabled' => true,
        ]);
        $app2 = WebApp::create([
            'slug' => 'keuangan',
            'name' => 'Keuangan SPP',
            'enabled' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.plans.update', $planSuper), [
            'label' => $planSuper->label,
            'description' => $planSuper->description,
            'price_idr' => $planSuper->price_idr,
            'duration_months' => $planSuper->duration_months,
            'duration_label' => $planSuper->duration_label,
            'sort_order' => $planSuper->sort_order,
            'web_app_ids' => [$app1->id, $app2->id],
        ]);

        $response->assertRedirect(route('admin.plans.index'));
        $this->assertTrue($planSuper->fresh()->hasWebApp($app1));
        $this->assertTrue($planSuper->fresh()->hasWebApp($app2));
        $this->assertDatabaseHas('plan_web_apps', [
            'plan_id' => $planSuper->id,
            'web_app_id' => $app1->id,
        ]);
    }

    public function test_school_with_plan_access_can_access_app_and_others_are_blocked(): void
    {
        $this->seed(PlanSeeder::class);

        $planSemester = Plan::findByCode('semester');
        $planSuper = Plan::findByCode('super');

        $appBasic = WebApp::create([
            'slug' => 'absensi',
            'name' => 'Absensi Digital',
            'access_url' => 'https://absensi.example.com',
            'enabled' => true,
        ]);

        $appSuper = WebApp::create([
            'slug' => 'cbt-pro',
            'name' => 'Ujian CBT Pro',
            'access_url' => 'https://cbt.example.com',
            'enabled' => true,
        ]);

        // Link cbt-pro only to Super plan
        $appSuper->plans()->sync([$planSuper->id]);
        // Link absensi to Semester plan
        $appBasic->plans()->sync([$planSemester->id]);

        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        // School subscribes to Semester plan
        Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $planSemester->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(6),
        ]);

        $this->assertTrue($school->fresh()->hasActiveSubscription());
        $this->assertTrue($school->fresh()->hasAccessToApp('absensi'));
        $this->assertFalse($school->fresh()->hasAccessToApp('cbt-pro'));

        // School trying to open cbt-pro should be forbidden (403)
        $this->actingAs($user)
            ->get('/apps/sekolah/cbt-pro')
            ->assertForbidden();
    }

    public function test_app_hub_displays_locked_state_and_upgrade_link(): void
    {
        $this->seed(PlanSeeder::class);

        $planSemester = Plan::findByCode('semester');
        $planSuper = Plan::findByCode('super');

        $appSuper = WebApp::create([
            'slug' => 'raport-merdeka',
            'name' => 'Raport Kurikulum Merdeka',
            'access_url' => 'https://raport.example.com',
            'enabled' => true,
        ]);

        $appSuper->plans()->sync([$planSuper->id]);

        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $planSemester->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths(6),
        ]);

        $response = $this->actingAs($user)->get('/apps');

        $response->assertOk();
        $response->assertSeeText('Raport Kurikulum Merdeka');
        $response->assertSeeText('Terkunci');
        $response->assertSeeText('Upgrade Paket');
        $response->assertSee($planSuper->label);
    }

    public function test_manual_school_app_grant_bypasses_plan_restriction(): void
    {
        $this->seed(PlanSeeder::class);

        $planSuper = Plan::findByCode('super');

        $appCustom = WebApp::create([
            'slug' => 'custom-app',
            'name' => 'Custom School App',
            'access_url' => 'https://custom.example.com',
            'enabled' => true,
        ]);

        $appCustom->plans()->sync([$planSuper->id]);

        $school = School::factory()->create();

        $this->assertFalse($school->hasAccessToApp('custom-app'));

        // Manually grant app to school (e.g. approved custom app request)
        $school->grantAppAccess('custom-app');

        $this->assertTrue($school->fresh()->hasAccessToApp('custom-app'));
    }
}
