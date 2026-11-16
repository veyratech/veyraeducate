<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolFeaturesTest extends TestCase
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
            'name' => 'SMA Veyra Nusantara',
            'meta' => [
                'level' => 'SMA',
                'status' => 'Swasta',
                'referral_code' => 'SCH-TEST01',
                'brand_theme' => 'teal',
            ],
        ]);

        $this->user->forceFill(['school_id' => $this->school->id])->save();
    }

    /**
     * Feature 2: School Card Generator Index.
     */
    public function test_school_card_generator_screen_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('school.cards.index'));

        $response->assertStatus(200)
            ->assertSee('Generator Kartu Siswa')
            ->assertSee('Cetak Kartu Peserta Ujian')
            ->assertSee('Ahmad Pratama Putra');
    }

    /**
     * Feature 2: School Card Print layout (A4 with 8 cards).
     */
    public function test_school_card_print_screen_renders_with_sample_students(): void
    {
        $response = $this->actingAs($this->user)->post(route('school.cards.print'), [
            'card_type' => 'student',
            'students_json' => json_encode([
                [
                    'nisn' => '0089999991',
                    'nik' => '3201010101080001',
                    'name' => 'Budi Santoso',
                    'gender' => 'L',
                    'class' => '10 IPA 1',
                    'birth_place' => 'Jakarta',
                    'birth_date' => '2008-01-01',
                ],
            ]),
        ]);

        $response->assertStatus(200)
            ->assertSee('KARTU TANDA PELAJAR')
            ->assertSee('Budi Santoso')
            ->assertSee('0089999991')
            ->assertSee('a4-page');
    }

    /**
     * Feature 1: Registration with Referral Code links referring school.
     */
    public function test_registration_with_referral_code_records_referrer(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Operator Mitra Baru',
            'email' => 'mitra@sekolahbaru.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'ref' => $this->school->referral_code,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $newSchool = School::where('name', 'Sekolah Operator Mitra Baru')->first();
        $this->assertNotNull($newSchool);
        $this->assertEquals($this->school->id, $newSchool->meta['referred_by_school_id'] ?? null);
    }

    /**
     * Feature 1: Referral Portal displays stats and link.
     */
    public function test_school_referral_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('school.referral.index'));

        $response->assertStatus(200)
            ->assertSee('Program Kemitraan &amp; Afiliasi', false)
            ->assertSee($this->school->referral_code)
            ->assertSee('Saldo Siap Klaim');
    }

    /**
     * Feature 3: Official Proposal page renders with interactive BOS calculator.
     */
    public function test_proposal_screen_renders_with_bos_calculator(): void
    {
        $response = $this->get(route('proposal'));

        $response->assertStatus(200)
            ->assertSee('Proposal Resmi Veyra Educate')
            ->assertSee('Simulasi Efisiensi Anggaran BOS')
            ->assertSee('totalSavings')
            ->assertSee('Permendikbudristek No. 63 Tahun 2023');
    }

    /**
     * Feature 4: WhatsApp Broadcast portal renders and dispatches announcements.
     */
    public function test_school_broadcast_portal_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('school.broadcast.index'));

        $response->assertStatus(200)
            ->assertSee('Pusat Komunikasi')
            ->assertSee('exam_schedule')
            ->assertSee('Official School Channel');
    }

    /**
     * Feature 4: Broadcast send generates WhatsApp link and writes to logs.
     */
    public function test_school_broadcast_send_via_wa_link(): void
    {
        $response = $this->actingAs($this->user)->post(route('school.broadcast.send'), [
            'title' => 'Pengumuman Ujian STS',
            'message' => 'Halo siswa, besok ada gladi bersih CBT.',
            'recipient_type' => 'parents',
            'phone' => '08123456789',
            'send_method' => 'wa_link',
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('wa.me', $response->headers->get('Location'));

        $this->school->refresh();
        $logs = $this->school->meta['broadcast_logs'] ?? [];
        $this->assertNotEmpty($logs);
        $this->assertEquals('Pengumuman Ujian STS', $logs[0]['title']);
    }

    /**
     * Feature 5: Persistent School Brand Theme Update.
     */
    public function test_school_brand_theme_can_be_updated_and_persisted(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('school.theme.update'), [
            'brand_theme' => 'indigo',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'brand_theme' => 'indigo',
            ]);

        $this->school->refresh();
        $this->assertEquals('indigo', $this->school->meta['brand_theme']);
    }
}
