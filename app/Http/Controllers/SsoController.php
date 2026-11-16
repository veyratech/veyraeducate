<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\WebApp;
use App\Services\SsoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function __construct(
        private readonly SsoService $ssoService
    ) {}

    /**
     * Endpoint SSO Redirect: dipanggil oleh aplikasi eksternal (CBT)
     * Format: /sso/redirect?app={callback_url}
     */
    public function redirect(Request $request): RedirectResponse
    {
        $targetApp = $request->query('app');

        // Validasi keamanan parameter app untuk mencegah Open Redirect & eksfiltrasi token SSO
        if ($targetApp !== null && $targetApp !== '') {
            if (! $this->ssoService->isValidRedirectUrl($targetApp)) {
                abort(400, 'URL callback SSO tidak diizinkan atau tidak terdaftar.');
            }
        }

        // Jika user belum login di Veyra Educate, simpan intended route lalu arahkan ke login
        if (! Auth::check()) {
            $intended = route('sso.redirect', array_filter(['app' => $targetApp]));
            session()->put('url.intended', $intended);

            return redirect()->route('login');
        }

        $user = Auth::user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->first();

        if (! $user->isAdmin()) {
            if (! $school || (! $school->hasActiveSubscription() && ! $school->webApps()->exists())) {
                return redirect()->route('dashboard')
                    ->with('status', 'Silakan aktifkan paket langganan terlebih dahulu untuk menggunakan akses Single Sign-On (SSO).');
            }
        }

        if ($school) {
            $school->forceFill(['last_app_access_at' => now()])->save();
        }

        $token = $this->ssoService->generateToken($user, $this->resolveAppSlug($targetApp));

        $callbackUrl = $targetApp ?: ($this->ssoService->getCbtUrl().'/sso/callback.php');
        $separator = str_contains($callbackUrl, '?') ? '&' : '?';
        $destination = $callbackUrl.$separator.'token='.urlencode($token);

        return redirect()->away($destination);
    }

    /**
     * Launcher langsung dari portal Veyra Educate (Dashboard / App Hub) ke CBT.
     * Route: /sso/launch/{slug?}
     */
    public function launch(Request $request, string $slug = 'cbtonline'): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Cek jika aplikasi terdaftar di WebApp
        $webApp = WebApp::query()->where('slug', $slug)->first();
        if ($webApp && ! $webApp->enabled) {
            abort(404, 'Aplikasi ini sedang dinonaktifkan.');
        }

        // Pastikan sekolah memiliki langganan aktif atau hak akses modul (kecuali Super Admin)
        $school = $user->school ?? School::where('owner_user_id', $user->id)->first();

        if (! $user->isAdmin()) {
            $hasAccess = $school && (
                ($webApp && $school->hasAccessToApp($slug))
                || $school->hasActiveSubscription()
            );

            if (! $hasAccess) {
                abort(403, 'Sekolah Anda memerlukan paket langganan aktif untuk mengakses aplikasi ini.');
            }
        }

        if ($school) {
            $school->forceFill(['last_app_access_at' => now()])->save();
        }

        $token = $this->ssoService->generateToken($user, $slug);

        // Jika WebApp memiliki target access_url khusus (aplikasi eksternal atau E-Rapor)
        if ($webApp && ! empty($webApp->access_url)) {
            $targetUrl = rtrim($webApp->access_url, '/');
            if (in_array(request()->getHost(), ['localhost', '127.0.0.1']) && str_contains($targetUrl, 'raport.veyracreative.site')) {
                $targetUrl = 'http://localhost/vraport/public';
            }
            // Hanya tambahkan callback untuk aplikasi SSO-native (CBT, raport, dll).
            // Untuk aplikasi eksternal murni, lewati URL apa adanya.
            if (
                str_contains($targetUrl, 'sso/callback')
                || str_contains($targetUrl, '/sso')
                || str_contains($targetUrl, 'sso_callback')
                || str_contains($targetUrl, 'cbt')
            ) {
                $sep = str_contains($targetUrl, '?') ? '&' : '?';
                $destination = $targetUrl.$sep.'token='.urlencode($token);

                return redirect()->away($destination);
            }

            // Aplikasi eksternal tanpa callback SSO: arahkan langsung
            return redirect()->away($targetUrl);
        }

        $cbtBase = $this->ssoService->getCbtUrl();
        $destination = $cbtBase.'/sso/callback.php?token='.urlencode($token);

        return redirect()->away($destination);
    }

    /**
     * Logout SSO terpusat.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Resolve app slug from a callback URL (used by sso/redirect endpoint).
     */
    private function resolveAppSlug(?string $url): string
    {
        if ($url === null || trim($url) === '') {
            return 'cbtonline';
        }

        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null) {
            return 'cbtonline';
        }

        $allowedHosts = $this->ssoService->getAllowedHosts();
        foreach ($allowedHosts as $allowed) {
            if (strtolower($host) === strtolower($allowed)) {
                return 'cbtonline';
            }
        }

        return 'cbtonline';
    }
}
