<x-marketing-layout
    title="Tentang Veyra Educate"
    description="Veyra Educate adalah wadah aplikasi sekolah untuk Indonesia — satu akun, langganan transparan, dashboard siap isi."
>
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 to-white dark:border-slate-700/50 dark:from-slate-900 dark:to-slate-950">
        <div class="container-site section">
            <x-section-heading
                eyebrow="Tentang"
                title="Dibuat untuk sekolah Indonesia"
                lead="Veyra Educate menampung aplikasi sekolah Anda: akun terpusat, langganan jelas, tanpa server lokal."
                align="center"
            />
        </div>
    </section>

    <section class="section container-site">
        <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
            <article class="card p-5 sm:p-7">
                <p class="eyebrow">Visi</p>
                <h2 class="mt-3 text-lg sm:text-xl font-extrabold text-ink">Digitalisasi sekolah tanpa ribet</h2>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    Bukan demo rumit — platform yang bisa dipakai operator dengan pelatihan singkat, di hari kerja yang padat.
                </p>
            </article>
            <article class="card p-5 sm:p-7">
                <p class="eyebrow">Misi</p>
                <h2 class="mt-3 text-lg sm:text-xl font-extrabold text-ink">Satu wadah, banyak aplikasi</h2>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    Aplikasi sekolah cukup diunggah ke wadah — akun, billing, dan dashboard sudah siap dari awal.
                </p>
            </article>
            <article class="card p-5 sm:p-7">
                <p class="eyebrow">Pendekatan</p>
                <h2 class="mt-3 text-lg sm:text-xl font-extrabold text-ink">Transparan sejak harga</h2>
                <p class="mt-3 text-sm leading-relaxed text-muted">
                    Harga di halaman = harga di checkout. Pembayaran resmi via Midtrans, aktivasi otomatis setelah terverifikasi.
                </p>
            </article>
        </div>
    </section>

    <section class="border-y border-brand-100 bg-white dark:border-slate-700/50 dark:bg-slate-950">
        <div class="container-site section">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="eyebrow">Prinsip produk</p>
                    <h2 class="section-title">Apa yang kami janjikan — dan yang tidak</h2>
                    <ul class="mt-6 space-y-4">
                        @foreach ([
                            ['Kami janjikan', 'Harga paket jelas, kanal pembayaran resmi, dan status langganan yang bisa dipantau di panel.'],
                            ['Kami janjikan', 'Fitur yang tercantum di kartu harga sesuai cakupan paket — tanpa biaya tersembunyi.'],
                            ['Kami tidak', 'Menampilkan testimoni sekolah palsu atau klaim performa tanpa data. Buktikan lewat demo.'],
                            ['Kami tidak', 'Mewajibkan server lokal — infrastruktur cloud dikelola tim kami.'],
                        ] as [$label, $body])
                            <li class="card flex flex-col gap-2 p-5 sm:flex-row sm:items-start sm:gap-4">
                                <span class="inline-flex w-fit shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider {{ $label === 'Kami janjikan' ? 'bg-brand-50 text-brand-800 dark:bg-brand-950/40 dark:text-brand-300' : 'bg-surface text-muted dark:bg-slate-800/60 dark:text-slate-400' }}">
                                    {{ $label }}
                                </span>
                                <p class="text-sm leading-relaxed text-muted">{{ $body }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card p-7 sm:p-9">
                    <h3 class="text-lg font-bold text-ink">Ekosistem layanan</h3>
                    <div class="mt-5 space-y-5">
                        <div>
                            <p class="text-sm font-semibold text-ink">Pembayaran &amp; aktivasi otomatis</p>
                            <p class="mt-1 text-sm text-muted">QRIS, virtual account, e-wallet — verifikasi lisensi tanpa campur tangan admin.</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-ink">Aplikasi yang terus bertambah</p>
                            <p class="mt-1 text-sm text-muted">Aplikasi baru diunggah ke wadah dan tersedia di panel pelanggan selama masa aktif.</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-ink">Akses terpusat (SSO)</p>
                            <p class="mt-1 text-sm text-muted">Satu kredensial operator untuk panel sekolah dan aplikasi aktif.</p>
                        </div>
                    </div>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="/demo" class="btn-primary">Minta demo</a>
                        <a href="/kontak" class="btn-secondary">Hubungi kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section container-site">
        <div class="card flex flex-col items-center gap-4 p-8 text-center sm:p-12">
            <h2 class="text-xl font-extrabold text-ink sm:text-2xl">Siap menyiapkan wadah aplikasi sekolah Anda?</h2>
            <p class="max-w-xl text-sm leading-relaxed text-muted sm:text-base">
                Pendaftaran memakan waktu kurang dari lima menit. Tim kami siap mendampingi onboarding melalui WhatsApp.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="btn-primary">Daftar sekolah</a>
                <a href="/harga" class="btn-secondary">Lihat paket</a>
            </div>
        </div>
    </section>
</x-marketing-layout>
