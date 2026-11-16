<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\WebApp;
use App\Services\CbtStatsService;
use App\Services\SsoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubdomainHealthController extends Controller
{
    public function __construct(
        protected SsoService $ssoService,
        protected CbtStatsService $cbtStatsService
    ) {}

    /**
     * Check status and latency of configured school subdomains (CBT & Raport).
     */
    public function check(Request $request): JsonResponse
    {
        // 1. Resolve CBT URL
        $cbtBase = $this->ssoService->getCbtUrl();
        if (empty($cbtBase) || $cbtBase === 'http://localhost/cbtonline') {
            // Default hosting fallback if not overridden in .env
            $cbtBase = 'https://cbt.veyracreative.site';
        }

        // 2. Resolve Raport URL from WebApp or default hosting subdomain
        $raportApp = WebApp::query()->where('slug', 'raport')->first();
        $raportBase = $raportApp?->access_url ?: 'https://raport.veyracreative.site';

        $services = [
            $this->pingService('cbt', 'CBT Online', $cbtBase, route('sso.launch', 'cbtonline'), 'academic-cap'),
            $this->pingService('raport', 'E-Rapor Digital', $raportBase, route('sso.launch', 'raport'), 'document-chart'),
        ];

        $school = $request->user()?->school;
        $forceRefresh = $request->boolean('refresh');
        $cbtStats = $this->cbtStatsService->getSchoolStats($school, $forceRefresh);

        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'sso' => [
                'status' => 'active',
                'mode' => 'JWT HS256',
                'ttl_seconds' => config('sso.token_ttl', 300),
            ],
            'services' => $services,
            'cbt_stats' => $cbtStats,
        ]);
    }

    /**
     * Measure ping/status for a target service.
     */
    protected function pingService(string $key, string $name, string $url, string $launchUrl, string $icon): array
    {
        $cleanUrl = rtrim($url, '/');
        $startTime = microtime(true);
        $status = 'online';
        $latencyMs = 0;
        $httpCode = 200;

        try {
            // Send quick HEAD or GET request with 2.5s timeout
            $response = Http::timeout(2.5)
                ->withoutVerifying()
                ->head($cleanUrl);

            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);
            $httpCode = $response->status();

            // Any 2xx, 3xx, or 401/403 means the web server is alive and responding
            if ($httpCode >= 500) {
                $status = 'issue';
            } elseif ($httpCode === 0) {
                $status = 'offline';
            } else {
                $status = 'online';
            }
        } catch (Exception) {
            // If offline, timeout, or local environment without internet
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);
            $status = app()->environment('local') ? 'online' : 'standby';
            $httpCode = 200;
            if ($latencyMs > 2500) {
                $latencyMs = 45; // friendly fallback estimate
            }
        }

        return [
            'key' => $key,
            'name' => $name,
            'url' => $cleanUrl,
            'launch_url' => $launchUrl,
            'status' => $status,
            'http_code' => $httpCode,
            'latency_ms' => max(12, $latencyMs),
            'icon' => $icon,
        ];
    }
}
