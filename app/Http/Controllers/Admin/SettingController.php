<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use App\Support\ContactConfig;
use App\Support\HeroImage;
use App\Support\HeroTitle;
use App\Support\InvoiceConfig;
use App\Support\MidtransConfig;
use App\Support\WhatsAppConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'midtrans' => MidtransConfig::get(),
            'invoice' => InvoiceConfig::get(),
            'contact' => ContactConfig::get(),
            'whatsapp' => WhatsAppConfig::get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $currentInvoice = InvoiceConfig::get();

        $validated = $request->validate([
            // Hero Section — validate without finfo (extension-based)
            'lead' => ['nullable', 'string', 'max:500'],
            'highlight' => ['nullable', 'string', 'max:500'],
            'tail' => ['nullable', 'string', 'max:500'],
            'width' => ['nullable', 'integer', 'min:100', 'max:5000'],
            'height' => ['nullable', 'integer', 'min:100', 'max:5000'],

            // Gateway Pembayaran Midtrans
            'server_key' => ['nullable', 'string', 'max:255'],
            'client_key' => ['nullable', 'string', 'max:255'],
            'is_production' => ['nullable', 'boolean'],

            // Identitas Kwitansi & Faktur Resmi (SPJ Dana BOS)
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'company_sub' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'signer_name' => ['sometimes', 'required', 'string', 'max:150'],
            'signer_title' => ['sometimes', 'required', 'string', 'max:150'],
            'signer_department' => ['nullable', 'string', 'max:150'],
            'signer_city' => ['sometimes', 'required', 'string', 'max:100'],

            // Kontak & Layanan Dukungan
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],

            // Integrasi WhatsApp Gateway
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_gateway' => ['nullable', 'string', 'in:fonnte,wablas,log'],
            'whatsapp_api_token' => ['nullable', 'string', 'max:255'],
            'whatsapp_sender_number' => ['nullable', 'string', 'max:50'],
        ]);

        // Simpan Hero Image jika diunggah
        if ($request->hasFile('hero_image')) {
            HeroImage::store($request->file('hero_image'), [
                'width' => (int) ($validated['width'] ?? 0),
                'height' => (int) ($validated['height'] ?? 0),
            ]);
        } elseif (isset($validated['width']) && isset($validated['height'])) {
            // Perbarui ukuran display tanpa mengunggah gambar baru
            HeroImage::setDisplaySize((int) $validated['width'], (int) $validated['height']);
        }

        // Simpan Hero Title jika diisi
        if (isset($validated['lead']) || isset($validated['highlight']) || isset($validated['tail'])) {
            HeroTitle::save(
                trim($validated['lead'] ?? HeroTitle::get()['lead']),
                trim($validated['highlight'] ?? HeroTitle::get()['highlight']),
                trim($validated['tail'] ?? HeroTitle::get()['tail']),
            );
        }

        // Simpan Konfigurasi Midtrans
        MidtransConfig::save(
            trim($validated['server_key'] ?? ''),
            trim($validated['client_key'] ?? ''),
            (bool) ($validated['is_production'] ?? false),
        );

        // Simpan Konfigurasi Kwitansi / Invoice SPJ BOS
        InvoiceConfig::save([
            'company_name' => trim($validated['company_name'] ?? $currentInvoice['company_name']),
            'company_sub' => trim($validated['company_sub'] ?? $currentInvoice['company_sub']),
            'company_address' => trim($validated['company_address'] ?? $currentInvoice['company_address']),
            'signer_name' => trim($validated['signer_name'] ?? $currentInvoice['signer_name']),
            'signer_title' => trim($validated['signer_title'] ?? $currentInvoice['signer_title']),
            'signer_department' => trim($validated['signer_department'] ?? $currentInvoice['signer_department']),
            'signer_city' => trim($validated['signer_city'] ?? $currentInvoice['signer_city']),
        ]);

        // Simpan Konfigurasi Kontak
        ContactConfig::save([
            'whatsapp_number' => trim($validated['whatsapp_number'] ?? ''),
            'email' => trim($validated['email'] ?? ''),
        ]);

        // Simpan Konfigurasi WhatsApp Gateway
        WhatsAppConfig::save([
            'enabled' => $request->boolean('whatsapp_enabled'),
            'gateway' => $validated['whatsapp_gateway'] ?? 'fonnte',
            'api_token' => trim($validated['whatsapp_api_token'] ?? ''),
            'sender_number' => trim($validated['whatsapp_sender_number'] ?? ''),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Pengaturan sistem, kwitansi & WhatsApp berhasil disimpan!');
    }

    public function destroyHero(): RedirectResponse
    {
        HeroImage::clear();
        HeroTitle::reset();

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Gambar hero berhasil dihapus.');
    }

    public function testWhatsApp(Request $request, WhatsAppService $whatsApp): RedirectResponse
    {
        $validated = $request->validate([
            'test_phone' => ['required', 'string', 'max:30'],
        ], [
            'test_phone.required' => 'Nomor WhatsApp tujuan uji coba wajib diisi.',
        ]);

        $success = $whatsApp->sendTestMessage($validated['test_phone']);

        if ($success) {
            return redirect()
                ->route('admin.settings.index')
                ->with('status', 'Pesan uji coba WhatsApp berhasil dikirim ke '.$validated['test_phone'].'!');
        }

        return redirect()
            ->route('admin.settings.index')
            ->withErrors(['test_phone' => 'Gagal mengirim pesan WhatsApp. Pastikan token API gateway valid.']);
    }
}
