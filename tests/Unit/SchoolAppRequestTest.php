<?php

namespace Tests\Unit;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolAppRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_application_link_exists_in_dashboard(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200)
            ->assertSee('/apps/sekolah/requests')
            ->assertSee('Permintaan App');
    }

    public function test_school_can_see_requests_page(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);

        $this->actingAs($user);

        $response = $this->get('/apps/sekolah/requests');

        $response->assertStatus(200)
            ->assertSee('Permintaan Aplikasi');
    }
}
