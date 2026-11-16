<x-app-layout>
    <script>
        window.cardStudioData = function () {
            return {
                cardType: 'exam',
                cardTemplate: 'modern',
                cardsPerPage: 8,
                cardTheme: @js($school->meta['brand_theme'] ?? 'teal'),
                examTitle: 'ASESMEN SUMATIF AKHIR SEMESTER (SAS)',
                academicYear: @js($school->meta['academic_year'] ?? '2026/2027'),
                headmasterName: @js($school->meta['headmaster_name'] ?? $school->meta['headmaster'] ?? 'Drs. H. Mulyadi, M.Pd.'),
                headmasterNip: @js($school->meta['headmaster_nip'] ?? '19780512 200312 1 002'),
                pasteModalOpen: false,
                pasteRawText: '',
                students: @js($defaultStudents),
                newStudent: {
                    nisn: '',
                    nik: '',
                    name: '',
                    gender: 'L',
                    class: '10 IPA 1',
                    birth_place: 'Indonesia',
                    birth_date: '2008-01-01',
                    session: 'Sesi 1 (07.30 - 09.30)',
                    room: 'Lab Komputer 1',
                    exam_user: '',
                    exam_pass: ''
                },
                addStudent() {
                    if (!this.newStudent.name || !this.newStudent.nisn) {
                        alert('Mohon isi nama siswa dan NISN.');
                        return;
                    }
                    const examUser = this.newStudent.exam_user || this.newStudent.nisn;
                    const examPass = this.newStudent.exam_pass || Math.random().toString(36).substring(2, 8).toUpperCase();
                    this.students.push({
                        ...this.newStudent,
                        nik: this.newStudent.nik || ('3201' + this.newStudent.nisn),
                        exam_user: examUser,
                        exam_pass: examPass
                    });
                    this.newStudent.name = '';
                    this.newStudent.nisn = '';
                    this.newStudent.exam_user = '';
                    this.newStudent.exam_pass = '';
                },
                removeStudent(index) {
                    this.students.splice(index, 1);
                },
                clearAllStudents() {
                    if (confirm('Kosongkan semua daftar siswa?')) {
                        this.students = [];
                    }
                },
                parseRowsFromText(rawText) {
                    const lines = rawText.split(/\r?\n/).filter(line => line.trim().length > 0);
                    let addedCount = 0;

                    lines.forEach((line, idx) => {
                        let cols = line.includes('\t') ? line.split('\t') : line.split(/[,;]/);
                        cols = cols.map(c => c.trim().replace(/^['"]|['"]$/g, ''));

                        // Skip header row if first column says 'nisn' or 'no'
                        if (idx === 0 && /^(nisn|no|nomor)$/i.test(cols[0] || '')) {
                            return;
                        }

                        if (cols.length >= 2) {
                            const nisn = cols[0] || ('008' + Math.floor(1000000 + Math.random() * 9000000));
                            // Auto-detect 8+ column Dapodik CSV (NISN, NIK, Nama, L/P, Tingkat, Rombel, Tempat Lahir, Tgl Lahir)
                            const isDapodikFormat = cols.length >= 7 && /^\d{8,16}$/.test(cols[1] || '');
                            const nik = isDapodikFormat ? cols[1] : ('3201' + nisn);
                            const name = (isDapodikFormat ? cols[2] : cols[1]) || ('Siswa ' + (this.students.length + 1));
                            const gender = isDapodikFormat ? (cols[3] || 'L') : 'L';
                            const cls = (isDapodikFormat ? (cols[5] || cols[4]) : cols[2]) || '10 IPA 1';
                            const birthPlace = isDapodikFormat ? (cols[6] || 'Indonesia') : 'Indonesia';
                            const birthDate = isDapodikFormat ? (cols[7] || '2008-01-01') : '2008-01-01';
                            const room = (!isDapodikFormat && cols[3]) ? cols[3] : 'Lab Komputer 1';
                            const session = (!isDapodikFormat && cols[4]) ? cols[4] : 'Sesi 1';
                            const pass = (!isDapodikFormat && cols[5]) ? cols[5] : Math.random().toString(36).substring(2, 8).toUpperCase();

                            this.students.push({
                                nisn: nisn,
                                nik: nik,
                                name: name,
                                gender: gender,
                                class: cls,
                                birth_place: birthPlace,
                                birth_date: birthDate,
                                session: session,
                                room: room,
                                exam_user: nisn,
                                exam_pass: pass
                            });
                            addedCount++;
                        }
                    });

                    return addedCount;
                },
                processExcelPaste() {
                    if (!this.pasteRawText.trim()) {
                        alert('Mohon paste teks dari Excel terlebih dahulu.');
                        return;
                    }
                    const addedCount = this.parseRowsFromText(this.pasteRawText);
                    this.pasteRawText = '';
                    this.pasteModalOpen = false;
                    alert('Berhasil menambahkan ' + addedCount + ' data siswa!');
                },
                handleCsvUpload(event) {
                    const file = event.target.files[0];
                    if (!file) {
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const content = e.target.result || '';
                        const added = this.parseRowsFromText(content);
                        event.target.value = '';
                        alert('Berhasil mengimpor ' + added + ' siswa dari file CSV!');
                    };
                    reader.readAsText(file);
                },
                submitPrint() {
                    if (this.students.length === 0) {
                        alert('Belum ada data siswa untuk dicetak.');
                        return;
                    }
                    const form = document.getElementById('print_form');
                    form.querySelector('[name="card_type"]').value = this.cardType;
                    form.querySelector('[name="card_template"]').value = this.cardTemplate;
                    form.querySelector('[name="cards_per_page"]').value = this.cardsPerPage;
                    form.querySelector('[name="card_theme"]').value = this.cardTheme;
                    form.querySelector('[name="exam_title"]').value = this.examTitle;
                    form.querySelector('[name="academic_year"]').value = this.academicYear;
                    form.querySelector('[name="headmaster_name"]').value = this.headmasterName;
                    form.querySelector('[name="headmaster_nip"]').value = this.headmasterNip;
                    document.getElementById('students_json_input').value = JSON.stringify(this.students);
                    form.submit();
                }
            };
        };
    </script>

    <div
        x-data="cardStudioData()"
        class="space-y-6"
    >
        {{-- Header Banner --}}
        <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl border border-white/10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-brand-500/10 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-16 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl" aria-hidden="true"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-brand-300 border border-white/20">
                            <span>🪪</span> Generator Kartu Siswa &amp; Ujian
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Studio V2.0
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Cetak Kartu Peserta Ujian &amp; Kartu Pelajar
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Cetak kartu resmi berformat standar A4 (pilihan 8 atau 10 kartu per lembar). Tersedia 3 gaya template: Modern Minimalis, Formal Kedinasan, dan Cyber CBT Pro lengkap dengan barcode &amp; QR Code asli.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0 flex-wrap">
                    <button
                        type="button"
                        @click="pasteModalOpen = true"
                        class="inline-flex items-center gap-2 rounded-2xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/25 px-4 py-3 text-xs sm:text-sm font-bold text-white transition cursor-pointer"
                    >
                        <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>Paste Cepat Excel</span>
                    </button>

                    <button
                        type="button"
                        @click="submitPrint()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 px-5 py-3 text-xs sm:text-sm font-extrabold text-white shadow-xl shadow-emerald-500/25 transition active:scale-95 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Pratinjau A4 (<span x-text="students.length"></span> Siswa)</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Configuration Controls & Live Preview Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Left Controls Column --}}
            <div class="lg:col-span-5 rounded-[2rem] border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm space-y-6">
                <div class="pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>⚙️</span> Pengaturan Format &amp; Gaya
                    </h2>
                    <span class="text-[11px] font-bold text-slate-400">Card Studio</span>
                </div>

                {{-- 1. Card Purpose (Exam vs Student ID) --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Tujuan Kartu:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="cardType = 'exam'"
                            class="p-3 rounded-2xl border text-left transition cursor-pointer"
                            :class="cardType === 'exam' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 font-bold ring-2 ring-brand-500/20' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <p class="text-xs font-bold">📝 Kartu Ujian (CBT)</p>
                            <p class="text-[10px] mt-0.5 opacity-80">Akun CBT, ruang, sesi &amp; password</p>
                        </button>
                        <button
                            type="button"
                            @click="cardType = 'student'"
                            class="p-3 rounded-2xl border text-left transition cursor-pointer"
                            :class="cardType === 'student' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 font-bold ring-2 ring-brand-500/20' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <p class="text-xs font-bold">🪪 Kartu Pelajar</p>
                            <p class="text-[10px] mt-0.5 opacity-80">Identitas siswa, NISN &amp; perpustakaan</p>
                        </button>
                    </div>
                </div>

                {{-- 2. Card Template Style (3 Gorgeous Choices) --}}
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span>Pilih Template Visual:</span>
                        <span class="text-[10px] font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider" x-text="cardTemplate"></span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        {{-- Modern --}}
                        <button
                            type="button"
                            @click="cardTemplate = 'modern'"
                            class="p-2.5 rounded-2xl border text-center transition cursor-pointer flex flex-col items-center justify-between min-h-[75px]"
                            :class="cardTemplate === 'modern' ? 'border-brand-500 bg-brand-50/40 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 font-black ring-2 ring-brand-500/20' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <span class="text-base">🌟</span>
                            <span class="text-xs font-bold leading-tight">Modern Minimalis</span>
                            <span class="text-[9px] opacity-70">Gradien &amp; QR</span>
                        </button>

                        {{-- Formal --}}
                        <button
                            type="button"
                            @click="cardTemplate = 'formal'"
                            class="p-2.5 rounded-2xl border text-center transition cursor-pointer flex flex-col items-center justify-between min-h-[75px]"
                            :class="cardTemplate === 'formal' ? 'border-blue-500 bg-blue-50/40 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-black ring-2 ring-blue-500/20' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <span class="text-base">🏛️</span>
                            <span class="text-xs font-bold leading-tight">Formal Nasional</span>
                            <span class="text-[9px] opacity-70">Kop Garis Ganda</span>
                        </button>

                        {{-- Cyber CBT --}}
                        <button
                            type="button"
                            @click="cardTemplate = 'cbt'"
                            class="p-2.5 rounded-2xl border text-center transition cursor-pointer flex flex-col items-center justify-between min-h-[75px]"
                            :class="cardTemplate === 'cbt' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-black ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <span class="text-base">💻</span>
                            <span class="text-xs font-bold leading-tight">Cyber CBT Pro</span>
                            <span class="text-[9px] opacity-70">Box Akun Login</span>
                        </button>
                    </div>
                </div>

                {{-- 3. A4 Grid Density --}}
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Layout Kertas A4:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="cardsPerPage = 8"
                            class="p-2.5 rounded-2xl border text-left transition cursor-pointer"
                            :class="cardsPerPage === 8 ? 'border-brand-500 bg-brand-50/40 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <p class="text-xs font-bold">📄 8 Kartu / Lembar A4</p>
                            <p class="text-[10px] opacity-80">Ukuran lega 92 × 60 mm</p>
                        </button>
                        <button
                            type="button"
                            @click="cardsPerPage = 10"
                            class="p-2.5 rounded-2xl border text-left transition cursor-pointer"
                            :class="cardsPerPage === 10 ? 'border-brand-500 bg-brand-50/40 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400'"
                        >
                            <p class="text-xs font-bold">💳 10 Kartu / Lembar A4</p>
                            <p class="text-[10px] opacity-80">Ukuran dompet 86 × 54 mm</p>
                        </button>
                    </div>
                </div>

                {{-- 4. Text Configuration --}}
                <div class="space-y-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div x-show="cardType === 'exam'">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Judul Asesmen / Ujian:</label>
                        <input
                            type="text"
                            x-model="examTitle"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-800 dark:text-slate-100"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Tahun Ajaran:</label>
                            <input
                                type="text"
                                x-model="academicYear"
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-800 dark:text-slate-100"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Nama Kepala Sekolah:</label>
                            <input
                                type="text"
                                x-model="headmasterName"
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold text-slate-800 dark:text-slate-100"
                            >
                        </div>
                    </div>

                    <div x-show="cardTemplate === 'formal'">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">NIP Kepala Sekolah:</label>
                        <input
                            type="text"
                            x-model="headmasterNip"
                            placeholder="19780512 200312 1 002"
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2 text-xs font-semibold font-mono text-slate-800 dark:text-slate-100"
                        >
                    </div>
                </div>

                {{-- 5. Accent Theme Palette --}}
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Warna Aksen Kartu:</label>
                    <div class="flex items-center gap-2.5">
                        <template x-for="t in ['teal', 'blue', 'emerald', 'amber', 'rose', 'indigo', 'slate']" :key="t">
                            <button
                                type="button"
                                @click="cardTheme = t"
                                class="h-6 w-6 rounded-full border-2 transition-transform cursor-pointer"
                                :class="cardTheme === t ? 'scale-125 border-slate-900 dark:border-white shadow-md' : 'border-transparent opacity-80 hover:opacity-100'"
                                :style="{
                                    'teal': 'background:#0f766e',
                                    'blue': 'background:#1d4ed8',
                                    'emerald': 'background:#059669',
                                    'amber': 'background:#d97706',
                                    'rose': 'background:#e11d48',
                                    'indigo': 'background:#4338ca',
                                    'slate': 'background:#334155'
                                }[t]"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right Interactive Live Preview Column --}}
            <div class="lg:col-span-7 rounded-[2.5rem] border border-slate-200/90 dark:border-slate-800 bg-gradient-to-b from-slate-50/80 to-slate-100/60 dark:from-slate-900 dark:to-slate-950 p-6 sm:p-8 shadow-sm flex flex-col items-center justify-between min-h-[460px]">
                
                {{-- Preview Top Toolbar --}}
                <div class="w-full flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            Pratinjau Kartu Fisik
                        </p>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500">
                        <span class="px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700" x-text="cardTemplate === 'formal' ? 'Gaya Kedinasan' : (cardTemplate === 'cbt' ? 'Gaya Cyber CBT' : 'Gaya Modern')"></span>
                        <span class="hidden sm:inline">&bull; Skala 100%</span>
                    </div>
                </div>

                {{-- The Rendered Card Box (Dynamic Layout based on Template) --}}
                <div class="my-6 relative">
                    
                    {{-- Crop marks guide indicator around card --}}
                    <div class="absolute -top-3 -left-3 text-slate-400 font-mono text-[9px] select-none">┌</div>
                    <div class="absolute -top-3 -right-3 text-slate-400 font-mono text-[9px] select-none">┐</div>
                    <div class="absolute -bottom-3 -left-3 text-slate-400 font-mono text-[9px] select-none">└</div>
                    <div class="absolute -bottom-3 -right-3 text-slate-400 font-mono text-[9px] select-none">┘</div>

                    {{-- ================= TEMPLATE 1: MODERN MINIMALIS ================= --}}
                    <template x-if="cardTemplate === 'modern'">
                        <div class="w-[340px] sm:w-[410px] h-[245px] rounded-2xl border border-slate-300 dark:border-slate-700 shadow-2xl overflow-hidden relative flex flex-col justify-between bg-white text-slate-900">
                            {{-- Header --}}
                            <div
                                class="px-4 py-2.5 text-white flex items-center gap-3 transition-colors shadow-sm"
                                :style="{
                                    'teal': 'background:#0f766e',
                                    'blue': 'background:#1d4ed8',
                                    'emerald': 'background:#059669',
                                    'amber': 'background:#d97706',
                                    'rose': 'background:#e11d48',
                                    'indigo': 'background:#4338ca',
                                    'slate': 'background:#334155'
                                }[cardTheme] || 'background:#0f766e'"
                            >
                                <div class="h-9 w-9 rounded-xl bg-white/20 backdrop-blur-md p-1 flex items-center justify-center shrink-0 border border-white/20">
                                    @if ($school->logo_path)
                                        <img src="{{ asset('storage/' . $school->logo_path) }}" class="h-full w-full object-contain">
                                    @else
                                        <span class="font-black text-xs">SK</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 leading-tight">
                                    <p class="text-[9px] font-black uppercase tracking-wider opacity-90" x-text="cardType === 'exam' ? 'KARTU PESERTA ASESMEN (CBT)' : 'KARTU TANDA PELAJAR'"></p>
                                    <p class="text-xs font-black truncate">{{ $school->name }}</p>
                                    <p class="text-[9px] opacity-85 truncate" x-text="academicYear ? ('Tahun Ajaran ' + academicYear) : ''"></p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-white/20 border border-white/20 text-white" x-text="students[0]?.class || '10 IPA 1'"></span>
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="p-3.5 flex gap-3.5 flex-1 items-center bg-white relative">
                                {{-- Pas Foto --}}
                                <div class="h-24 w-20 rounded-xl bg-slate-100 border border-slate-300 flex flex-col items-center justify-center text-slate-400 shrink-0 shadow-inner">
                                    <span class="text-xl">👤</span>
                                    <span class="text-[8.5px] font-bold mt-1 text-slate-400">PAS FOTO</span>
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 space-y-1.5 text-[11px] leading-tight min-w-0">
                                    <div>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Nama Lengkap</p>
                                        <p class="font-extrabold text-slate-900 text-xs truncate" x-text="students[0]?.name || 'Ahmad Pratama Putra'"></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-[10px]">
                                        <div>
                                            <p class="text-[8.5px] text-slate-400 font-bold uppercase">NISN</p>
                                            <p class="font-bold text-slate-800 font-mono" x-text="students[0]?.nisn || '0081234567'"></p>
                                        </div>
                                        <div>
                                            <p class="text-[8.5px] text-slate-400 font-bold uppercase">Kelas</p>
                                            <p class="font-bold text-slate-800" x-text="students[0]?.class || '10 IPA 1'"></p>
                                        </div>
                                    </div>

                                    <div x-show="cardType === 'exam'" class="p-2 rounded-xl bg-slate-50 border border-slate-200/80 grid grid-cols-2 gap-1.5 text-[10px]">
                                        <div>
                                            <span class="text-slate-400 font-semibold block text-[8px] uppercase">User CBT:</span>
                                            <span class="font-bold font-mono text-slate-800 text-[10px]" x-text="students[0]?.exam_user || '0081234567'"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 font-semibold block text-[8px] uppercase">Password:</span>
                                            <span class="font-bold font-mono text-rose-600 text-[10px]" x-text="students[0]?.exam_pass || 'PRATAMA26'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Barcode & QR Code --}}
                            <div class="bg-slate-50 px-4 py-1.5 border-t border-slate-200 flex items-center justify-between text-[10px]">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-slate-600 font-bold tracking-widest text-[9px]">||||| | |||| ||| |||</span>
                                    <span class="text-[8px] text-slate-400 font-mono" x-text="'*' + (students[0]?.nisn || '0081234567') + '*'"></span>
                                </div>
                                <span class="font-bold text-slate-700 text-[9px]" x-text="headmasterName"></span>
                            </div>
                        </div>
                    </template>

                    {{-- ================= TEMPLATE 2: FORMAL KEDINASAN ================= --}}
                    <template x-if="cardTemplate === 'formal'">
                        <div class="w-[340px] sm:w-[410px] h-[245px] rounded-lg border-2 border-slate-800 shadow-2xl overflow-hidden relative flex flex-col justify-between bg-white text-slate-900">
                            {{-- Formal Kop Surat with Double Line --}}
                            <div class="px-3 pt-2 pb-1 text-center border-b-[3px] border-double border-slate-900 bg-white">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="h-8 w-8 rounded p-0.5 flex items-center justify-center shrink-0">
                                        @if ($school->logo_path)
                                            <img src="{{ asset('storage/' . $school->logo_path) }}" class="h-full w-full object-contain">
                                        @else
                                            <span class="font-black text-[10px]">SK</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 leading-none">
                                        <p class="text-[8.5px] font-bold uppercase tracking-wider text-slate-600">PEMERINTAH DAERAH PROVINSI</p>
                                        <p class="text-[11px] font-black uppercase text-slate-900 mt-0.5">{{ $school->name }}</p>
                                        <p class="text-[7.5px] text-slate-500 mt-0.5 truncate" x-text="cardType === 'exam' ? examTitle : ('TAHUN PELAJARAN ' + academicYear)"></p>
                                    </div>
                                    <div class="h-8 w-8 rounded p-0.5 flex items-center justify-center shrink-0 text-slate-700 font-bold text-xs">
                                        🏛️
                                    </div>
                                </div>
                            </div>

                            {{-- Title Header --}}
                            <div class="text-center py-0.5 bg-slate-100 border-b border-slate-200">
                                <span class="text-[9px] font-black tracking-widest uppercase text-slate-800 underline" x-text="cardType === 'exam' ? 'KARTU TANDA PESERTA UJIAN' : 'KARTU TANDA PELAJAR'"></span>
                            </div>

                            {{-- Body Table --}}
                            <div class="px-3 py-2 flex gap-3 flex-1 items-center">
                                <div class="h-24 w-18 border-2 border-slate-800 bg-rose-50 flex flex-col items-center justify-center text-slate-500 shrink-0 text-center p-1">
                                    <span class="text-sm">📷</span>
                                    <span class="text-[7.5px] font-bold mt-1">FOTO 2×3</span>
                                    <span class="text-[7px] text-slate-400">RESMI</span>
                                </div>
                                <div class="flex-1 text-[10px] space-y-1">
                                    <div class="flex items-center">
                                        <span class="w-16 font-bold text-slate-600">Nama</span>
                                        <span class="mr-1">:</span>
                                        <span class="font-black text-slate-900 truncate" x-text="students[0]?.name || 'Ahmad Pratama Putra'"></span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-16 font-bold text-slate-600">NISN / NIK</span>
                                        <span class="mr-1">:</span>
                                        <span class="font-mono font-bold text-slate-800" x-text="(students[0]?.nisn || '0081234567') + ' / ' + (students[0]?.nik || '320101080001')"></span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-16 font-bold text-slate-600">Kelas</span>
                                        <span class="mr-1">:</span>
                                        <span class="font-bold text-slate-800" x-text="students[0]?.class || '10 IPA 1'"></span>
                                    </div>
                                    <div class="flex items-center" x-show="cardType === 'exam'">
                                        <span class="w-16 font-bold text-slate-600">Sesi &bull; Ruang</span>
                                        <span class="mr-1">:</span>
                                        <span class="font-bold text-slate-800" x-text="(students[0]?.session || 'Sesi 1') + ' • ' + (students[0]?.room || 'Ruang 1')"></span>
                                    </div>
                                    <div class="flex items-center" x-show="cardType === 'exam'">
                                        <span class="w-16 font-bold text-slate-600">User / Sandi</span>
                                        <span class="mr-1">:</span>
                                        <span class="font-mono font-bold text-rose-700" x-text="(students[0]?.exam_user || '0081234567') + ' / ' + (students[0]?.exam_pass || 'PASS26')"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Formal Signature Footer --}}
                            <div class="px-3 py-1.5 border-t border-slate-300 flex items-center justify-between text-[8px] bg-white">
                                <div class="font-mono tracking-widest text-[8px] text-slate-600">*{{ $school->npsn ?? 'NPSN' }}*</div>
                                <div class="text-right leading-tight">
                                    <span class="text-slate-500">Kepala Sekolah,</span><br>
                                    <strong class="text-slate-900" x-text="headmasterName"></strong><br>
                                    <span class="text-[7.5px] text-slate-500" x-text="'NIP. ' + headmasterNip"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ================= TEMPLATE 3: CYBER CBT PRO ================= --}}
                    <template x-if="cardTemplate === 'cbt'">
                        <div class="w-[340px] sm:w-[410px] h-[245px] rounded-2xl border-2 border-emerald-500 shadow-2xl overflow-hidden relative flex flex-col justify-between bg-slate-950 text-white">
                            {{-- Watermark background --}}
                            <div class="absolute inset-0 opacity-5 flex items-center justify-center pointer-events-none">
                                <span class="text-8xl font-black">CBT</span>
                            </div>

                            {{-- Tech Header --}}
                            <div class="px-4 py-2 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                    <div>
                                        <p class="text-[8.5px] font-black uppercase tracking-widest text-emerald-200">PORTAL CBT ONLINE</p>
                                        <p class="text-xs font-black truncate max-w-[190px]">{{ $school->name }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-black/30 border border-white/20 text-white" x-text="academicYear"></span>
                            </div>

                            {{-- Tech Body with Highlighted Account Box --}}
                            <div class="p-3.5 flex gap-3.5 flex-1 items-center relative z-10">
                                {{-- Pas Foto with Tech Border --}}
                                <div class="h-24 w-20 rounded-xl bg-slate-900 border-2 border-emerald-500/50 flex flex-col items-center justify-center text-emerald-400 shrink-0">
                                    <span class="text-xl">⚡</span>
                                    <span class="text-[8px] font-mono font-bold mt-1 text-emerald-400">PESERTA</span>
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 space-y-1.5 min-w-0">
                                    <div>
                                        <p class="text-[8px] text-emerald-400 font-mono font-bold uppercase tracking-wider">NAMA PESERTA ASESMEN</p>
                                        <p class="font-black text-white text-xs truncate" x-text="students[0]?.name || 'Ahmad Pratama Putra'"></p>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px]">
                                        <span class="font-mono text-slate-300" x-text="'NISN: ' + (students[0]?.nisn || '0081234567')"></span>
                                        <span class="text-emerald-500">&bull;</span>
                                        <span class="font-bold text-white" x-text="students[0]?.class || '10 IPA 1'"></span>
                                    </div>

                                    {{-- High Contrast Login Box --}}
                                    <div class="p-2 rounded-xl bg-emerald-950/80 border border-emerald-500/60 shadow-lg grid grid-cols-2 gap-2 text-[10px]">
                                        <div>
                                            <span class="text-[8px] text-emerald-300 font-mono block">USERNAME LOGIN:</span>
                                            <span class="font-mono font-black text-white text-[11px]" x-text="students[0]?.exam_user || '0081234567'"></span>
                                        </div>
                                        <div>
                                            <span class="text-[8px] text-emerald-300 font-mono block">TOKEN / SANDI:</span>
                                            <span class="font-mono font-black text-[#fecf59] text-[11px]" x-text="students[0]?.exam_pass || 'PRATAMA26'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tech Footer --}}
                            <div class="bg-slate-900/90 px-4 py-1.5 border-t border-slate-800 flex items-center justify-between text-[9px] font-mono text-slate-400">
                                <span class="text-emerald-400" x-text="(students[0]?.session || 'Sesi 1') + ' &bull; ' + (students[0]?.room || 'Lab 1')"></span>
                                <span class="text-slate-400">STATUS: TERDAFTAR RESMI</span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Bottom Quick Stats --}}
                <div class="w-full flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <span>Target Cetak: <strong class="text-slate-800 dark:text-white" x-text="students.length"></strong> Siswa</span>
                    <span>Estimasi: <strong class="text-slate-800 dark:text-white" x-text="Math.ceil(students.length / cardsPerPage)"></strong> Lembar A4</span>
                </div>
            </div>
        </div>

        {{-- Students Management Table --}}
        <div class="rounded-[2.5rem] border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📋</span> Daftar Siswa yang Akan Dicetak (<span x-text="students.length"></span> Siswa)
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Tambah siswa secara manual atau gunakan fitur Paste Cepat dari Excel.
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-50 dark:bg-brand-950/60 text-xs font-bold text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/80 hover:bg-brand-100 transition cursor-pointer">
                        <span>📂 Upload CSV</span>
                        <input type="file" accept=".csv,.txt" class="hidden" @change="handleCsvUpload($event)">
                    </label>
                    <button
                        type="button"
                        @click="pasteModalOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-xs font-bold text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80 hover:bg-emerald-100 transition cursor-pointer"
                    >
                        <span>📋 Paste Cepat Excel</span>
                    </button>
                    <a
                        href="{{ route('school.settings.template-dapodik', 'siswa') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition"
                    >
                        <span>📥 Unduh Template CSV</span>
                    </a>
                    <button
                        type="button"
                        @click="clearAllStudents()"
                        x-show="students.length > 0"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-xs font-bold text-rose-700 dark:text-rose-300 hover:bg-rose-100 transition cursor-pointer"
                    >
                        <span>Kosongkan</span>
                    </button>
                </div>
            </div>

            {{-- Quick Add Single Form --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input
                    type="text"
                    x-model="newStudent.name"
                    placeholder="Nama Lengkap Siswa..."
                    class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-medium"
                >
                <input
                    type="text"
                    x-model="newStudent.nisn"
                    placeholder="NISN (10 digit)..."
                    class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-medium font-mono"
                >
                <input
                    type="text"
                    x-model="newStudent.class"
                    placeholder="Kelas (mis. 10 IPA 1)..."
                    class="rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-medium"
                >
                <button
                    type="button"
                    @click="addStudent()"
                    class="rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs py-2 px-4 shadow-sm transition cursor-pointer"
                >
                    + Tambah ke Daftar
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-800">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/80 text-slate-500 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">No</th>
                            <th class="py-2.5 px-3">NISN</th>
                            <th class="py-2.5 px-3">Nama Siswa</th>
                            <th class="py-2.5 px-3">Kelas</th>
                            <th class="py-2.5 px-3">Sesi / Ruang</th>
                            <th class="py-2.5 px-3">User &amp; Sandi CBT</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="(s, index) in students" :key="index">
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                <td class="py-2.5 px-3 font-bold" x-text="index + 1"></td>
                                <td class="py-2.5 px-3 font-mono font-semibold" x-text="s.nisn"></td>
                                <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white" x-text="s.name"></td>
                                <td class="py-2.5 px-3" x-text="s.class"></td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-500" x-text="(s.session || 'Sesi 1') + ' • ' + (s.room || 'Lab 1')"></td>
                                <td class="py-2.5 px-3 font-mono text-[11px]">
                                    <span x-text="s.exam_user || s.nisn"></span> / <span class="text-rose-600 font-bold" x-text="s.exam_pass || 'PASS'"></span>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <button
                                        type="button"
                                        @click="removeStudent(index)"
                                        class="text-rose-600 hover:text-rose-700 font-bold text-xs cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Excel Paste Modal Dialog --}}
        <div
            x-show="pasteModalOpen"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        >
            <div
                @click.outside="pasteModalOpen = false"
                class="w-full max-w-2xl rounded-3xl bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4"
            >
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📋</span> Paste Cepat Data Siswa dari Excel
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Salin (Ctrl+C) kolom dari Excel lalu tempelkan (Ctrl+V) langsung ke kotak di bawah.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="pasteModalOpen = false"
                        class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 text-xs text-amber-800 dark:text-amber-300">
                    <p class="font-bold">Urutan Kolom Rekomendasi di Excel:</p>
                    <p class="font-mono mt-1 text-[11px]">Kolom 1: NISN | Kolom 2: Nama Siswa | Kolom 3: Kelas | Kolom 4: Ruang | Kolom 5: Sesi | Kolom 6: Password CBT</p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1.5">
                        Tempelkan Teks Hasil Copy Excel di Sini:
                    </label>
                    <textarea
                        x-model="pasteRawText"
                        rows="8"
                        placeholder="Contoh:&#10;0081234567	Ahmad Pratama	10 IPA 1	Lab 1	Sesi 1	PRATAMA26&#10;0081234568	Nurul Hidayah	10 IPA 1	Lab 1	Sesi 1	NURUL26"
                        class="w-full rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-xs font-mono text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button
                        type="button"
                        @click="pasteModalOpen = false"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="processExcelPaste()"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md transition cursor-pointer"
                    >
                        Proses &amp; Tambahkan ke Tabel
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden Form for Print Submission --}}
        <form id="print_form" method="POST" action="{{ route('school.cards.print') }}" target="_blank" class="hidden">
            @csrf
            <input type="hidden" name="card_type" :value="cardType">
            <input type="hidden" name="card_template" :value="cardTemplate">
            <input type="hidden" name="cards_per_page" :value="cardsPerPage">
            <input type="hidden" name="card_theme" :value="cardTheme">
            <input type="hidden" name="exam_title" :value="examTitle">
            <input type="hidden" name="academic_year" :value="academicYear">
            <input type="hidden" name="headmaster_name" :value="headmasterName">
            <input type="hidden" name="headmaster_nip" :value="headmasterNip">
            <input type="hidden" id="students_json_input" name="students_json">
        </form>
    </div>
</x-app-layout>
