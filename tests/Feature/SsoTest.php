<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\WebApp;
use App\Services\SsoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SsoTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithSchool(string $role = 'school'): User
    {
        $school = School::factory()->create([
            'name' => 'SMK AL-FATA',
            'npsn' => '69769391',
        ]);

        $defaultApp = WebApp::query()->firstOrCreate(
            ['slug' => 'cbt-core'],
            ['name' => 'CBT Core', 'enabled' => true]
        );
        $school->webApps()->syncWithoutDetaching([$defaultApp->id]);

        return User::factory()->create([
            'role' => $role,
            'school_id' => $school->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_on_sso_redirect(): void
    {
        $response = $this->get('/sso/redirect?app='.urlencode('http://localhost/cbtonline/sso/callback.php'));

        $response->assertRedirect(route('login'));
        $this->assertEquals(
            route('sso.redirect', ['app' => 'http://localhost/cbtonline/sso/callback.php']),
            session('url.intended')
        );
    }

    public function test_authenticated_user_is_redirected_to_cbt_callback_with_token(): void
    {
        $user = $this->createUserWithSchool('school');

        $callback = 'http://localhost/cbtonline/sso/callback.php';
        $response = $this->actingAs($user)
            ->get('/sso/redirect?app='.urlencode($callback));

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');

        $this->assertStringStartsWith($callback.'?token=', $targetUrl);

        parse_str(parse_url($targetUrl, PHP_URL_QUERY), $queryParams);
        $this->assertArrayHasKey('token', $queryParams);

        $ssoService = app(SsoService::class);
        $decoded = $ssoService->decodeToken($queryParams['token']);

        $this->assertNotNull($decoded);
        $this->assertEquals($user->id, $decoded['user']['id']);
        $this->assertEquals($user->name, $decoded['user']['name']);
        $this->assertEquals($user->email, $decoded['user']['email']);
        $this->assertEquals('admin', $decoded['user']['role']);
        $this->assertEquals('SMK AL-FATA', $decoded['user']['school_name']);
        $this->assertEquals('69769391', $decoded['user']['school_code']);
    }

    public function test_sso_launch_endpoint_redirects_to_cbt_callback(): void
    {
        $user = $this->createUserWithSchool('school');

        $app = WebApp::factory()->create([
            'slug' => 'cbtonline',
            'enabled' => true,
        ]);
        $user->school->webApps()->syncWithoutDetaching([$app->id]);

        $response = $this->actingAs($user)
            ->get('/sso/launch/cbtonline');

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');

        $this->assertStringContainsString('/sso/callback.php?token=', $targetUrl);
    }

    public function test_sso_launch_fails_when_app_is_disabled(): void
    {
        $user = $this->createUserWithSchool('school');

        WebApp::factory()->create([
            'slug' => 'cbt-disabled',
            'enabled' => false,
        ]);

        $this->actingAs($user)
            ->get('/sso/launch/cbt-disabled')
            ->assertNotFound();
    }

    public function test_sso_logout_clears_authenticated_session(): void
    {
        $user = $this->createUserWithSchool('school');

        $this->actingAs($user)
            ->get('/sso/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_sso_service_tampered_token_is_rejected(): void
    {
        $user = $this->createUserWithSchool('admin');
        $ssoService = app(SsoService::class);

        $token = $ssoService->generateToken($user);
        $this->assertNotNull($ssoService->decodeToken($token));

        // Modifikasi tanda tangan token (tampering)
        $tampered = $token.'bad';
        $this->assertNull($ssoService->decodeToken($tampered));
    }

    public function test_sso_redirect_rejects_untrusted_external_url(): void
    {
        $user = $this->createUserWithSchool('school');

        $this->actingAs($user)
            ->get('/sso/redirect?app='.urlencode('https://evil-attacker.com/steal-token'))
            ->assertStatus(400);
    }

    public function test_sso_redirect_rejects_untrusted_url_for_guest(): void
    {
        $this->get('/sso/redirect?app='.urlencode('https://evil-attacker.com/steal-token'))
            ->assertStatus(400);
    }

    public function test_sso_redirect_allows_whitelisted_configured_host(): void
    {
        config()->set('sso.allowed_hosts', ['trusted-app.test']);
        $user = $this->createUserWithSchool('school');

        $response = $this->actingAs($user)
            ->get('/sso/redirect?app='.urlencode('https://trusted-app.test/auth/callback'));

        $response->assertRedirect();
        $targetUrl = (string) $response->headers->get('Location');
        $this->assertStringStartsWith('https://trusted-app.test/auth/callback?token=', $targetUrl);
    }

    public function test_sso_service_throws_in_production_environment_with_default_key(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config()->set('sso.secret_key', 'd65585e5da280c4f036b8f1b6e9ff6cfe4c061b59515c379b6842f7023f21ebb');

        $ssoService = new SsoService;

        $this->expectException(\RuntimeException::class);
        $ssoService->getSecretKey();
    }
}
