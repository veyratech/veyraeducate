<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
    }

    public function test_user_role_helpers_work_correctly(): void
    {
        $admin = new User(['role' => 'admin']);
        $school = new User(['role' => 'school']);
        $bendahara = new User(['role' => 'bendahara']);
        $operator = new User(['role' => 'operator']);
        $teacher = new User(['role' => 'teacher']);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($school->isSchoolAdmin());
        $this->assertTrue($bendahara->isTreasurer());
        $this->assertTrue($operator->isOperator());

        $this->assertTrue($school->canAccessBilling());
        $this->assertTrue($bendahara->canAccessBilling());
        $this->assertFalse($teacher->canAccessBilling());
        $this->assertFalse($operator->canAccessBilling());

        $this->assertSame('Bendahara BOS', $bendahara->roleLabel());
        $this->assertSame('Operator Dapodik & IT', $operator->roleLabel());
        $this->assertSame('Guru / Pengajar', $teacher->roleLabel());
    }

    public function test_school_owner_can_add_treasurer_and_operator_members(): void
    {
        $owner = User::factory()->create(['role' => 'school']);
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $owner->update(['school_id' => $school->id]);

        $response = $this->actingAs($owner)->post(route('school.settings.team.store'), [
            'name' => 'Ahmad Bendahara, S.E.',
            'email' => 'bendahara@sekolah.sch.id',
            'role' => 'bendahara',
            'phone' => '081234567890',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('school.settings', ['tab' => 'tim']));
        $this->assertDatabaseHas('users', [
            'email' => 'bendahara@sekolah.sch.id',
            'role' => 'bendahara',
            'school_id' => $school->id,
        ]);
    }

    public function test_treasurer_can_access_billing_page(): void
    {
        $owner = User::factory()->create(['role' => 'school']);
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $treasurer = User::factory()->create([
            'role' => 'bendahara',
            'school_id' => $school->id,
        ]);

        $this->actingAs($treasurer)
            ->get(route('pembayaran.history'))
            ->assertOk();
    }

    public function test_teacher_cannot_access_billing_page_and_is_redirected(): void
    {
        $owner = User::factory()->create(['role' => 'school']);
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'school_id' => $school->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('pembayaran.history'))
            ->assertRedirect(route('dashboard'));
    }
}
