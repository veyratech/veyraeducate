<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PaymentOrder;
use App\Models\Subscription;
use App\Support\WhatsAppConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan teks WhatsApp ke nomor tujuan.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if ($normalizedPhone === '') {
            return false;
        }

        $config = WhatsAppConfig::get();

        // Jika gateway dinonaktifkan atau token belum diisi, catat ke log (aman & tidak memutus proses)
        if (! $config['enabled'] || empty($config['api_token']) || $config['gateway'] === 'log') {
            Log::info("WhatsApp Simulated [{$normalizedPhone}]:\n{$message}");

            return true;
        }

        try {
            if ($config['gateway'] === 'wablas') {
                $response = Http::timeout(10)
                    ->withHeaders(['Authorization' => $config['api_token']])
                    ->post('https://api.wablas.com/api/send-message', [
                        'phone' => $normalizedPhone,
                        'message' => $message,
                    ]);

                return $response->successful();
            }

            // Default: Fonnte API
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $config['api_token']])
                ->post('https://api.fonnte.com/send', [
                    'target' => $normalizedPhone,
                    'message' => $message,
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim WhatsApp ke {$normalizedPhone}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Kirim notifikasi bukti pelunasan dan kwitansi resmi SPJ BOS.
     */
    public function sendPaymentSuccess(PaymentOrder $order): bool
    {
        $school = $order->school;
        $user = $order->user;
        $targetPhone = $school?->phone ?: ($user?->phone ?: '');

        if ($targetPhone === '') {
            return false;
        }

        $schoolName = $school?->name ?? 'Sekolah';
        $planName = $order->plan?->label ?? 'Paket Sekolah';
        $amount = 'Rp '.number_format((float) $order->amount_idr, 0, ',', '.');
        $invoiceUrl = route('pembayaran.invoice', $order->order_id);

        $text = "🎓 *PEMBAYARAN TERVERIFIKASI — VEYRA EDUCATE*\n\n"
            ."Yth. Pengelola *{$schoolName}*,\n\n"
            ."Pembayaran perpanjangan langganan Anda telah berhasil diverifikasi oleh sistem:\n"
            ."• *Nomor Order:* `{$order->order_id}`\n"
            ."• *Paket Layanan:* {$planName}\n"
            ."• *Total Biaya:* {$amount}\n"
            ."• *Status:* LUNAS (Aktif Seketika)\n\n"
            ."📄 *Kwitansi & Faktur Resmi SPJ BOS* berstempel digital dapat Anda unduh kapan saja melalui tautan resmi:\n"
            ."{$invoiceUrl}\n\n"
            ."Terima kasih atas kepercayaan {$schoolName} bermitra bersama Veyra Educate.\n\n"
            .'_Pesan otomatis dikirim oleh Veyra Educate System._';

        return $this->sendMessage($targetPhone, $text);
    }

    /**
     * Kirim pengingat masa aktif langganan sekolah yang akan berakhir.
     */
    public function sendExpiryReminder(Subscription $subscription, int $daysLeft): bool
    {
        $school = $subscription->school;
        $owner = $school?->owner;
        $targetPhone = $school?->phone ?: ($owner?->phone ?: '');

        if ($targetPhone === '') {
            return false;
        }

        $schoolName = $school?->name ?? 'Sekolah';
        $planName = $subscription->plan?->label ?? 'Paket Sekolah';
        $expiryDate = $subscription->ends_at?->locale('id')->isoFormat('dddd, D MMMM Y') ?? 'segera';
        $renewalUrl = url('/dashboard');

        $text = "⏳ *PENGINGAT MASA AKTIF LANGGANAN — VEYRA EDUCATE*\n\n"
            ."Yth. Bapak/Ibu Kepala Sekolah & Pengelola *{$schoolName}*,\n\n"
            ."Kami menginformasikan bahwa masa aktif lisensi Veyra Educate & CBT Online untuk sekolah Anda akan berakhir dalam *{$daysLeft} hari lagi*:\n"
            ."• *Paket Saat Ini:* {$planName}\n"
            ."• *Jatuh Tempo:* {$expiryDate}\n\n"
            ."Agar pelaksanaan ujian dan akses modul aplikasi sekolah tidak terputus, silakan lakukan perpanjangan melalui tautan dashboard sekolah:\n"
            ."{$renewalUrl}\n\n"
            ."Jika membutuhkan faktur penawaran resmi untuk pengajuan pencairan Dana BOS, silakan hubungi tim kami.\n\n"
            ."Salam hangat,\n*Tim Layanan Veyra Educate*";

        return $this->sendMessage($targetPhone, $text);
    }

    /**
     * Kirim pesan tes koneksi gateway WhatsApp.
     */
    public function sendTestMessage(string $phone): bool
    {
        $text = "✅ *KONEKSI WHATSAPP GATEWAY BERHASIL!*\n\n"
            ."Pesan ini merupakan uji coba resmi dari portal *Veyra Educate*.\n"
            ."Gateway WhatsApp Anda telah terkonfigurasi dengan benar dan siap mengirimkan kwitansi serta notifikasi otomatis ke sekolah-sekolah mitra.\n\n"
            .'_Waktu uji coba: '.now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm').' WIB_';

        return $this->sendMessage($phone, $text);
    }

    /**
     * Normalisasi nomor telepon standar Indonesia ke format internasional (628xxx).
     */
    public function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($cleaned, '08')) {
            return '62'.substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '8')) {
            return '62'.$cleaned;
        }

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        return $cleaned;
    }
}
