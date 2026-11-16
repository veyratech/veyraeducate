<x-marketing-layout
    title="Fitur — Wadah Aplikasi Sekolah Veyra Educate"
    description="Jelajahi fitur Veyra Educate: dashboard sekolah, slot aplikasi, multi-tenant, akun terpusat, pembayaran Midtrans, dan panel admin."
>
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 to-white dark:border-slate-700/50 dark:from-slate-900 dark:to-slate-950">
        <div class="container-site section">
            <x-section-heading
                eyebrow="Fitur"
                title="Satu wadah untuk aplikasi sekolah"
                lead="Platform siap pakai untuk menampung aplikasi sekolah Anda — akun, langganan, dan slot aplikasi dalam satu tempat."
                align="center"
            />
        </div>
    </section>

    <section class="section container-site">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                [
                    'title' => 'Dashboard Sekolah & SSO Terpusat',
                    'body' => 'Satu akun dan kredensial SSO untuk mengakses CBT Online, E-Raport Kurikulum Merdeka, Absensi, PPDB, serta seluruh aplikasi aktif tanpa login berulang.',
                    'tag' => 'Semua paket',
                ],
                [
                    'title' => 'Generator Cetak Kartu Ujian & Pelajar (A4)',
                    'body' => 'Impor data siswa dari CSV Dapodik atau tempel langsung dari Excel. Dilengkapi Barcode/QR Code NISN, tema warna, dan tata letak siap cetak kertas A4.',
                    'tag' => 'Semua paket',
                ],
                [
                    'title' => 'Pusat Broadcast WhatsApp Sekolah',
                    'body' => 'Kirim pengumuman libur, jadwal ujian, undangan rapat komite, atau tagihan ke orang tua/wali murid menggunakan template otomatis satu klik.',
                    'tag' => 'Semua paket',
                ],
                [
                    'title' => 'Dokumen SPJ Dana BOS Otomatis (Kwitansi & BAST)',
                    'body' => 'Setiap transaksi lunas langsung menghasilkan Kwitansi Resmi berstempel dan Berita Acara Serah Terima (BAST) ukuran A4 sesuai standar audit ARKAS/BOS.',
                    'tag' => 'Semua paket',
                ],
                [
                    'title' => 'Manajemen Tim (Kepsek, Operator & Bendahara BOS)',
                    'body' => 'Kolaborasi antar akun Kepala Sekolah, Operator Dapodik, Guru, dan Bendahara BOS dalam satu tenant sekolah dengan sinkronisasi tagihan otomatis.',
                    'tag' => 'Semua paket',
                ],
                [
                    'title' => 'Program Kemitraan Referral (Komisi 10%)',
                    'body' => 'Rekomendasikan sekolah mitra di gugus/KKG/MKKS Anda dan dapatkan komisi 10% yang bisa dicairkan tunai atau menjadi potongan tagihan perpanjangan.',
                    'tag' => 'Semua paket',
                ],
                [
                    'title' => 'Pembayaran Otomatis & Transfer Bank BOS',
                    'body' => 'Mendukung QRIS, Virtual Account, E-Wallet (Midtrans), serta Transfer Bank Manual dengan unggah bukti transfer khusus untuk rekening Dana BOS.',
                    'tag' => 'Checkout',
                ],
                [
                    'title' => 'Custom Branding & Subdomain Sekolah',
                    'body' => 'Identitas logo, kop surat, dan penyesuaian tampilan profesional khusus untuk sekolah Anda.',
                    'tag' => 'Super VIP',
                ],
                [
                    'title' => 'Onboarding, Pelatihan & Priority Support',
                    'body' => 'Pendampingan tim operator & guru saat implementasi awal serta jalur bantuan prioritas via WhatsApp.',
                    'tag' => '1 Tahun & Super VIP',
                ],
            ] as $feature)
                <x-feature-card :title="$feature['title']" :body="$feature['body']">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </x-slot>
                    <span class="mt-4 inline-flex w-fit rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-brand-800 dark:bg-brand-950/40 dark:text-brand-300">
                        {{ $feature['tag'] }}
                    </span>
                </x-feature-card>
            @endforeach
        </div>
    </section>

    <section class="border-y border-brand-100 bg-white dark:border-slate-700/50 dark:bg-slate-950">
        <div class="container-site section">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="eyebrow">Pengalaman sekolah</p>
                    <h2 class="section-title">Dirancang untuk dipakai, bukan demo</h2>
                    <p class="section-lead">
                        Alur sekolah dipangkas: daftar, bayar, aktif, buka aplikasi.
                        Tidak ada klaim berlebihan — cek harga dan FAQ sebelum membeli.
                    </p>
                    <div class="mt-7 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <a href="/demo" class="btn-primary w-full sm:w-auto justify-center">Coba demo</a>
                        <a href="/harga" class="btn-secondary w-full sm:w-auto justify-center">Bandingkan paket</a>
                    </div>
                </div>

                <ul class="space-y-4">
                    @foreach ([
                        'Satu akun terpusat untuk Kepala Sekolah, Operator & Bendahara BOS',
                        'Langganan & seluruh aplikasi aktif otomatis setelah pelunasan',
                        'Kwitansi & BAST SPJ Dana BOS siap cetak ukuran A4 kapan saja',
                        'Cetak kartu ujian ber-barcode & broadcast WA wali murid langsung dari panel',
                    ] as $item)
                        <li class="card flex items-start gap-3 p-5 dark:bg-slate-900 dark:border-slate-800">
                            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-700 text-xs font-bold text-white" aria-hidden="true">✓</span>
                            <span class="text-sm font-medium text-ink dark:text-slate-100 sm:text-base">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="section container-site">
        <div class="card flex flex-col items-center gap-4 p-8 text-center sm:p-12 dark:bg-slate-900 dark:border-slate-800">
            <h2 class="text-xl font-extrabold text-ink dark:text-slate-100 sm:text-2xl">
                Lihat fitur sesuai paket
            </h2>
            <p class="max-w-xl text-sm leading-relaxed text-muted dark:text-slate-400 sm:text-base">
                Kartu harga mencantumkan rincian benefit per paket — Semester Rp 600.000, 1 Tahun Rp 1.100.000, Super VIP Rp 1.500.000.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="/harga" class="btn-primary">Lihat harga</a>
                <a href="{{ route('register') }}" class="btn-secondary">Daftar sekolah</a>
            </div>
        </div>
    </section>
</x-marketing-layout>
