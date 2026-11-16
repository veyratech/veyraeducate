<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\File;

class InvoiceConfig
{
    public static function getPath(): string
    {
        return storage_path('app/settings/invoice.json');
    }

    /**
     * @return array<string, string>
     */
    public static function get(): array
    {
        $path = self::getPath();
        $defaults = [
            'company_name' => (string) config('invoice.company_name', 'PT. VEYRA CREATIVE TECH'),
            'company_sub' => (string) config('invoice.company_sub', 'Penyedia Layanan Sistem Informasi & Wadah Aplikasi Sekolah Terpadu (Veyra Educate)'),
            'company_address' => (string) config('invoice.company_address', 'Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep KodePos: 69461 • Telp/WA: +62 857-0707-4778 • Email: veyracreativetech@gmail.com'),
            'signer_name' => (string) config('invoice.signer_name', 'Ahmad Fauzi, S.Kom., M.T.'),
            'signer_title' => (string) config('invoice.signer_title', 'Finance & Licensing Manager'),
            'signer_department' => (string) config('invoice.signer_department', 'Bagian Keuangan & Penagihan'),
            'signer_city' => (string) config('invoice.signer_city', 'Sumenep'),
        ];

        if (! is_file($path)) {
            return $defaults;
        }

        $saved = json_decode((string) File::get($path), true) ?: [];

        return array_merge($defaults, array_filter($saved, fn ($v) => $v !== null && $v !== ''));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $path = self::getPath();
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        self::apply();
    }

    public static function apply(): void
    {
        $settings = self::get();
        config([
            'invoice.company_name' => $settings['company_name'],
            'invoice.company_sub' => $settings['company_sub'],
            'invoice.company_address' => $settings['company_address'],
            'invoice.signer_name' => $settings['signer_name'],
            'invoice.signer_title' => $settings['signer_title'],
            'invoice.signer_department' => $settings['signer_department'],
            'invoice.signer_city' => $settings['signer_city'],
        ]);
    }
}
