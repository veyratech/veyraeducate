<x-marketing-layout
    title="Kontak — Hubungi Tim Veyra Educate"
    description="Hubungi tim Veyra Educate via WhatsApp untuk konsultasi paket, demo, dan onboarding sekolah."
>
    @php
        $contact = \App\Support\ContactConfig::get();
        $waRaw = preg_replace('/\D+/', '', (string) ($contact['whatsapp_number'] ?? '085707074778'));
        $waIntl = str_starts_with($waRaw, '0') ? '62' . substr($waRaw, 1) : ($waRaw ?: '6285707074778');
        $waDisplay = $contact['whatsapp_number'] ?? '0857-0707-4778';
        $contactEmail = $contact['email'] ?? 'veyracreativetech@gmail.com';
    @endphp
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 to-white dark:border-slate-700/50 dark:from-slate-900 dark:to-slate-950">
        <div class="container-site section">
            <x-section-heading
                eyebrow="Kontak"
                title="Hubungi tim Veyra Educate"
                lead="Kanal utama: WhatsApp. Balasan pada hari kerja — untuk demo &amp; onboarding sekolah, sertakan nama sekolah Anda."
                align="center"
            />
        </div>
    </section>

    <section class="section container-site">
        <div class="grid gap-8 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <div
                    x-data="{
                        name: '',
                        school: '',
                        topic: 'Konsultasi Paket Sekolah',
                        message: '',
                        get whatsappUrl() {
                            let text = `Halo Tim Veyra Educate,\n\nNama: ${this.name || '-'}\nSekolah: ${this.school || '-'}\nTopik: ${this.topic}\nPesan/Kebutuhan: ${this.message || 'Saya ingin informasi lebih lanjut terkait ekosistem Veyra Educate.'}`;
                            return `https://wa.me/{{ $waIntl }}?text=${encodeURIComponent(text)}`;
                        }
                    }"
                    class="card p-5 sm:p-8 dark:bg-slate-850 dark:border-slate-800"
                >
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div>
                            <h2 class="text-lg font-bold text-ink dark:text-slate-100">Kirim Pesan Konsultasi</h2>
                            <p class="text-xs text-muted dark:text-slate-400 mt-0.5">
                                Isi formulir di bawah untuk terhubung langsung dengan tim konsultan Veyra via WhatsApp resmi.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Online
                        </span>
                    </div>

                    <form @submit.prevent="window.open(whatsappUrl, '_blank')" class="space-y-4 mt-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">Nama Anda <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    x-model="name"
                                    required
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">Nama Sekolah / Lembaga <span class="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    x-model="school"
                                    required
                                    placeholder="Contoh: SMAN 1 Jakarta"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">Topik Kebutuhan</label>
                            <select
                                x-model="topic"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            >
                                <option value="Konsultasi Paket Sekolah">Konsultasi Paket &amp; Biaya Langganan</option>
                                <option value="Demo Aplikasi & Modul">Permintaan Jadwal Demo Aplikasi</option>
                                <option value="Bantuan Onboarding & Teknis">Bantuan Onboarding &amp; Setup Server</option>
                                <option value="Pengajuan Kustom Modul">Pengajuan Modul / Sistem Khusus</option>
                                <option value="Pertanyaan Umum">Lainnya / Pertanyaan Umum</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">Pesan / Kebutuhan Tambahan</label>
                            <textarea
                                x-model="message"
                                rows="3"
                                placeholder="Tuliskan kendala, modul yang dicari, atau jadwal demo yang diinginkan..."
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            ></textarea>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <p class="text-[11px] text-muted dark:text-slate-400">
                                Pesan otomatis disiapkan dan dibuka di WhatsApp resmi.
                            </p>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 text-xs shadow-md shadow-emerald-600/20 transition-all active:scale-95 cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.149.929 3.182 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.768-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.074-2.127-.518-1.782-.738-2.919-2.56-3.007-2.678-.088-.118-.716-.953-.716-1.815 0-.862.451-1.286.613-1.46.162-.174.354-.218.472-.218.118 0 .236.001.339.006.109.005.253-.042.395.3.147.354.502 1.224.546 1.313.044.089.074.192.015.31-.059.118-.089.192-.177.295-.088.104-.186.232-.266.311-.088.089-.18.186-.077.363.103.177.458.756.983 1.224.675.602 1.244.788 1.421.877.177.089.28-.074.383-.192.103-.118.442-.516.56-.693.118-.177.236-.148.398-.089.162.059 1.026.484 1.203.573.177.089.295.133.339.207.044.074.044.43-.1 1.007z"/>
                                </svg>
                                <span>Hubungi via WhatsApp</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <div class="card p-7 dark:bg-slate-900 dark:border-slate-800">
                    <h2 class="text-base font-bold text-ink dark:text-slate-100">WhatsApp &amp; Telepon</h2>
                    <p class="mt-2 text-sm font-semibold text-ink dark:text-slate-200">{{ $waDisplay }}</p>
                    <a class="btn-primary mt-4 w-full" href="https://wa.me/{{ $waIntl }}?text=Halo%20Veyra%20Educate" target="_blank" rel="noopener noreferrer">
                        Buka WhatsApp
                    </a>
                    <p class="mt-3 text-xs text-muted dark:text-slate-400">Konsultasi paket, demo, dan onboarding sekolah.</p>
                </div>

                <div class="card p-7 dark:bg-slate-900 dark:border-slate-800">
                    <h2 class="text-base font-bold text-ink dark:text-slate-100">Kantor Pusat</h2>
                    <p class="mt-2 text-xs font-bold text-brand-700 dark:text-brand-400 uppercase tracking-wider">PT. VEYRA CREATIVE TECH</p>
                    <p class="mt-1 text-xs text-muted dark:text-slate-400 leading-relaxed">
                        Jl. Dsn Pakacangann Des. billapora Rebba No. 03 Kec. Lenteng Kab. Sumenep KodePos: 69461
                    </p>
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <span class="text-muted dark:text-slate-400">Email:</span>
                        <a href="mailto:{{ $contactEmail }}" class="text-brand-600 dark:text-brand-400 font-semibold hover:underline ml-1">
                            {{ $contactEmail }}
                        </a>
                    </div>
                </div>

                <div class="card p-7 dark:bg-slate-900 dark:border-slate-800">
                    <h2 class="text-base font-bold text-ink dark:text-slate-100">Paket singkat</h2>
                    <ul class="mt-3 space-y-2 text-sm text-muted dark:text-slate-400">
                        <li>Semester — Rp 600.000 / 6 bulan</li>
                        <li>1 Tahun — Rp 1.100.000 / 12 bulan</li>
                        <li>Super VIP — Rp 1.500.000 / 12 bulan + ekstra</li>
                    </ul>
                    <a class="btn-secondary mt-4 w-full" href="/harga">Bandingkan paket</a>
                </div>

                <div class="card p-7 dark:bg-slate-900 dark:border-slate-800">
                    <h2 class="text-base font-bold text-ink dark:text-slate-100">Pertanyaan umum</h2>
                    <p class="mt-2 text-sm text-muted dark:text-slate-400">Cek FAQ dulu untuk topik pembayaran, server, dan ganti paket.</p>
                    <a class="btn-secondary mt-4 w-full" href="/faq">Buka FAQ</a>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
