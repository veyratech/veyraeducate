<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_card_studio_loads_and_prints_cards_with_persisted_students(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create([
            'owner_user_id' => $user->id,
            'name' => 'SMA Negeri 1 Kartu',
        ]);
        $user->forceFill(['school_id' => $school->id])->save();

        // 1. Index page loads cleanly with cardStudioData()
        $this->actingAs($user)
            ->get(route('school.cards.index'))
            ->assertOk()
            ->assertSee('cardStudioData()')
            ->assertSee('Cetak Kartu Peserta Ujian');

        $customStudents = [
            [
                'nisn' => '0099887766',
                'nik' => '32010099887766',
                'name' => 'Budi Santoso',
                'gender' => 'L',
                'class' => '12 IPA 1',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-01-01',
                'session' => 'Sesi 1',
                'room' => 'Lab 1',
                'exam_user' => '0099887766',
                'exam_pass' => 'BUDI26',
            ],
        ];

        // 2. Print POST renders A4 cards and persists students to school meta
        $this->actingAs($user)
            ->post(route('school.cards.print'), [
                'card_type' => 'exam',
                'card_template' => 'cbt',
                'cards_per_page' => 8,
                'card_theme' => 'emerald',
                'exam_title' => 'UJIAN SEKOLAH 2026',
                'academic_year' => '2026/2027',
                'headmaster_name' => 'Dr. Hendra, M.Pd.',
                'headmaster_nip' => '19800101 200501 1 001',
                'students_json' => json_encode($customStudents),
            ])
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('0099887766')
            ->assertSee('BUDI26')
            ->assertSee('Dr. Hendra, M.Pd.');

        $school->refresh();
        $this->assertCount(1, $school->meta['card_students'] ?? []);
        $this->assertSame('Budi Santoso', $school->meta['card_students'][0]['name']);

        // 3. Refreshing /cetak-kartu/cetak via GET also works using persisted students
        $this->actingAs($user)
            ->get(route('school.cards.print'))
            ->assertOk()
            ->assertSee('Budi Santoso');
    }
}
