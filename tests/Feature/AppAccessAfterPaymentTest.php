<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WebApp;
use App\SubscriptionStatus;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AppAccessAfterPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('midtrans.server_key', 'Mid-server-test-key');
        Config::set('midtrans.client_key', 'Mid-client-test-key');
        Config::set('midtrans.is_production', false);
    }

    private function admin(): User
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(PlanSeeder::class);

        return User::query()->where('email', 'admin@veyraeducate.test')->firstOrFail();
    }

    public function test_school_without_payment_cannot_access_app(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $app = WebApp::where('slug', 'rapor')->first();
        $this->assertTrue($app->enabled);
        $this->assertFalse($school->hasAccessToApp('rapor'));

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('sso.launch', 'cbtonline'))
            ->assertForbidden();
    }

    public function test_school_without_payment_sees_no_apps_in_dashboard(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk()
            ->assertDontSeeText('Rapor Digital');
    }

    public function test_payment_grants_app_access_automatically(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $plan = Plan::where('code', 'semester')->first();
        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
        ]);
        $order = PaymentOrder::create([
            'order_id' => 'VEY-WEBHOOK-TEST-001',
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_idr' => $plan->price_idr,
            'status' => PaymentStatus::Pending,
            'expired_at' => now()->addDay(),
        ]);

        $this->assertFalse($school->fresh()->hasAccessToApp('rapor'));

        $payload = [
            'order_id' => $order->order_id,
            'status_code' => '200',
            'transaction_status' => 'settlement',
            'transaction_id' => 'txn-123',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].'Mid-server-test-key');

        $this->postJson('/webhooks/midtrans', $payload)
            ->assertOk();

        $order->refresh();
        $this->assertEquals(PaymentStatus::Paid, $order->status);
        $this->assertTrue($school->fresh()->hasAccessToApp('rapor'));

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertOk();
    }

    public function test_mark_paid_grants_app_access(): void
    {
        $admin = $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $plan = Plan::where('code', 'semester')->first();
        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
        ]);
        $order = PaymentOrder::create([
            'order_id' => 'VEY-MARK-TEST-001',
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_idr' => $plan->price_idr,
            'status' => PaymentStatus::Pending,
            'expired_at' => now()->addDay(),
        ]);

        $this->assertFalse($school->fresh()->hasAccessToApp('rapor'));

        $this->actingAs($admin)
            ->post(route('admin.payments.mark-paid', $order->order_id))
            ->assertRedirect();

        $order->refresh();
        $this->assertEquals(PaymentStatus::Paid, $order->status);
        $this->assertTrue($school->fresh()->hasAccessToApp('rapor'));

        $this->actingAs($user)
            ->get('/apps/sekolah/rapor')
            ->assertOk();
    }

    public function test_dashboard_shows_apps_after_payment(): void
    {
        $admin = $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $plan = Plan::where('code', 'semester')->first();
        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
        ]);
        $order = PaymentOrder::create([
            'order_id' => 'VEY-DASH-TEST-001',
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_idr' => $plan->price_idr,
            'status' => PaymentStatus::Pending,
            'expired_at' => now()->addDay(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.mark-paid', $order->order_id));

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $this->assertTrue($school->fresh()->hasAccessToApp('rapor'));
    }

    public function test_school_with_existing_subscription_sees_apps_for_open_plan(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('rapor', [], ['name' => 'Rapor Digital']);

        $plan = Plan::where('code', 'semester')->first();
        Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
        ]);

        // App has no plan restrictions, plan has no app restrictions → access granted
        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Rapor Digital');
        $this->assertTrue($school->fresh()->hasAccessToApp('rapor'));
    }

    public function test_admin_can_grant_individual_app_access(): void
    {
        $this->admin();
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->installSchoolApp('app-a', ['index.blade.php' => '<div>App A</div>'], ['name' => 'App A']);
        $this->installSchoolApp('app-b', ['index.blade.php' => '<div>App B</div>'], ['name' => 'App B']);

        $school->grantAppAccess('app-a');
        $this->assertTrue($school->hasAccessToApp('app-a'));
        $this->assertFalse($school->hasAccessToApp('app-b'));
    }
}
