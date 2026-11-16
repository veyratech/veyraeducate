<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\WebApp;
use Illuminate\Support\Str;
use RuntimeException;

class SsoService
{
    public function __construct(
        private readonly ?string $secretKey = null,
        private readonly int $tokenTtl = 300
    ) {}

    /**
     * Dapatkan secret key SSO aktif.
     */
    public function getSecretKey(): string
    {
        $key = $this->secretKey ?: (string) config('sso.secret_key');

        if ($key === '') {
            if (app()->environment('production')) {
                throw new RuntimeException('SSO_SECRET_KEY wajib dikonfigurasi di lingkungan produksi.');
            }

            return 'd65585e5da280c4f036b8f1b6e9ff6cfe4c061b59515c379b6842f7023f21ebb';
        }

        if (app()->environment('production') && $key === 'd65585e5da280c4f036b8f1b6e9ff6cfe4c061b59515c379b6842f7023f21ebb') {
            throw new RuntimeException('SSO_SECRET_KEY tidak boleh menggunakan kunci contoh bawaan di lingkungan produksi.');
        }

        return $key;
    }

    /**
     * Dapatkan URL server CBT tujuan.
     */
    public function getCbtUrl(): string
    {
        return rtrim((string) config('sso.cbt_url', 'http://localhost/cbtonline'), '/');
    }

    /**
     * Generate signed JWT token untuk pengguna Veyra Educate.
     */
    public function generateToken(User $user, string $slug = 'cbtonline'): string
    {
        $school = $user->school ?? School::where('owner_user_id', $user->id)->first();

        // Normalisasi peran:
        // Admin Veyra -> Superadmin CBT (bisa akses superadmin)
        // User Sekolah -> Admin Tenant/Lembaga di CBT
        // Guru -> Teacher di CBT
        // Siswa -> Student di CBT
        $role = match ($user->role) {
            User::ROLE_ADMIN => 'superadmin',
            User::ROLE_SCHOOL => 'admin',
            'teacher', 'guru' => 'teacher',
            'student', 'siswa' => 'student',
            default => 'admin',
        };

        $tenantName = $school?->name ?? ($user->name ? 'Sekolah '.$user->name : 'Sekolah');
        $tenantCode = $school?->npsn ?: ('SCH'.($school?->id ?? $user->id));
        $tenantSlug = Str::slug($school?->name ?? ('sekolah-'.$user->id));

        $activeSubscription = $school?->activeSubscription;
        if ($school && $school->hasActiveSubscription()) {
            $planCode = $school->activeSubscription?->plan?->code ?? 'semester';
            $planLabel = $school->activeSubscription?->plan?->label ?? 'Paket Semesteran';
        } else {
            $planCode = 'semester';
            $planLabel = 'Paket Semesteran';
        }

        $now = time();
        $payload = [
            'iss' => (string) config('app.url'),
            'sub' => (string) $user->id,
            'aud' => $slug,
            'iat' => $now,
            'exp' => $now + $this->tokenTtl,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'username' => explode('@', $user->email)[0],
                'nis' => explode('@', $user->email)[0],
                'gender' => 'L',
                'school_id' => $school?->id ?? $user->id,
                'school_name' => $tenantName,
                'school_code' => $tenantCode,
                'school_slug' => $tenantSlug,
                'tenant_id' => $school?->id ?? $user->id,
                'tenant_name' => $tenantName,
                'tenant_code' => $tenantCode,
                'tenant_slug' => $tenantSlug,
                'school_logo' => $school?->logo_path ? url('storage/'.$school->logo_path) : null,
                'logo_url' => $school?->logo_path ? url('storage/'.$school->logo_path) : null,
                'package' => $planCode,
                'plan_code' => $planCode,
                'plan_label' => $planLabel,
            ],
        ];

        return $this->encodeJwt($payload);
    }

    /**
     * Encode payload array menjadi JWT HS256 yang valid standar RFC 7519.
     */
    public function encodeJwt(array $payload): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];

        $headerEncoded = $this->base64UrlEncode((string) json_encode($header, JSON_UNESCAPED_SLASHES));
        $payloadEncoded = $this->base64UrlEncode((string) json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signingInput = "{$headerEncoded}.{$payloadEncoded}";
        $signature = hash_hmac('sha256', $signingInput, $this->getSecretKey(), true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return "{$signingInput}.{$signatureEncoded}";
    }

    /**
     * Validasi dan decode token JWT.
     */
    public function decodeToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $sigB64] = $parts;

        $sig = $this->base64UrlDecode($sigB64);
        $expectedSig = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->getSecretKey(), true);

        if (! hash_equals($expectedSig, $sig)) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($payloadB64);
        $payload = json_decode($payloadJson, true);

        if (! is_array($payload)) {
            return null;
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Periksa apakah URL redirect/callback SSO aman dan diizinkan.
     */
    public function isValidRedirectUrl(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return false;
        }

        if (str_contains($url, "\0")) {
            return false;
        }

        $parsed = parse_url($url);
        if ($parsed === false) {
            return false;
        }

        // Jika URL relatif (misalnya /sso/callback.php)
        if (! isset($parsed['host'])) {
            return str_starts_with($url, '/') && ! str_starts_with($url, '//') && ! str_starts_with($url, '/\\');
        }

        // Jika URL absolut, pastikan skema hanya http atau https
        $scheme = strtolower($parsed['scheme'] ?? '');
        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parsed['host']);
        $allowedHosts = $this->getAllowedHosts();

        return in_array($host, $allowedHosts, true);
    }

    /**
     * Dapatkan daftar host yang diizinkan untuk target redirect SSO.
     *
     * @return array<int, string>
     */
    public function getAllowedHosts(): array
    {
        $hosts = [];

        // 1. Host dari CBT URL
        $cbtHost = parse_url($this->getCbtUrl(), PHP_URL_HOST);
        if ($cbtHost) {
            $hosts[] = strtolower($cbtHost);
        }

        // 2. Host dari App URL
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if ($appHost) {
            $hosts[] = strtolower($appHost);
        }

        // 3. Host dari konfigurasi sso.allowed_hosts
        $configured = config('sso.allowed_hosts', []);
        if (is_array($configured)) {
            foreach ($configured as $h) {
                if (is_string($h) && trim($h) !== '') {
                    $hosts[] = strtolower(trim($h));
                }
            }
        }

        // 4. Host dari WebApp terdaftar yang memiliki access_url
        $webAppUrls = WebApp::query()->whereNotNull('access_url')->pluck('access_url');
        foreach ($webAppUrls as $appUrl) {
            $wHost = parse_url((string) $appUrl, PHP_URL_HOST);
            if ($wHost) {
                $hosts[] = strtolower($wHost);
            }
        }

        // 5. Izinkan localhost & 127.0.0.1 saat local/testing
        if (app()->environment('local', 'testing')) {
            $hosts[] = 'localhost';
            $hosts[] = '127.0.0.1';
        }

        return array_values(array_unique($hosts));
    }

    /**
     * Base64URL encode tanpa padding sesuai RFC 7515.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64URL decode dengan padding toleran.
     */
    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode(strtr($data, '-_', '+/'));
    }
}
