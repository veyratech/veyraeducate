<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoRoutesTest extends TestCase
{
    public function test_sitemap_returns_xml_with_marketing_paths(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('/fitur', false);
        $response->assertSee('/harga', false);
        $response->assertSee(url('/'), false);
    }

    public function test_robots_txt_allows_marketing_and_blocks_private_paths(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertSee('Allow: /', false);
        $response->assertSee('Disallow: /dashboard', false);
        $response->assertSee('Sitemap: '.url('/sitemap.xml'), false);
    }
}
