<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\PaymentOrder;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use App\SubscriptionStatus;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolReferralTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
    }

    public function test_school_cannot_double_claim_referral_commission(): void
    {
        $referrerUser = User::factory()->create();
        $referrerSchool = School::factory()->create([
            'owner_user_id' => $referrerUser->id,
        ]);
        $referrerUser->forceFill(['school_id' => $referrerSchool->id])->save();

        $referredUser = User::factory()->create();
        $referredSchool = School::factory()->create([
            'owner_user_id' => $referredUser->id,
            'meta' => [
                'referred_by_school_id' => $referrerSchool->id,
            ],
        ]);

        $plan = Plan::findByCode('semester');
        $subscription = $referredSchool->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
        ]);

        PaymentOrder::factory()->create([
            'school_id' => $referredSchool->id,
            'user_id' => $referredUser->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'amount_idr' => 600000,
            'status' => PaymentStatus::Paid,
        ]);

        // Gross commission = 10% of 600,000 = 60,000
        $this->assertSame(60000, $referrerSchool->getReferralEarnings());
        $this->assertSame(60000, $referrerSchool->getAvailableReferralBalance());

        // First claim succeeds
        $this->actingAs($referrerUser)
            ->post(route('school.referral.claim'), [
                'claim_type' => 'discount_voucher',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $referrerSchool->refresh();
        $this->assertSame(60000, $referrerSchool->getClaimedReferralAmount());
        $this->assertSame(0, $referrerSchool->getAvailableReferralBalance());

        // Second immediate claim is blocked because available balance is now 0
        $this->actingAs($referrerUser)
            ->post(route('school.referral.claim'), [
                'claim_type' => 'discount_voucher',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertCount(1, $referrerSchool->fresh()->meta['referral_claims'] ?? []);
    }

    public function test_cash_claim_requires_bank_account_details(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $this->actingAs($user)
            ->post(route('school.referral.claim'), [
                'claim_type' => 'cash',
            ])
            ->assertSessionHasErrors(['bank_name', 'account_number', 'account_holder']);
    }

    public function test_registration_ignores_non_existent_referral_school_id(): void
    {
        $this->post('/register', [
            'name' => 'Operator Baru',
            'email' => 'operator.baru@sekolah.sch.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'ref' => 'SCH-99999',
        ])->assertRedirect();

        $createdSchool = School::where('email', 'operator.baru@sekolah.sch.id')->firstOrFail();
        $this->assertNull($createdSchool->meta['referred_by_school_id'] ?? null);
    }

    public function test_admin_can_reject_and_approve_referral_claims(): void
    {
        $admin = User::factory()->admin()->create();

        $referrerUser = User::factory()->create();
        $referrerSchool = School::factory()->create([
            'owner_user_id' => $referrerUser->id,
        ]);
        $referrerUser->forceFill(['school_id' => $referrerSchool->id])->save();

        $referredUser = User::factory()->create();
        $referredSchool = School::factory()->create([
            'owner_user_id' => $referredUser->id,
            'meta' => ['referred_by_school_id' => $referrerSchool->id],
        ]);

        $plan = Plan::findByCode('semester');
        $subscription = $referredSchool->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonths(6),
        ]);

        PaymentOrder::factory()->create([
            'school_id' => $referredSchool->id,
            'user_id' => $referredUser->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'amount_idr' => 600000,
            'status' => PaymentStatus::Paid,
        ]);

        // School submits a claim
        $this->actingAs($referrerUser)
            ->post(route('school.referral.claim'), ['claim_type' => 'discount_voucher'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $referrerSchool->refresh();
        $claimId = $referrerSchool->meta['referral_claims'][0]['id'];
        $this->assertSame(0, $referrerSchool->getAvailableReferralBalance());

        // Admin rejects the claim -> balance returns to 60,000
        $this->actingAs($admin)
            ->patch(route('admin.referrals.update-status', [$referrerSchool, $claimId]), [
                'action' => 'reject',
                'admin_notes' => 'Mohon konfirmasi ulang.',
            ])
            ->assertRedirect(route('admin.referrals.index'));

        $referrerSchool->refresh();
        $this->assertSame('Ditolak', $referrerSchool->meta['referral_claims'][0]['status']);
        $this->assertSame(60000, $referrerSchool->getAvailableReferralBalance());

        // School can re-submit claim and Admin approves it
        $this->actingAs($referrerUser)
            ->post(route('school.referral.claim'), ['claim_type' => 'discount_voucher'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $referrerSchool->refresh();
        $newClaimId = $referrerSchool->meta['referral_claims'][1]['id'];

        $this->actingAs($admin)
            ->patch(route('admin.referrals.update-status', [$referrerSchool, $newClaimId]), [
                'action' => 'approve',
            ])
            ->assertRedirect(route('admin.referrals.index'));

        $referrerSchool->refresh();
        $this->assertSame('Dicairkan', $referrerSchool->meta['referral_claims'][1]['status']);
        $this->assertTrue($referrerSchool->hasActiveSubscription());
    }
}
