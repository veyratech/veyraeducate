<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManualPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
        $this->seed(AdminUserSeeder::class);
        Storage::fake('public');
    }

    public function test_can_checkout_with_manual_transfer_method(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'plan_code' => 'semester',
            'school_name' => 'SMK Negeri 1 Uji Coba',
            'school_email' => 'smk1@contoh.id',
            'school_phone' => '081234567890',
            'payment_method' => 'manual_transfer',
        ]);

        $order = PaymentOrder::latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame('manual_transfer', $order->payment_method);
        $this->assertSame(PaymentStatus::Pending, $order->status);

        $response->assertRedirect(route('pembayaran.show', $order));
    }

    public function test_school_can_upload_payment_proof(): void
    {
        $owner = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        $order = PaymentOrder::factory()->create([
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $owner->id,
            'plan_id' => $plan->id,
            'payment_method' => 'manual_transfer',
        ]);

        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $response = $this->actingAs($owner)->post(route('pembayaran.upload-proof', $order->order_id), [
            'proof' => $file,
            'bank_sender' => 'Bank Jatim Giro BOS',
            'notes' => 'Pencairan BOS tahap 1',
        ]);

        $response->assertRedirect();
        $order->refresh();

        $this->assertTrue($order->hasProof());
        $this->assertSame('Bank Jatim Giro BOS', $order->bank_sender);
        $this->assertSame('Pencairan BOS tahap 1', $order->notes);
        Storage::disk('public')->assertExists($order->proof_path);
    }

    public function test_admin_can_verify_and_mark_paid_manual_order(): void
    {
        $admin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $owner = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $plan = Plan::findByCode('semester');
        $subscription = $school->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'pending',
        ]);

        $order = PaymentOrder::factory()->create([
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'user_id' => $owner->id,
            'plan_id' => $plan->id,
            'payment_method' => 'manual_transfer',
            'proof_path' => 'payment_proofs/test.jpg',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments.show', $order))
            ->assertOk()
            ->assertSee('Bukti Transfer Bank Sekolah');

        $this->actingAs($admin)
            ->post(route('admin.payments.mark-paid', $order))
            ->assertRedirect();

        $order->refresh();
        $subscription->refresh();

        $this->assertTrue($order->isPaid());
        $this->assertTrue($subscription->isActive());
    }
}
