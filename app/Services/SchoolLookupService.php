<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Resolves school data (name, city, level, status) from external APIs by NPSN or NSM.
 *
 * Source order (configurable in config/school_lookup.php):
 *   1. Fazrian Dapodik proxy  → nationwide coverage (Jatim, Sumenep, dll.)
 *   2. Vercel community API   → Jakarta-only fallback
 *   3. Kemendikdasmen ref     → official page scraper (often down)
 *   4. EMIS Kemenag           → madrasah (NSM) lookup
 */
class SchoolLookupService
{
    /**
     * Resolve a code (NPSN 8-digit or NSM 9-13 digit).
     */
    public function lookup(string $code): array
    {
        $length = strlen($code);

        if ($length === 8) {
            return $this->lookupByNpsn($code);
        }

        if ($length >= 9 && $length <= 13) {
            $nsmResult = $this->lookupByNsm($code);

            if ($nsmResult !== null) {
                return $nsmResult;
            }

            // NSM range: fall back to NPSN lookup (some NSM values overlap with NPSN)
            Log::info("NSM not found, retrying as NPSN: {$code}");

            return $this->lookupByNpsn($code);
        }

        return [
            'found' => false,
            'message' => 'Masukkan NPSN (8 digit) atau NSM (9–13 digit).',
        ];
    }

    /**
     * Try each configured NPSN source in order.
     */
    private function lookupByNpsn(string $npsn): array
    {
        foreach (config('school_lookup.npsn_sources', []) as $source) {
            try {
                match ($source['type'] ?? '') {
                    'dapodik_fazrian' => $result = $this->queryFazrianNpsn($npsn, $source),
                    'dapodik_vercel' => $result = $this->queryVercelNpsn($npsn, $source),
                    'kemendikdasmen_ref' => $result = $this->queryKemendikdasmenRef($npsn, $source),
                    default => $result = null,
                };

                if ($result !== null) {
                    return $result;
                }
            } catch (\Throwable $e) {
                Log::warning("School lookup source [{$source['type']}] failed for NPSN {$npsn}: ".$e->getMessage());
            }
        }

        Log::info("All NPSN sources exhausted for {$npsn}");

        return [
            'found' => false,
            'message' => "NPSN {$npsn} tidak ditemukan di database publik mana pun. Silakan verifikasi angka atau isi nama sekolah secara manual.",
        ];
    }

    /**
     * Fazrian Dapodik API — wraps api.fazriansyah.eu.org which proxies belajar.id (Dapodik resmi).
     * Response shape: {"data": {"satuanPendidikan": {...}, "meta": {...}}}
     */
    private function queryFazrianNpsn(string $npsn, array $source): ?array
    {
        $res = Http::timeout($source['timeout'] ?? 8)
            ->retry(1, 200, throw: false)
            ->get($source['url'], ['npsn' => $npsn]);

        if (! $res->successful()) {
            return null;
        }

        $json = $res->json();

        // Expected: { data: { satuanPendidikan: { npsn, nama, ... } } }
        $sp = $json['data']['satuanPendidikan'] ?? null;

        if (! is_array($sp) || empty($sp['npsn'])) {
            return null;
        }

        if ((string) $sp['npsn'] !== $npsn) {
            return null;
        }

        $kabupaten = $sp['namaKabupaten'] ?? $sp['kabupaten_kota'] ?? null;
        $provinsi = $sp['namaProvinsi'] ?? $sp['propinsi'] ?? null;
        $city = trim(($kabupaten ?: '').($kabupaten && $provinsi ? ', ' : '').($provinsi ?? ''));

        $upper = strtoupper($sp['nama'] ?? '');
        $isMadrasah = str_contains($upper, 'MADRASAH')
            || preg_match('/\b(MI|MTS|MA|RA|RD|PD|MAK)\b/', $upper);

        return [
            'found' => true,
            'source' => 'dapodik',
            'npsn' => (string) $sp['npsn'],
            'name' => trim((string) ($sp['nama'] ?? '')),
            'city' => $city ?: null,
            'district' => $sp['namaKecamatan'] ?? null,
            'address' => $sp['alamatJalan'] ?? null,
            'level' => $sp['bentuk'] ?? null,
            'status' => $this->mapStatus($sp['statusSatuanPendidikan'] ?? ''),
            'lembaga' => $isMadrasah ? 'Kemenag' : 'Kemendikbud (Diknas)',
        ];
    }

    /**
     * Vercel community API — Jakarta-only coverage.
     * Response shape: { dataSekolah: [{ npsn, sekolah, kabupaten_kota, propinsi, ... }] }
     */
    private function queryVercelNpsn(string $npsn, array $source): ?array
    {
        $res = Http::timeout($source['timeout'] ?? 8)
            ->retry(1, 200, throw: false)
            ->get($source['url'], ['npsn' => $npsn, 'perPage' => 1]);

        if (! $res->successful()) {
            return null;
        }

        $schools = $res->json('dataSekolah');

        if (! is_array($schools) || empty($schools[0])) {
            return null;
        }

        $s = $schools[0];

        if ((string) ($s['npsn'] ?? '') !== $npsn) {
            return null;
        }

        $city = trim(($s['kabupaten_kota'] ?? '').($s['kabupaten_kota'] && $s['propinsi'] ? ', ' : '').($s['propinsi'] ?? ''));

        $upper = strtoupper($s['sekolah'] ?? '');
        $isMadrasah = str_contains($upper, 'MADRASAH')
            || in_array(strtoupper($s['bentuk'] ?? ''), ['MI', 'MTS', 'MA', 'RA', 'RD', 'PD', 'MADRASAH'], true);

        return [
            'found' => true,
            'source' => 'dapodik',
            'npsn' => (string) $s['npsn'],
            'name' => trim((string) ($s['sekolah'] ?? '')),
            'city' => $city ?: null,
            'district' => $s['kecamatan'] ?? null,
            'address' => trim((string) ($s['alamat_jalan'] ?? '')) ?: null,
            'level' => $s['bentuk'] ?? null,
            'status' => $this->mapStatus($s['status'] ?? ''),
            'lembaga' => $isMadrasah ? 'Kemenag' : 'Kemendikbud (Diknas)',
        ];
    }

    /**
     * Kemendikdasmen reference HTML page — screen-scrapes <td> label/value pairs.
     */
    private function queryKemendikdasmenRef(string $npsn, array $source): ?array
    {
        $url = str_replace(':npsn', urlencode($npsn), $source['url']);

        $res = Http::timeout($source['timeout'] ?? 8)
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
            ->retry(1, 200, throw: false)
            ->get($url);

        if (! $res->successful() || $res->status() === 404) {
            return null;
        }

        $html = $res->body();
        $fields = [];

        if (preg_match_all('/<td>(?:\s|&nbsp;)*<\/td>\s*<td>(.*?)<\/td>\s*<td>:<\/td>\s*<td>(.*?)<\/td>/s', $html, $m, PREG_SET_ORDER)) {
            foreach ($m as $row) {
                $label = trim(strip_tags(html_entity_decode($row[1])));
                $value = trim(strip_tags(html_entity_decode($row[2])));
                $fields[$label] = $value;
            }
        }

        if (empty($fields['Nama']) || ($fields['NPSN'] ?? '') !== $npsn) {
            return null;
        }

        $statusRaw = strtoupper($fields['Status Sekolah'] ?? '');
        $kabupaten = $fields['Kab.-Kota/Negara (LN)'] ?? null;
        $provinsi = $fields['Propinsi/Luar Negeri (LN)'] ?? null;
        $city = trim(($kabupaten ?: '').($kabupaten && $provinsi ? ', ' : '').($provinsi ?? ''));

        $kementerian = strtolower($fields['Kementerian Pembina'] ?? '');
        $bentuk = $fields['Bentuk Pendidikan'] ?? null;

        return [
            'found' => true,
            'source' => 'dapodik',
            'npsn' => $npsn,
            'name' => $fields['Nama'],
            'city' => $city ?: null,
            'district' => $fields['Kecamatan/Kota (LN)'] ?? null,
            'address' => $fields['Alamat'] ?? null,
            'level' => $bentuk,
            'status' => str_contains($statusRaw, 'SWASTA') ? 'Swasta' : (str_contains($statusRaw, 'NEGERI') ? 'Negeri' : null),
            'lembaga' => str_contains($kementerian, 'agama') ? 'Kemenag' : 'Kemendikbud (Diknas)',
        ];
    }

    /**
     * Lookup by NSM via EMIS Kemenag HTML parser.
     */
    private function lookupByNsm(string $nsm): ?array
    {
        foreach (config('school_lookup.nsm_sources', []) as $source) {
            try {
                $res = Http::timeout($source['timeout'] ?? 8)
                    ->retry(1, 200, throw: false)
                    ->get($source['url'], ['nsm' => $nsm]);

                if (! $res->successful()) {
                    continue;
                }

                $html = $res->body();
                $fields = [];

                if (preg_match_all('/<td class="kemenag-label">(.*?)<\/td>\s*<td class="kemenag-value">(.*?)<\/td>/s', $html, $m, PREG_SET_ORDER)) {
                    foreach ($m as $row) {
                        $label = trim(strip_tags(html_entity_decode($row[1])));
                        $value = trim(strip_tags(html_entity_decode($row[2])));
                        $fields[$label] = $value;
                    }
                }

                $name = $fields['Madrasah'] ?? null;

                if (! $name || ($fields['NSM'] ?? '') !== $nsm) {
                    continue;
                }

                $upper = strtoupper($name);
                $level = null;
                foreach (['MTS', 'MAS', 'MA', 'MIS', 'MI', 'RA', 'MAK'] as $candidate) {
                    if (str_contains($upper, $candidate)) {
                        $level = $candidate;
                        break;
                    }
                }

                $kabupaten = $fields['Kab/Kota'] ?? null;
                $provinsi = $fields['Provinsi'] ?? null;
                $city = trim(($kabupaten ?: '').($kabupaten && $provinsi ? ', ' : '').($provinsi ?? ''));

                return [
                    'found' => true,
                    'source' => 'emis',
                    'nsm' => $nsm,
                    'npsn' => $fields['NPSN'] ?? '',
                    'name' => $name,
                    'city' => $city ?: null,
                    'district' => null,
                    'address' => $fields['Alamat'] ?? null,
                    'level' => $level,
                    'status' => $fields['Status'] ?? null,
                    'lembaga' => 'Kemenag',
                ];
            } catch (\Throwable $e) {
                Log::warning("EMIS lookup failed for NSM {$nsm}: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Normalise raw status string into readable status.
     */
    private function mapStatus(string $raw): ?string
    {
        $upper = strtoupper(trim($raw));

        if ($upper === 'N' || str_contains($upper, 'NEGERI')) {
            return 'Negeri';
        }

        if ($upper === 'S' || str_contains($upper, 'SWASTA')) {
            return 'Swasta';
        }

        return $upper ?: null;
    }
}
