<x-marketing-layout
    title="FAQ — Pertanyaan yang Sering Diajukan"
    description="Jawaban FAQ Veyra Educate: apa itu platform, cara kerja pembayaran, kebutuhan server, pembayaran pending, dan ganti paket."
>
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 to-white dark:border-slate-700/50 dark:from-slate-900 dark:to-slate-950">
        <div class="container-site section">
            <x-section-heading
                eyebrow="Bantuan"
                title="Pertanyaan yang sering diajukan"
                lead="Belum ketemu? Chat WhatsApp — sebutkan nama sekolah dan topik (bayar, server, paket…)."
                align="center"
            />

            <div class="mx-auto mt-8 max-w-xl">
                <label for="faq_search" class="sr-only">Cari FAQ</label>
                <input
                    id="faq_search"
                    type="search"
                    placeholder="Cari: bayar, server, paket…"
                    class="block w-full rounded-xl border-brand-200 bg-white text-ink shadow-sm placeholder:text-muted/70 focus:border-brand-500 focus:ring-brand-500"
                    x-data
                    x-model="window.faqQuery"
                    @input.window="window.faqQuery = $event.target.value"
                >
            </div>
        </div>
    </section>

    <section class="section container-site" x-data="{ get q() { return (window.faqQuery || '').toLowerCase() } }">
        <div class="grid gap-4 lg:grid-cols-2" role="list">
            @foreach ([
                [
                    'q' => 'Apa itu Veyra Educate?',
                    'a' => 'Veyra Educate adalah wadah aplikasi sekolah: dashboard, akun terpusat, dan langganan. Aplikasi sekolah Anda tampil di dashboard setelah diunggah ke platform.',
                    'tags' => 'platform wadah aplikasi',
                ],
                [
                    'q' => 'Apa bedanya antar paket?',
                    'a' => 'Semua paket mendapat akses panel & slot aplikasi. Paket lebih tinggi menambah masa berlaku, custom branding, onboarding, dan prioritas support.',
                    'tags' => 'paket super tahunan semester',
                ],
                [
                    'q' => 'Apakah sekolah perlu menyiapkan server sendiri?',
                    'a' => 'Tidak. Seluruh infrastruktur, hosting, dan pemeliharaan server ditangani oleh tim Veyra Educate.',
                    'tags' => 'server cloud infrastruktur',
                ],
                [
                    'q' => 'Bagaimana jika pembayaran pending atau gagal?',
                    'a' => 'Akun tetap dapat masuk. Panel menampilkan tombol bayar ulang dengan nomor order baru. Status berubah Aktif setelah notifikasi pelunasan diterima dari kanal pembayaran.',
                    'tags' => 'bayar pending gagal midtrans',
                ],
                [
                    'q' => 'Bisakah berganti paket setelah mendaftar?',
                    'a' => 'Untuk perubahan paket, hubungi tim kami via WhatsApp — kami bantu prosesnya sesuai masa aktif langganan Anda.',
                    'tags' => 'ganti paket upgrade',
                ],
                [
                    'q' => 'Kanal pembayaran apa saja yang didukung?',
                    'a' => 'QRIS, virtual account bank, dan e-wallet melalui Midtrans. Pilihan kanal ditampilkan di halaman checkout sesuai ketersediaan.',
                    'tags' => 'bayar qris va ewallet midtrans',
                ],
                [
                    'q' => 'Berapa lama proses onboarding?',
                    'a' => 'Pendaftaran akun kurang dari lima menit. Onboarding tim sekolah dapat didampingi via WhatsApp.',
                    'tags' => 'onboarding pendampingan',
                ],
                [
                    'q' => 'Apakah ada batasan jumlah akun guru?',
                    'a' => 'Tidak ada batasan ketat pada seluruh paket. Penggunaan wajar per sekolah yang menjadi acuan, bukan angka langganan.',
                    'tags' => 'akun guru unlimited kuota',
                ],
            ] as $item)
                <details
                    class="card group p-4 sm:p-6"
                    role="listitem"
                    data-search="{{ strtolower($item['q'] . ' ' . $item['a'] . ' ' . $item['tags']) }}"
                    x-show="!q || $el.dataset.search.includes(q)"
                >
                    <summary class="cursor-pointer list-none rounded-md text-sm font-bold text-ink marker:hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 sm:text-base">
                        {{ $item['q'] }}
                    </summary>
                    <p class="mt-2.5 sm:mt-3 text-xs sm:text-sm leading-relaxed text-muted">{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>

        <div class="card mt-10 flex flex-col items-center gap-4 p-5 sm:p-8 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-ink">Masih ada pertanyaan?</h2>
                <p class="mt-1 text-xs sm:text-sm text-muted">Chat tim kami — sebutkan sekolah & topik agar lebih cepat dibantu.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row w-full sm:w-auto">
                <a class="btn-primary w-full sm:w-auto justify-center" href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate%2C%20saya%20punya%20pertanyaan" target="_blank" rel="noopener noreferrer">
                    Chat WhatsApp
                </a>
                <a class="btn-secondary w-full sm:w-auto justify-center" href="/kontak">Halaman kontak</a>
            </div>
        </div>
    </section>
</x-marketing-layout>
