<?php

use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $paths = [
        '',
        '/fitur',
        '/harga',
        '/demo',
        '/brosur',
        '/proposal',
        '/tentang',
        '/kontak',
        '/faq',
        '/login',
        '/register',
    ];

    $urls = collect($paths)
        ->map(fn (string $path) => [
            'loc' => url($path),
            'lastmod' => now()->toIso8601String(),
        ])
        ->map(fn (array $row) => implode('', [
            '<url>',
            '<loc>', e($row['loc']), '</loc>',
            '<lastmod>', $row['lastmod'], '</lastmod>',
            '</url>',
        ]))
        ->implode('');

    return response(
        '<?xml version="1.0" encoding="UTF-8"?>'
        .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        .$urls
        .'</urlset>',
        200,
        ['Content-Type' => 'application/xml; charset=UTF-8'],
    );
})->name('sitemap');

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nDisallow: /dashboard\nDisallow: /profile\nDisallow: /checkout\nDisallow: /pembayaran\nDisallow: /webhooks/\nSitemap: ".url('/sitemap.xml')."\n",
        200,
        ['Content-Type' => 'text/plain'],
    );
})->name('robots');
