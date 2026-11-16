<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CbtStatsSyncTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedSchoolUser(): User
    {
        $school = School::factory()->create([
            'name' => 'SMKS AL-FATA',
            'npsn' => '20549210',
        ]);

        return User::factory()->create([
            'role' => User::ROLE_SCHOOL,
            'school_id' => $school->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_subdomain_status_endpoint_returns_synced_cbt_stats_from_api(): void
    {
        $user = $this->createVerifiedSchoolUser();

        Http::fake([
            '*/api/sso.php' => Http::response([
                'success' => true,
                'data' => [
                    'tenant_found' => true,
                    'tenant' => [
                        'id' => 1,
                        'code' => '20549210',
                        'slug' => 'smks-al-fata',
                        'name' => 'SMKS AL-FATA',
                        'package' => 'tahunan',
                        'student_quota' => 500,
                    ],
                    'students_active' => 240,
                    'students_total' => 250,
                    'student_quota' => 500,
                    'classes_count' => 8,
                    'teachers_count' => 18,
                    'subjects_count' => 14,
                    'question_banks_count' => 12,
                    'questions_count' => 480,
                    'active_exams_count' => 2,
                    'ongoing_sessions_count' => 35,
                    'completed_sessions_count' => 180,
                    'active_exams' => [
                        [
                            'id' => 10,
                            'title' => 'Sumatif Akhir Semester Matematika',
                            'token' => 'MTK26A',
                            'duration_minutes' => 90,
                            'start_time' => now()->addDay()->toDateTimeString(),
                            'end_time' => now()->addDays(2)->toDateTimeString(),
                            'subject_name' => 'Matematika',
                            'class_name' => 'XII TKJ 1',
                            'in_progress_count' => 20,
                            'completed_count' => 10,
                        ],
                    ],
                    'synced_at' => now()->toIso8601String(),
                ],
            ], 200),
            '*' => Http::response('', 200),
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('subdomain.status', ['refresh' => 1]));

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('cbt_stats.synced', true)
            ->assertJsonPath('cbt_stats.tenant_found', true)
            ->assertJsonPath('cbt_stats.students_active', 240)
            ->assertJsonPath('cbt_stats.student_quota', 500)
            ->assertJsonPath('cbt_stats.quota_percentage', 48)
            ->assertJsonPath('cbt_stats.question_banks_count', 12)
            ->assertJsonPath('cbt_stats.questions_count', 480)
            ->assertJsonPath('cbt_stats.active_exams_count', 2)
            ->assertJsonPath('cbt_stats.ongoing_sessions_count', 35)
            ->assertJsonPath('cbt_stats.active_exams.0.token', 'MTK26A');
    }

    public function test_dashboard_displays_synced_cbt_stats_and_active_exam_agenda(): void
    {
        $user = $this->createVerifiedSchoolUser();

        Http::fake([
            '*/api/sso.php' => Http::response([
                'success' => true,
                'data' => [
                    'tenant_found' => true,
                    'tenant' => [
                        'id' => 1,
                        'name' => 'SMKS AL-FATA',
                        'package' => 'semester',
                        'student_quota' => 500,
                    ],
                    'students_active' => 315,
                    'students_total' => 315,
                    'student_quota' => 500,
                    'classes_count' => 10,
                    'teachers_count' => 22,
                    'subjects_count' => 15,
                    'question_banks_count' => 9,
                    'questions_count' => 360,
                    'active_exams_count' => 1,
                    'ongoing_sessions_count' => 42,
                    'completed_sessions_count' => 110,
                    'active_exams' => [
                        [
                            'id' => 7,
                            'title' => 'Ujian Sekolah Bahasa Inggris',
                            'token' => 'BIG99X',
                            'duration_minutes' => 120,
                            'start_time' => now()->addDays(2)->toDateTimeString(),
                            'end_time' => now()->addDays(3)->toDateTimeString(),
                            'subject_name' => 'Bahasa Inggris',
                            'class_name' => 'XII Semua Jurusan',
                            'in_progress_count' => 42,
                            'completed_count' => 110,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Statistik Live CBT Online')
            ->assertSee('Ujian CBT: Ujian Sekolah Bahasa Inggris')
            ->assertSee('BIG99X');
    }

    public function test_cbt_stats_gracefully_falls_back_when_cbt_api_is_unavailable(): void
    {
        $user = $this->createVerifiedSchoolUser();

        Http::fake([
            '*/api/sso.php' => Http::response(['success' => false], 500),
            '*' => Http::response('', 200),
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('subdomain.status', ['refresh' => 1]));

        $response->assertOk()
            ->assertJsonPath('cbt_stats.synced', false)
            ->assertJsonPath('cbt_stats.students_active', 0)
            ->assertJsonPath('cbt_stats.student_quota', 500);
    }
}
