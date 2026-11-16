<x-marketing-layout
    title="Demo Interaktif — Coba Veyra Educate & CBT Online"
    description="Uji coba langsung antarmuka ujian CBT siswa dari browser Anda dan akses portal demo manajemen sekolah tanpa perlu instalasi rumit.">

    {{-- Hero Section --}}
    <section class="border-b border-brand-100 bg-gradient-to-b from-brand-50/70 via-white to-white dark:border-slate-800 dark:from-slate-900 dark:via-slate-950 dark:to-slate-950">
        <div class="container-site section pb-12 sm:pb-16 text-center">
            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-3.5 py-1 text-xs font-bold mb-4 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                SIMULASI &amp; DEMO SISTEM INTERAKTIF
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-ink dark:text-white max-w-4xl mx-auto leading-tight">
                Rasakan Pengalaman Ujian CBT &amp; Portal Sekolah Secara Nyata
            </h1>
            <p class="mt-4 text-sm sm:text-base lg:text-lg text-muted max-w-2xl mx-auto leading-relaxed">
                Uji langsung antarmuka pengerjaan soal siswa langsung di browser Anda, atau masuk ke portal sekolah simulasi dengan <strong>1-klik tanpa password</strong> untuk melihat dashboard manajemen dan kwitansi BOS.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="#simulator" class="btn-primary inline-flex items-center gap-2 px-6 py-3 text-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Coba Simulator CBT Siswa</span>
                </a>
                
                <form action="{{ route('demo.login') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary inline-flex items-center gap-2 px-6 py-3 text-sm hover:border-brand-500 hover:text-brand-600 transition">
                        <svg class="h-4 w-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Masuk Portal Sekolah Demo (1-Klik)</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Interactive Simulator Section --}}
    <section id="simulator" class="section container-site" x-data="cbtSimulator()">
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-ink dark:text-white flex items-center gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-600 text-white text-xs font-black shadow-md shadow-brand-500/30">CBT</span>
                        <span>Simulator Ujian Siswa (Live In-Browser)</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-muted mt-1">
                        Simulasi nyata tampilan siswa saat mengerjakan ujian dari smartphone atau komputer.
                    </p>
                </div>

                {{-- Countdown Timer Widget --}}
                <div class="flex items-center gap-2 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 px-4 py-2 text-amber-800 dark:text-amber-300 shadow-sm" x-show="!isSubmitted">
                    <svg class="h-4 w-4 animate-spin text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-semibold">Sisa Waktu:</span>
                    <span class="font-mono font-bold text-sm tracking-wider" x-text="formatTimer()">14:59</span>
                </div>
            </div>

            {{-- Main Simulator Card --}}
            <div class="card overflow-hidden border-2 border-brand-200/80 dark:border-slate-800 shadow-xl relative bg-white dark:bg-slate-900">
                
                {{-- Header Siswa Bar --}}
                <div class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold">
                            AR
                        </div>
                        <div>
                            <div class="font-bold text-ink dark:text-slate-100">Ahmad Rizky Pratama</div>
                            <div class="text-[11px] text-muted">NIS: 202611082 · Kelas XII-RPL 1</div>
                        </div>
                    </div>
                    <div class="text-right hidden sm:block">
                        <div class="font-semibold text-ink dark:text-slate-200">Asesmen Sumatif Akhir Jenjang (CBT)</div>
                        <div class="text-[11px] text-brand-600 dark:text-brand-400">Bahasa Indonesia &amp; Literasi Digital</div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5" x-show="!isSubmitted">
                    <div class="bg-brand-500 h-1.5 transition-all duration-300" :style="'width: ' + ((Object.keys(answers).length / questions.length) * 100) + '%'"></div>
                </div>

                {{-- Content: State Belum Selesai --}}
                <div class="p-5 sm:p-8 space-y-6" x-show="!isSubmitted">
                    {{-- Soal Header & Nomor --}}
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-brand-100 dark:bg-brand-950 text-brand-700 dark:text-brand-300 text-xs font-bold" x-text="'Soal No. ' + (currentIndex + 1)">Soal No. 1</span>
                            <span class="text-xs text-muted" x-text="'dari ' + questions.length + ' butir'">dari 5 butir</span>
                        </div>
                        <button
                            type="button"
                            @click="toggleDoubtful(currentIndex)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="doubtful[currentIndex] ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-100 dark:hover:bg-amber-950/60 hover:text-amber-700'"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span x-text="doubtful[currentIndex] ? 'Ragu-Ragu (Ditandai)' : 'Tandai Ragu-Ragu'">Tandai Ragu-Ragu</span>
                        </button>
                    </div>

                    {{-- Pertanyaan --}}
                    <div class="text-base sm:text-lg font-medium text-ink dark:text-slate-100 leading-relaxed" x-text="questions[currentIndex].text">
                        Soal pertanyaan...
                    </div>

                    {{-- Pilihan Opsi A, B, C, D --}}
                    <div class="space-y-3 pt-2">
                        <template x-for="(option, key) in questions[currentIndex].options" :key="key">
                            <label
                                class="flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl border cursor-pointer transition-all duration-150"
                                :class="answers[currentIndex] === key 
                                    ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-950/40 text-brand-950 dark:text-brand-100 font-semibold shadow-sm ring-1 ring-brand-500' 
                                    : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50/60 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'"
                            >
                                <input
                                    type="radio"
                                    :name="'q_' + currentIndex"
                                    :value="key"
                                    x-model="answers[currentIndex]"
                                    class="hidden"
                                />
                                <span
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold uppercase transition"
                                    :class="answers[currentIndex] === key 
                                        ? 'bg-brand-600 text-white shadow-sm' 
                                        : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'"
                                    x-text="key"
                                ></span>
                                <span class="text-xs sm:text-sm leading-relaxed" x-text="option"></span>
                            </label>
                        </template>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2.5 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="prevQuestion"
                            :disabled="currentIndex === 0"
                            class="btn-secondary text-xs sm:text-sm py-2.5 px-4 justify-center disabled:opacity-40 disabled:cursor-not-allowed w-full sm:w-auto"
                        >
                            &larr; Soal Sebelumnya
                        </button>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button
                                type="button"
                                x-show="currentIndex < questions.length - 1"
                                @click="nextQuestion"
                                class="btn-primary text-xs sm:text-sm py-2.5 px-5 w-full sm:w-auto justify-center"
                            >
                                Soal Selanjutnya &rarr;
                            </button>

                            <button
                                type="button"
                                x-show="currentIndex === questions.length - 1"
                                @click="submitExam"
                                class="btn-primary bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm py-2.5 px-5 shadow-lg shadow-emerald-600/30 font-bold w-full sm:w-auto justify-center"
                            >
                                Selesaikan Ujian &check;
                            </button>
                        </div>
                    </div>

                    {{-- Navigasi Kotak Nomor Soal --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="text-xs font-bold text-slate-500 mb-2.5">Lembar Jawaban Cepat:</div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(q, idx) in questions" :key="idx">
                                <button
                                    type="button"
                                    @click="currentIndex = idx"
                                    class="h-9 w-9 rounded-xl font-bold text-xs flex items-center justify-center border transition"
                                    :class="{
                                        'ring-2 ring-brand-500 ring-offset-2 dark:ring-offset-slate-900': currentIndex === idx,
                                        'bg-amber-500 text-white border-amber-600': doubtful[idx],
                                        'bg-emerald-600 text-white border-emerald-700': answers[idx] !== undefined && !doubtful[idx],
                                        'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200': answers[idx] === undefined && !doubtful[idx]
                                    }"
                                    x-text="idx + 1"
                                ></button>
                            </template>
                        </div>
                        <div class="flex items-center gap-4 mt-3 text-[11px] text-muted">
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span> Sudah dijawab</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> Ragu-ragu</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-300 dark:bg-slate-700"></span> Belum dijawab</span>
                        </div>
                    </div>
                </div>

                {{-- Content: State Hasil Skor Ujian (Setelah Selesai) --}}
                <div class="p-6 sm:p-10 text-center space-y-6" x-show="isSubmitted" x-cloak>
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 text-4xl shadow-inner mb-2 animate-bounce">
                        🏆
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-ink dark:text-white">Ujian Simulasi Telah Selesai!</h3>
                        <p class="text-sm text-muted mt-1 max-w-md mx-auto">
                            Berikut adalah rekap kalkulasi nilai otomatis yang dihitung secara instan oleh sistem CBT:
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-xl mx-auto">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div class="text-2xl font-black text-brand-600" x-text="score">0</div>
                            <div class="text-[11px] font-bold text-muted mt-0.5">Nilai Akhir (Skala 100)</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div class="text-2xl font-black text-emerald-600" x-text="correctCount">0</div>
                            <div class="text-[11px] font-bold text-muted mt-0.5">Jawaban Benar</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div class="text-2xl font-black text-rose-600" x-text="questions.length - correctCount">0</div>
                            <div class="text-[11px] font-bold text-muted mt-0.5">Jawaban Salah</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div class="text-2xl font-black text-indigo-600">Instan</div>
                            <div class="text-[11px] font-bold text-muted mt-0.5">Siap Olah E-Rapor</div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                        <button
                            type="button"
                            @click="resetExam"
                            class="btn-secondary text-xs sm:text-sm py-2.5 px-5"
                        >
                            &circlearrowleft; Ulangi Ujian Simulasi
                        </button>
                        <form action="{{ route('demo.login') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-primary text-xs sm:text-sm py-2.5 px-6 shadow-lg shadow-brand-500/20">
                                Masuk ke Portal Sekolah Demo &rarr;
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cards Section: 2 Jalur Akses Demo --}}
    <section class="section container-site pt-0">
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Card 1: Portal Demo Sekolah --}}
            <div class="card p-6 sm:p-8 flex flex-col justify-between border-brand-200/90 dark:border-slate-800 bg-gradient-to-br from-white via-white to-brand-50/40 dark:from-slate-900 dark:via-slate-900 dark:to-brand-950/20">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 dark:bg-brand-950 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800 px-3 py-1 text-xs font-bold">
                        <span>🏫</span> AKSES DASHBOARD SEKOLAH INSTAN
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-ink dark:text-white">
                        Coba Fitur Manajemen &amp; BOS
                    </h2>
                    <p class="text-sm leading-relaxed text-muted">
                        Masuk langsung ke akun simulasi kepala sekolah &amp; operator. Anda dapat meninjau:
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-muted">
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-600 font-bold">✓</span>
                            <span><strong>Kwitansi Resmi SPJ BOS:</strong> Cetak invoice resmi lengkap dengan rincian durasi lisensi, stempel digital, dan tanda tangan manajer keuangan.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-600 font-bold">✓</span>
                            <span><strong>Katalog Aplikasi Terpasang:</strong> Akses wadah modul sekolah tanpa perlu membuat akun terpisah di tiap aplikasi.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-600 font-bold">✓</span>
                            <span><strong>Kustomisasi Tema Warna &amp; Logo:</strong> Personalisasi identitas portal dengan tema resmi sekolah Anda.</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full justify-center text-sm py-3 shadow-md shadow-brand-500/20">
                            Masuk Portal Sekolah Demo Sekarang &rarr;
                        </button>
                    </form>
                    <span class="text-[11px] text-center block text-slate-400 mt-2">
                        Tanpa perlu memasukkan password · Otomatis masuk ke akun simulasi
                    </span>
                </div>
            </div>

            {{-- Card 2: Ujian CBT & Pendampingan Gladi Bersih --}}
            <div class="card p-6 sm:p-8 flex flex-col justify-between border-slate-200 dark:border-slate-800">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 px-3 py-1 text-xs font-bold">
                        <span>📱</span> UJI COBA CBT &amp; GLADI BERSIH
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-ink dark:text-white">
                        Butuh Akun Guru / Siswa Penuh?
                    </h2>
                    <p class="text-sm leading-relaxed text-muted">
                        Ingin mencoba memasukkan 50 butir soal asli sekolah Anda atau melakukan uji coba gladi bersih dengan puluhan siswa sekaligus?
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-muted">
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Akun Guru Pembuat Soal:</strong> Fasilitas import bank soal dari Excel dan pembuatan jadwal ujian.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Akun Siswa Demo Massal:</strong> Kredensial untuk dicoba langsung oleh dewan guru dan perwakilan siswa.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Pendampingan Tim Teknis:</strong> Panduan langsung via Google Meet atau WhatsApp saat gladi bersih.</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a
                        href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate%2C%20saya%20ingin%20minta%20akses%20demo%20CBT%20dan%20jadwal%20gladi%20bersih%20untuk%20sekolah%20saya"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-primary bg-emerald-600 hover:bg-emerald-700 text-white w-full justify-center text-sm py-3 shadow-md shadow-emerald-600/20"
                    >
                        Minta Akun CBT via WhatsApp &rarr;
                    </a>
                    <span class="text-[11px] text-center block text-slate-400 mt-2">
                        Respon cepat dalam hitungan menit oleh tim teknis kami
                    </span>
                </div>
            </div>
        </div>

        {{-- Materi Pendukung untuk Rapat Sekolah --}}
        <div class="mt-12 rounded-3xl border border-brand-200/80 bg-brand-50/30 dark:bg-brand-950/20 p-6 sm:p-8">
            <div class="max-w-2xl mb-6">
                <h3 class="text-lg font-bold text-ink dark:text-white">Bahan Pertimbangan untuk Rapat Penganggaran Sekolah</h3>
                <p class="text-xs sm:text-sm text-muted mt-1">
                    Unduh atau cetak dokumen resmi untuk diajukan kepada Kepala Sekolah, Waka Kurikulum, atau Bendahara BOS.
                </p>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <a href="/brosur" class="flex items-center gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-brand-400 hover:shadow-md transition group">
                    <div class="h-10 w-10 rounded-xl bg-brand-50 dark:bg-brand-950/50 text-brand-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition">
                        📄
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-ink dark:text-white group-hover:text-brand-600">Brosur Digital</span>
                        <span class="block text-[11px] text-muted">Ringkasan fitur &amp; paket</span>
                    </div>
                </a>

                <a href="/proposal" class="flex items-center gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-brand-400 hover:shadow-md transition group">
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition">
                        📜
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-ink dark:text-white group-hover:text-emerald-600">Proposal Resmi (A4)</span>
                        <span class="block text-[11px] text-muted">Format cetak SPJ BOS</span>
                    </div>
                </a>

                <a href="/harga" class="flex items-center gap-3 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-brand-400 hover:shadow-md transition group">
                    <div class="h-10 w-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition">
                        🏷️
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-ink dark:text-white group-hover:text-amber-600">Daftar Harga Paket</span>
                        <span class="block text-[11px] text-muted">Mulai Rp 1,5 Juta/tahun</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- Script Simulator CBT Logic --}}
    <script>
        function cbtSimulator() {
            return {
                currentIndex: 0,
                timerSeconds: 899,
                timerInterval: null,
                answers: {},
                doubtful: {},
                isSubmitted: false,
                score: 0,
                correctCount: 0,
                questions: [
                    {
                        text: "Dalam rangka akuntabilitas penggunaan Dana Bantuan Operasional Satuan Pendidikan (BOSP), dokumen digital manakah yang wajib memuat rincian masa aktif lisensi, nomor transaksi resmi, dan tanda tangan penanggung jawab keuangan?",
                        options: {
                            a: "Brosur promosi produk",
                            b: "Kwitansi & Faktur Resmi berstempel digital dari penyedia platform",
                            c: "Hasil screenshot percakapan WhatsApp",
                            d: "Buku panduan instalasi aplikasi"
                        },
                        correct: "b"
                    },
                    {
                        text: "Fitur utama pada sistem CBT Online yang berfungsi mencegah kecurangan siswa saat ujian berlangsung adalah...",
                        options: {
                            a: "Pengubahan latar belakang tema warna",
                            b: "Pencetakan kartu peserta secara manual",
                            c: "Pengacakan butir soal dan pengacakan opsi jawaban (A,B,C,D,E) secara otomatis",
                            d: "Pengurangan waktu ujian tanpa pemberitahuan"
                        },
                        correct: "c"
                    },
                    {
                        text: "Sebuah sekolah dengan 500 siswa mengadakan Asesmen Sumatif 4 kali setahun. Manakah penghematan paling signifikan yang dirasakan sekolah saat beralih ke Veyra CBT Online?",
                        options: {
                            a: "Menghilangkan 100% biaya kertas, tinta cetak master, dan fotokopi lembar soal",
                            b: "Menghapus kebutuhan guru pengawas di dalam ruang kelas",
                            c: "Menghilangkan kurikulum nasional sekolah",
                            d: "Mengurangi jam belajar siswa di sekolah"
                        },
                        correct: "a"
                    },
                    {
                        text: "Prinsip Single Sign-On (SSO) terintegrasi pada Veyra Educate memberikan manfaat utama bagi guru dan siswa berupa...",
                        options: {
                            a: "Mengharuskan login ulang dengan password berbeda di setiap aplikasi",
                            b: "Satu akun resmi untuk mengakses portal sekolah, ujian CBT, dan seluruh modul tanpa repot login berulang kali",
                            c: "Menghapus identitas NPSN resmi sekolah",
                            d: "Membatasi akses ujian hanya untuk perangkat laptop sekolah"
                        },
                        correct: "b"
                    },
                    {
                        text: "Setelah asesmen berbasis komputer selesai dilaksanakan, fitur apakah yang paling mempercepat pekerjaan guru dalam merekap nilai raport?",
                        options: {
                            a: "Koreksi manual butir soal menggunakan lembar jawaban komputer (LJK)",
                            b: "Mengetik ulang nilai siswa satu per satu ke lembar kertas",
                            c: "Ekspor otomatis rekap nilai dan analisis butir soal ke format Excel yang siap diolah ke E-Rapor",
                            d: "Mengirim nilai melalui surat pos ke orang tua siswa"
                        },
                        correct: "c"
                    }
                ],
                init() {
                    this.startTimer();
                },
                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.timerSeconds > 0) {
                            this.timerSeconds--;
                        } else {
                            this.submitExam();
                        }
                    }, 1000);
                },
                formatTimer() {
                    const mins = Math.floor(this.timerSeconds / 60);
                    const secs = this.timerSeconds % 60;
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                },
                nextQuestion() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                    }
                },
                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },
                toggleDoubtful(idx) {
                    this.doubtful[idx] = !this.doubtful[idx];
                },
                submitExam() {
                    clearInterval(this.timerInterval);
                    let correct = 0;
                    this.questions.forEach((q, idx) => {
                        if (this.answers[idx] === q.correct) {
                            correct++;
                        }
                    });
                    this.correctCount = correct;
                    this.score = Math.round((correct / this.questions.length) * 100);
                    this.isSubmitted = true;
                },
                resetExam() {
                    this.answers = {};
                    this.doubtful = {};
                    this.currentIndex = 0;
                    this.timerSeconds = 899;
                    this.isSubmitted = false;
                    this.startTimer();
                }
            };
        }
    </script>
</x-marketing-layout>