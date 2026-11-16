<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubdomainHealthAndCardStudioTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'email_verified_at' => now(),
        ]);

        $this->school = School::factory()->create([
            'owner_user_id' => $this->user->id,
            'name' => 'SMK Veyra Nusantara',
            'meta' => [
                'level' => 'SMK',
                'status' => 'Swasta',
                'brand_theme' => 'teal',
                'headmaster_name' => 'Drs. Ahmad Dahlan, M.Pd.',
                'headmaster_nip' => '19790101 200501 1 004',
            ],
        ]);

        $this->user->forceFill(['school_id' => $this->school->id])->save();
    }

    public function test_guest_cannot_access_subdomain_status_api(): void
    {
        $response = $this->getJson(route('subdomain.status'));

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_subdomain_status_api(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('subdomain.status'));

        $response->assertStatus(200)
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure([
                'status',
                'timestamp',
                'sso' => ['status', 'mode', 'ttl_seconds'],
                'services' => [
                    '*' => ['key', 'name', 'url', 'launch_url', 'status', 'latency_ms', 'icon'],
                ],
            ]);
    }

    public function test_school_card_print_renders_with_formal_template(): void
    {
        $response = $this->actingAs($this->user)->post(route('school.cards.print'), [
            'card_type' => 'student',
            'card_template' => 'formal',
            'cards_per_page' => 10,
            'students_json' => json_encode([
                [
                    'nisn' => '0089998888',
                    'nik' => '3201010101089999',
                    'name' => 'Bintang Pratama',
                    'class' => '11 RPL 1',
                    'birth_place' => 'Jakarta',
                    'birth_date' => '2008-01-01',
                ],
            ]),
        ]);

        $response->assertStatus(200)
            ->assertSee('Bintang Pratama')
            ->assertSee('KARTU TANDA PELAJAR')
            ->assertSee('grid-10')
            ->assertSee('card-box-10');
    }

    public function test_school_card_print_renders_with_cbt_template(): void
    {
        $response = $this->actingAs($this->user)->post(route('school.cards.print'), [
            'card_type' => 'exam',
            'card_template' => 'cbt',
            'cards_per_page' => 8,
            'exam_title' => 'STS GASAL 2026',
            'students_json' => json_encode([
                [
                    'nisn' => '0087776666',
                    'name' => 'Citra Lestari',
                    'class' => '12 TKJ 2',
                    'session' => 'Sesi 1',
                    'room' => 'Lab Cisco',
                    'exam_user' => '0087776666',
                    'exam_pass' => 'CITRA26',
                ],
            ]),
        ]);

        $response->assertStatus(200)
            ->assertSee('Citra Lestari')
            ->assertSee('PORTAL CBT ONLINE')
            ->assertSee('CITRA26')
            ->assertSee('grid-8')
            ->assertSee('card-box-8');
    }
}
