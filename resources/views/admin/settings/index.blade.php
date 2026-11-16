<x-admin-layout title="Pengaturan Sistem — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Konfigurasi Sistem</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Pengaturan Sistem &amp; Legalitas</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    Konfigurasi penandatangan kwitansi (SPJ Dana BOS), gateway pembayaran Midtrans, dan kontak layanan.
                </p>
            </div>
            <a
                href="{{ route('admin.homepage.index') }}"
                class="btn-minimal-secondary text-xs sm:text-sm inline-flex items-center gap-1.5"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 11a1 1 0 011-1h14a1 1 0 011 1v8a1 1 0 01-1 1H5a1 1 0 01-1-1v-8z" />
                </svg>
                <span>Kelola Tampilan Homepage →</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site max-w-5xl space-y-6">

            {{-- Status Notification --}}
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            {{-- Guide banner --}}
            <div class="rounded-2xl border border-brand-200/70 bg-gradient-to-r from-brand-50 to-white p-5 dark:border-brand-900/50 dark:from-brand-950/30 dark:to-slate-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-ink dark:text-slate-100 flex items-center gap-2">
                        <span>💡</span> Pusat Konfigurasi Terpusat
                    </h3>
                    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 max-w-2xl">
                        Semua data penandatangan kwitansi dan proposal resmi dapat langsung Anda sesuaikan dari halaman ini tanpa harus mengedit file <code class="font-mono text-brand-700 dark:text-brand-300 font-semibold">.env</code> secara manual.
                    </p>
                </div>
                <a href="{{ route('admin.homepage.index') }}" class="btn-minimal-ghost text-xs text-brand-700 dark:text-brand-300 shrink-0 font-bold hover:underline">
                    Edit Banner &amp; Teks Web Depan →
                </a>
            </div>

            <form
                method="POST"
                action="{{ route('admin.settings.update') }}"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- CARD 1: IDENTITAS KWITANSI & SPJ DANA BOS --}}
                <div class="card-minimal p-6 space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📜</span>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">
                                1. Identitas Penandatangan Kwitansi &amp; Proposal (SPJ BOS)
                            </h3>
                        </div>
                        <p class="text-xs text-muted dark:text-slate-400 mt-1">
                            Informasi ini otomatis tampil di lembar Kwitansi Pembayaran Resmi dan Surat Penawaran Proposal A4.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="signer_name" value="Nama Lengkap Penandatangan (beserta gelar)" />
                            <input
                                id="signer_name"
                                name="signer_name"
                                type="text"
                                required
                                maxlength="150"
                                value="{{ old('signer_name', $invoice['signer_name'] ?? '') }}"
                                placeholder="Contoh: Ahmad Fauzi, S.Kom., M.T."
                                class="mt-1 form-input-minimal"
                            >
                            <x-input-error :messages="$errors->get('signer_name')" class="mt-1" />
                            <p class="text-[11px] text-muted mt-1">Nama ini akan dicantumkan di tanda tangan resmi kwitansi.</p>
                        </div>

                        <div>
                            <x-input-label for="signer_title" value="Jabatan Penandatangan" />
                            <input
                                id="signer_title"
                                name="signer_title"
                                type="text"
                                required
                                maxlength="150"
                                value="{{ old('signer_title', $invoice['signer_title'] ?? '') }}"
                                placeholder="Contoh: Finance & Licensing Manager / Direktur"
                                class="mt-1 form-input-minimal"
                            >
                            <x-input-error :messages="$errors->get('signer_title')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="signer_department" value="Bagian / Departemen" />
                            <input
                                id="signer_department"
                                name="signer_department"
                                type="text"
                                maxlength="150"
                                value="{{ old('signer_department', $invoice['signer_department'] ?? '') }}"
                                placeholder="Contoh: Bagian Keuangan & Penagihan"
                                class="mt-1 form-input-minimal"
                            >
                            <x-input-error :messages="$errors->get('signer_department')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="signer_city" value="Kota Penerbitan Dokumen" />
                            <input
                                id="signer_city"
                                name="signer_city"
                                type="text"
                                required
                                maxlength="100"
                                value="{{ old('signer_city', $invoice['signer_city'] ?? 'Sumenep') }}"
                                placeholder="Contoh: Sumenep"
                                class="mt-1 form-input-minimal"
                            >
                            <x-input-error :messages="$errors->get('signer_city')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Data Perusahaan / Kop Surat --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="company_name" value="Nama Perusahaan / Penerbit" />
                                <input
                                    id="company_name"
                                    name="company_name"
                                    type="text"
                                    required
                                    maxlength="255"
                                    value="{{ old('company_name', $invoice['company_name'] ?? 'PT. VEYRA CREATIVE TECH') }}"
                                    class="mt-1 form-input-minimal"
                                >
                                <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="company_sub" value="Sub-Kop / Keterangan Lembaga" />
                                <input
                                    id="company_sub"
                                    name="company_sub"
                                    type="text"
                                    maxlength="255"
                                    value="{{ old('company_sub', $invoice['company_sub'] ?? '') }}"
                                    placeholder="Penyedia Layanan Sistem Informasi Sekolah"
                                    class="mt-1 form-input-minimal"
                                >
                                <x-input-error :messages="$errors->get('company_sub')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="company_address" value="Alamat Lengkap & Kontak Perusahaan (Kop Surat)" />
                            <textarea
                                id="company_address"
                                name="company_address"
                                rows="2"
                                class="mt-1 form-input-minimal text-xs"
                                placeholder="Alamat lengkap, nomor telepon, dan email resmi..."
                            >{{ old('company_address', $invoice['company_address'] ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('company_address')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- CARD 2: GATEWAY PEMBAYARAN MIDTRANS --}}
                <div class="card-minimal p-6 space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">💳</span>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">
                                2. Integrasi Gateway Pembayaran (Midtrans)
                            </h3>
                        </div>
                        <p class="text-xs text-muted dark:text-slate-400 mt-1">
                            Digunakan untuk menerima pembayaran langganan via QRIS, Virtual Account bank, dan E-Wallet secara otomatis.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="server_key" value="Midtrans Server Key" />
                            <input
                                id="server_key"
                                name="server_key"
                                type="text"
                                placeholder="Contoh: Mid-server-xxxxxxxx / SB-Mid-server-xxxxxxxx"
                                value="{{ old('server_key', $midtrans['server_key'] ?? '') }}"
                                class="mt-1 form-input-minimal font-mono text-xs"
                            >
                            <x-input-error :messages="$errors->get('server_key')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="client_key" value="Midtrans Client Key" />
                            <input
                                id="client_key"
                                name="client_key"
                                type="text"
                                placeholder="Contoh: Mid-client-xxxxxxxx / SB-Mid-client-xxxxxxxx"
                                value="{{ old('client_key', $midtrans['client_key'] ?? '') }}"
                                class="mt-1 form-input-minimal font-mono text-xs"
                            >
                            <x-input-error :messages="$errors->get('client_key')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-2">
                        <label for="is_production" class="flex cursor-pointer items-center gap-2.5">
                            <input
                                id="is_production"
                                name="is_production"
                                type="checkbox"
                                value="1"
                                {{ old('is_production', $midtrans['is_production'] ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                            >
                            <div>
                                <span class="text-sm font-semibold text-ink dark:text-slate-200">Mode Produksi / Live Transaksi Nyata</span>
                                <p class="text-xs text-muted">Centang jika akun Midtrans sudah aktif untuk menerima dana asli. Jangan centang jika masih dalam tahap testing (Sandbox).</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- CARD 3: KONTAK CS & WHATSAPP --}}
                <div class="card-minimal p-6 space-y-5">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📱</span>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">
                                3. Kontak Resmi Layanan &amp; CS
                            </h3>
                        </div>
                        <p class="text-xs text-muted dark:text-slate-400 mt-1">
                            Nomor WhatsApp tujuan untuk konsultasi sekolah, permintaan demo, dan bantuan teknis.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="whatsapp_number" value="Nomor WhatsApp Admin / CS" />
                            <input
                                id="whatsapp_number"
                                name="whatsapp_number"
                                type="text"
                                maxlength="50"
                                value="{{ old('whatsapp_number', $contact['whatsapp_number'] ?? '085707074778') }}"
                                placeholder="Contoh: 085707074778"
                                class="mt-1 form-input-minimal"
                            >
                            <x-input-error :messages="$errors->get('whatsapp_number')" class="mt-1" />
                            <p class="text-[11px] text-muted mt-1">Gunakan format angka langsung (0857... atau 62857...).</p>
                        </div>

                        <div>
                            <x-input-label for="email" value="Email Dukungan / Layanan" />
                            <input
                                id="email"
                                name="email"
                                type="email"
                                maxlength="100"
                                value="{{ old('email', $contact['email'] ?? 'veyracreativetech@gmail.com') }}"
                                placeholder="Contoh: support@veyracreative.site"
                                class="mt-1 form-input-minimal"
                            >
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- CARD 4: INTEGRASI WHATSAPP GATEWAY --}}
                <div class="card-minimal p-6 space-y-5 border-emerald-200/80 dark:border-emerald-950/60">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg">💬</span>
                                <h3 class="text-base font-bold text-ink dark:text-slate-100">
                                    4. WhatsApp Gateway &amp; Notifikasi Otomatis
                                </h3>
                            </div>
                            <p class="text-xs text-muted dark:text-slate-400 mt-1">
                                Kirim kwitansi resmi SPJ BOS dan pengingat masa aktif langganan otomatis ke nomor WhatsApp Kepala Sekolah.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ ($whatsapp['enabled'] ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                <span class="h-2 w-2 rounded-full {{ ($whatsapp['enabled'] ?? false) ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                {{ ($whatsapp['enabled'] ?? false) ? 'Gateway Aktif' : 'Gateway Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-1">
                        <label for="whatsapp_enabled" class="flex cursor-pointer items-center gap-2.5">
                            <input
                                id="whatsapp_enabled"
                                name="whatsapp_enabled"
                                type="checkbox"
                                value="1"
                                {{ old('whatsapp_enabled', $whatsapp['enabled'] ?? false) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                            >
                            <div>
                                <span class="text-sm font-semibold text-ink dark:text-slate-200">Aktifkan Notifikasi WhatsApp Otomatis</span>
                                <p class="text-xs text-muted">Kirim pesan WhatsApp otomatis saat order disetujui/lunas dan saat langganan sekolah mendekati jatuh tempo.</p>
                            </div>
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <x-input-label for="whatsapp_gateway" value="Penyedia Gateway (Vendor)" />
                            <select
                                id="whatsapp_gateway"
                                name="whatsapp_gateway"
                                class="mt-1 form-input-minimal"
                            >
                                <option value="fonnte" {{ old('whatsapp_gateway', $whatsapp['gateway'] ?? 'fonnte') === 'fonnte' ? 'selected' : '' }}>Fonnte (Rekomendasi Indonesia - fonnte.com)</option>
                                <option value="wablas" {{ old('whatsapp_gateway', $whatsapp['gateway'] ?? '') === 'wablas' ? 'selected' : '' }}>Wablas (wablas.com)</option>
                                <option value="log" {{ old('whatsapp_gateway', $whatsapp['gateway'] ?? '') === 'log' ? 'selected' : '' }}>Simulasi Log Saja (Testing Tanpa API)</option>
                            </select>
                            <p class="text-[11px] text-muted mt-1">Dukung pengiriman pesan tanpa biaya per pesan menggunakan Fonnte atau Wablas.</p>
                        </div>

                        <div>
                            <x-input-label for="whatsapp_sender_number" value="Nomor Pengirim (Device WA)" />
                            <input
                                id="whatsapp_sender_number"
                                name="whatsapp_sender_number"
                                type="text"
                                maxlength="50"
                                placeholder="Contoh: 6285707074778"
                                value="{{ old('whatsapp_sender_number', $whatsapp['sender_number'] ?? '') }}"
                                class="mt-1 form-input-minimal font-mono text-xs"
                            >
                            <x-input-error :messages="$errors->get('whatsapp_sender_number')" class="mt-1" />
                            <p class="text-[11px] text-muted mt-1">Nomor perangkat WhatsApp yang terhubung di dashboard gateway.</p>
                        </div>

                        <div>
                            <x-input-label for="whatsapp_api_token" value="API Token Gateway" />
                            <input
                                id="whatsapp_api_token"
                                name="whatsapp_api_token"
                                type="password"
                                placeholder="Masukkan token API Fonnte / Wablas Anda"
                                value="{{ old('whatsapp_api_token', $whatsapp['api_token'] ?? '') }}"
                                class="mt-1 form-input-minimal font-mono text-xs"
                            >
                            <x-input-error :messages="$errors->get('whatsapp_api_token')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- SUBMIT BAR --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <x-primary-button class="btn-minimal-primary px-6 py-2.5 text-sm font-bold shadow-md">
                        Simpan Semua Pengaturan
                    </x-primary-button>
                    <div class="flex items-center gap-3">
                        <a href="{{ url('proposal') }}" target="_blank" class="link-sm text-xs text-brand-600 font-semibold hover:underline">
                            Lihat Lembar Proposal →
                        </a>
                        <span class="text-slate-300">|</span>
                        <a href="{{ route('home') }}" target="_blank" class="link-sm text-xs text-muted hover:underline">
                            Buka Web Depan →
                        </a>
                    </div>
                </div>
            </form>

            {{-- TEST WHATSAPP MESSAGE CARD --}}
            <div class="card-minimal p-5 bg-slate-50/60 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-ink dark:text-slate-200 flex items-center gap-2">
                            <span>🧪</span> Tes Koneksi WhatsApp Gateway
                        </h4>
                        <p class="text-xs text-muted mt-0.5">
                            Kirim pesan tes ke nomor Anda untuk memastikan koneksi token Fonnte/Wablas berjalan lancar.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.test-whatsapp') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        @csrf
                        <input
                            type="text"
                            name="test_phone"
                            placeholder="08xxxxxxxxxx"
                            value="{{ old('test_phone', $contact['whatsapp_number'] ?? '') }}"
                            required
                            class="form-input-minimal text-xs py-1.5 px-3 w-40 sm:w-48"
                        />
                        <button type="submit" class="btn-minimal-secondary text-xs py-1.5 px-3 shrink-0">
                            Kirim Tes ➔
                        </button>
                    </form>
                </div>
                <x-input-error :messages="$errors->get('test_phone')" class="mt-2" />
            </div>
        </div>
    </div>
</x-admin-layout>
