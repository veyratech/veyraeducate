<x-marketing-layout
    title="Kebijakan Privasi & Perlindungan Data Siswa — Veyra Educate"
    description="Kebijakan privasi dan perlindungan data pribadi siswa serta tenaga pendidik di platform Veyra Educate sesuai UU No. 27 Tahun 2022 (UU PDP)."
>
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 to-white dark:border-slate-800 dark:from-slate-900 dark:to-slate-950">
        <div class="container-site section">
            <x-section-heading
                eyebrow="Keamanan & Legalitas"
                title="Kebijakan Privasi & Perlindungan Data Siswa"
                lead="Komitmen penuh PT. Veyra Creative Tech dalam melindungi integritas data peserta didik, pendidik, dan arsip asesmen sekolah sesuai UU Pelindungan Data Pribadi (UU PDP)."
                align="center"
            />
            <p class="text-center text-xs text-slate-400 mt-3 font-mono">
                Terakhir diperbarui: 1 Oktober 2026 &bull; Berlaku untuk seluruh ekosistem Veyra Educate &amp; CBT Online
            </p>
        </div>
    </section>

    <section class="section container-site max-w-4xl mx-auto">
        <div class="card p-6 sm:p-10 space-y-8 text-slate-700 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
            
            {{-- Point 1 --}}
            <div>
                <div class="flex items-center gap-2.5 text-brand-700 dark:text-brand-400 font-extrabold text-base sm:text-lg mb-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-bold">1</span>
                    <h3>Prinsip Dasar &amp; Kedaulatan Data Sekolah</h3>
                </div>
                <p>
                    PT. Veyra Creative Tech bertindak semata-mata sebagai <strong>Pengolah Data (Data Processor)</strong> infrastruktur teknologi cloud. Seluruh data identitas siswa, nilai rapor, bank soal ujian, dan profil tenaga pengajar adalah <strong>100% Hak Milik Eksklusif Pihak Sekolah (Data Controller)</strong>. Veyra Educate <strong>TIDAK PERNAH</strong> dan <strong>TIDAK AKAN PERNAH</strong> menjual, menyewakan, atau membagikan data sekolah kepada pihak ketiga, biro periklanan, atau pihak eksternal manapun.
                </p>
            </div>

            {{-- Point 2 --}}
            <div>
                <div class="flex items-center gap-2.5 text-brand-700 dark:text-brand-400 font-extrabold text-base sm:text-lg mb-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-bold">2</span>
                    <h3>Kategori Data yang Dikelola</h3>
                </div>
                <ul class="list-disc pl-6 space-y-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    <li><strong>Data Peserta Didik:</strong> Nomor Induk Siswa (NIS), NISN, Nama Lengkap, Rombongan Belajar (Kelas), dan foto profil ujian (jika diunggah).</li>
                    <li><strong>Data Asesmen &amp; Evaluasi:</strong> Lembar jawaban digital, catatan riwayat pengerjaan soal, rekapitulasi skor/nilai akhir, dan waktu pengerjaan ujian.</li>
                    <li><strong>Data Akun Sekolah:</strong> Nama resmi lembaga sekolah/madrasah, NPSN/NSM, alamat email penanggung jawab, dan nomor telepon kontak operator.</li>
                </ul>
            </div>

            {{-- Point 3 --}}
            <div>
                <div class="flex items-center gap-2.5 text-brand-700 dark:text-brand-400 font-extrabold text-base sm:text-lg mb-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-bold">3</span>
                    <h3>Standar Keamanan Teknis &amp; Enkripsi</h3>
                </div>
                <p class="mb-3">
                    Kami menerapkan standar arsitektur keamanan bertingkat untuk menjamin kelancaran dan kerahasiaan asesmen sekolah:
                </p>
                <div class="grid sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 p-4">
                        <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 mb-1">
                            <span>🔒</span> Enkripsi Transportasi Data (TLS/SSL)
                        </p>
                        <p class="text-slate-500">
                            Semua pertukaran data antara ponsel/laptop siswa dengan server dilindungi enkripsi HTTPS 256-bit standar industri perbankan.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 p-4">
                        <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 mb-1">
                            <span>🛡️</span> Multi-Tenant Data Isolation
                        </p>
                        <p class="text-slate-500">
                            Setiap sekolah memiliki segmentasi basis data dan token tersendiri sehingga tidak ada kemungkinan data sekolah lain saling tertukar.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Point 4 --}}
            <div>
                <div class="flex items-center gap-2.5 text-brand-700 dark:text-brand-400 font-extrabold text-base sm:text-lg mb-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-bold">4</span>
                    <h3>Hak Sekolah Terhadap Data (Ekspor &amp; Penghapusan)</h3>
                </div>
                <p>
                    Pihak sekolah memiliki hak penuh setiap saat untuk mengunduh seluruh arsip nilai dalam format spreadsheet (Microsoft Excel) serta meminta penghapusan total data lembaga apabila masa langganan telah berakhir dan tidak diperpanjang.
                </p>
            </div>

            {{-- Point 5 --}}
            <div>
                <div class="flex items-center gap-2.5 text-brand-700 dark:text-brand-400 font-extrabold text-base sm:text-lg mb-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 text-xs font-bold">5</span>
                    <h3>Kontak Layanan Privasi &amp; Data Protection</h3>
                </div>
                <p>
                    Apabila pihak sekolah, pengawas, atau komite madrasah memerlukan klarifikasi atau surat kepatuhan formal terkait keamanan data, silakan hubungi tim legalitas kami:
                </p>
                <div class="mt-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs sm:text-sm text-emerald-900 dark:text-emerald-200">
                    <p class="font-bold">PT. VEYRA CREATIVE TECH — Divisi Perlindungan Data &amp; Kepatuhan</p>
                    <p class="mt-0.5">Alamat: Jl. Raya Gapura, Sumenep, Jawa Timur &bull; WhatsApp Resmi: +62 857-0707-4778</p>
                    <p class="mt-0.5">Email Kepatuhan: legal@veyracreative.site / kontak@veyracreative.site</p>
                </div>
            </div>

        </div>
    </section>
</x-marketing-layout>
