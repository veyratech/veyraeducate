<?php

namespace Tests\Feature;

use App\Models\Plan;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
    }

    public function test_homepage_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Veyra Educate');
        $response->assertSee('Rp 600.000');
        $response->assertSee('Rp 1.100.000');
        $response->assertSee('Rp 1.500.000');
    }

    public function test_homepage_shows_updated_plan_price(): void
    {
        Plan::query()
            ->where('code', 'semester')
            ->update(['price_idr' => 750000]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Rp 750.000')
            ->assertDontSee('Rp 600.000');
    }

    public function test_marketing_nav_pages_render(): void
    {
        foreach (['/fitur', '/harga', '/demo', '/tentang', '/kontak', '/faq'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_homepage_links_to_core_routes(): void
    {
        $response = $this->get('/');

        $response->assertSee(route('login'), false);
        $response->assertSee(route('register'), false);
    }
}
