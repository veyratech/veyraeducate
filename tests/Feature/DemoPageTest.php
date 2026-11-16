<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_page_is_accessible(): void
    {
        $response = $this->get('/demo');

        $response->assertOk();
        $response->assertSee('Simulator Ujian Siswa', false);
        $response->assertSee('Lembar Jawaban Cepat', false);
        $response->assertSee('Masuk Portal Sekolah Demo', false);
    }

    public function test_demo_login_creates_and_authenticates_demo_school_user(): void
    {
        $this->seed(PlanSeeder::class);

        $response = $this->post(route('demo.login'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        /** @var User $user */
        $user = auth()->user();
        $this->assertSame('demo.sekolah@veyraeducate.id', $user->email);
        $this->assertNotNull($user->school);
        $this->assertTrue($user->school->hasActiveSubscription());
    }

    public function test_demo_login_reuses_existing_demo_user_cleanly(): void
    {
        $this->seed(PlanSeeder::class);

        $this->post(route('demo.login'))->assertRedirect(route('dashboard'));
        auth()->logout();

        $this->post(route('demo.login'))->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertSame(1, User::where('email', 'demo.sekolah@veyraeducate.id')->count());
    }
}
