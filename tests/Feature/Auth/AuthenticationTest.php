<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Masuk panel')
            ->assertSee('action="'.route('login').'"', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_admins_cannot_login_through_customer_login(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::where('email', 'admin@veyraeducate.test')->firstOrFail();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('admin_login_hint');
        $this->assertGuest();
    }

    public function test_login_updates_last_login_at(): void
    {
        $user = User::factory()->create(['last_login_at' => null]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_admin_login_does_not_update_last_login_at(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::where('email', 'admin@veyraeducate.test')->firstOrFail();

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertNull($admin->fresh()->last_login_at);
    }
}
