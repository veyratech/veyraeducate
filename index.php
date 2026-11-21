<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Possible relative base prefixes for finding Laravel core:
$prefixes = [
    '',                                  // Satu folder bersama (jika seluruh project diekstrak di public_html)
    '../',                               // Jika core Laravel ada di root user (/home/username/)
    '../portal.veyracreative.site/',     // Jika di /home/username/portal.veyracreative.site
    '../portal/',                        // Jika di /home/username/portal
    '../veyraeducate/',                  // Jika di /home/username/veyraeducate
    '../veyracreative/',                 // Jika di /home/username/veyracreative
    '../veyracreative.site/',            // Jika di /home/username/veyracreative.site
    '../veyraeducate-deploy/',           // Jika ekstrak default zip
    '../veyra/',                         // Jika di /home/username/veyra
    '../laravel/',                       // Jika di /home/username/laravel
    '../VEYRA/',                         // Jika di /home/username/VEYRA
    '../../backend/',                    // Local workspace dev
    '../../',
];

// Determine if the application is in maintenance mode...
foreach ($prefixes as $pfx) {
    if (file_exists($maintenance = __DIR__.'/'.$pfx.'storage/framework/maintenance.php')) {
        require $maintenance;
        break;
    }
}

// Register the Composer autoloader...
$autoloadFile = null;
foreach ($prefixes as $pfx) {
    if (file_exists($candidate = __DIR__.'/'.$pfx.'vendor/autoload.php')) {
        $autoloadFile = $candidate;
        break;
    }
}

if (! $autoloadFile) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "<div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;max-width:680px;margin:40px auto;padding:24px;border:1px solid #fecaca;border-radius:12px;background:#fef2f2;color:#991b1b;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);'>";
    echo "<h2 style='margin-top:0;font-size:20px;display:flex;align-items:center;gap:8px;'>⚠️ Autoload Error</h2>";
    echo "<p style='font-size:15px;line-height:1.5;'>Tidak dapat menemukan file <code>vendor/autoload.php</code>.</p>";
    echo "<div style='background:#ffffff;border:1px solid #fee2e2;padding:12px 16px;border-radius:8px;font-size:13px;color:#374151;margin:16px 0;'>";
    echo "<strong>Lokasi index.php saat ini:</strong><br><code style='color:#dc2626;'>".htmlspecialchars(__DIR__).'</code><br><br>';
    echo '<strong>Folder vendor yang dicari:</strong><br>';
    echo "<ul style='margin:4px 0 0 16px;padding:0;'>";
    foreach ($prefixes as $pfx) {
        echo '<li><code>'.htmlspecialchars(realpath(__DIR__.'/'.$pfx) ?: (__DIR__.'/'.$pfx)).'/vendor/autoload.php</code></li>';
    }
    echo '</ul>';
    echo '</div>';
    echo "<p style='font-weight:600;margin-bottom:8px;'>Solusi Cepat di cPanel File Manager:</p>";
    echo "<ol style='margin:0 0 16px 20px;padding:0;line-height:1.6;font-size:14px;'>";
    echo '<li>Buka <strong>File Manager</strong> di cPanel.</li>';
    echo '<li>Periksa di folder mana Anda mengekstrak file ZIP (apakah di <code>/home/username/veyraeducate</code> atau di tempat lain).</li>';
    echo '<li>Pastikan folder <strong><code>vendor</code></strong> ada di dalam folder tersebut. Jika belum ada, ekstrak kembali file ZIP <code>veyraeducate-deploy.zip</code>.</li>';
    echo '</ol>';
    echo '</div>';
    exit(1);
}

require $autoloadFile;

// Bootstrap Laravel and handle the request...
$bootstrapFile = null;
foreach ($prefixes as $pfx) {
    if (file_exists($candidate = __DIR__.'/'.$pfx.'bootstrap/app.php')) {
        $bootstrapFile = $candidate;
        break;
    }
}

if (! $bootstrapFile) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Bootstrap error: Tidak dapat menemukan bootstrap/app.php.');
}

/** @var Application $app */
$app = require_once $bootstrapFile;

if (method_exists($app, 'usePublicPath')) {
    $app->usePublicPath(__DIR__.'/public');
}

if (! file_exists($app->environmentPath().'/.env') && file_exists(__DIR__.'/.env')) {
    $app->useEnvironmentPath(__DIR__);
}

$app->handleRequest(Request::capture());
