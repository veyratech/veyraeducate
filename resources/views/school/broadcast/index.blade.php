<x-app-layout>
    <script>
        window.broadcastStudioData = function () {
            return {
                templates: [],
                selectedTemplateId: 'exam_schedule',
                title: 'Pengumuman Jadwal STS/SAS CBT Online',
                message: '',
                recipientType: 'parents',
                phone: '',
                sendMethod: 'wa_link',
                applyTemplate(tpl) {
                    this.selectedTemplateId = tpl.id;
                    this.title = tpl.title;
                    this.message = tpl.text;
                },
                loadFromLog(el) {
                    this.title = el.dataset.title || '';
                    if (el.dataset.message) {
                        this.message = el.dataset.message;
                    }
                    if (el.dataset.type) {
                        this.recipientType = el.dataset.type;
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                init() {
                    try {
                        this.templates = JSON.parse(this.$el.dataset.templates || '[]');
                    } catch (e) {
                        this.templates = [];
                    }
                    if (this.templates.length > 0) {
                        this.applyTemplate(this.templates[0]);
                    }
                }
            };
        };
    </script>

    <div
        x-data="broadcastStudioData()"
        data-templates="{{ json_encode($templates) }}"
        class="space-y-6"
    >
        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-900 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white font-bold">✓</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('info'))
            <div class="rounded-2xl border border-blue-200 bg-blue-50/90 p-4 text-blue-900 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-500 text-white font-bold">ℹ</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('info') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-900 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white font-bold">!</span>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Hero Banner with Dynamic School Theme --}}
        <div
            :class="'bg-gradient-to-r ' + (themeConfig[schoolTheme]?.hero || themeConfig.teal.hero)"
            class="relative overflow-hidden rounded-[2rem] p-6 sm:p-8 text-white shadow-xl shadow-black/10 transition-all duration-300"
        >
            <div class="pointer-events-none absolute -right-12 -top-12 h-64 w-64 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-10 h-44 w-44 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-2xl space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-white/20 backdrop-blur-md border border-white/20 text-white">
                        <span>💬</span> Otomasi &amp; Broadcast WhatsApp Sekolah
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Pusat Komunikasi &amp; Notifikasi Instan Sekolah
                    </h1>
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed">
                        Kirimkan jadwal ujian CBT, panduan token simulasi, pengumuman rapor, hingga surat edaran administrasi ke seluruh guru, siswa, dan wali murid dengan satu klik praktis.
                    </p>
                </div>

                {{-- Status Pill --}}
                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 flex flex-col gap-2 min-w-[240px]">
                    <span class="text-[11px] font-bold text-white/80 uppercase tracking-wider">Metode Pengiriman</span>
                    <div class="flex items-center gap-2">
                        <span class="flex h-3 w-3 rounded-full {{ $isGatewayActive ? 'bg-emerald-400 animate-pulse' : 'bg-amber-300' }}"></span>
                        <span class="text-xs sm:text-sm font-bold text-white">
                            {{ $isGatewayActive ? 'WhatsApp Gateway Aktif' : 'WhatsApp Web / Direct Mode' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-white/80">
                        {{ $isGatewayActive ? 'Pesan otomatis dikirim langsung oleh server.' : 'Membuka aplikasi WhatsApp resmi dengan pesan siap kirim.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Main Composer Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Left Column: Form & Presets (7 cols) --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- Quick Presets --}}
                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                        <span>⚡</span> Template Pengumuman Cepat
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <template x-for="tpl in templates" :key="tpl.id">
                            <button
                                type="button"
                                @click="applyTemplate(tpl)"
                                :class="selectedTemplateId === tpl.id ? 'border-brand-500 bg-brand-50/80 text-brand-700 ring-2 ring-brand-500/20 dark:bg-brand-950/40 dark:text-brand-300' : 'border-slate-200 hover:border-slate-300 bg-white text-slate-700 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-300'"
                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer"
                            >
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="tpl.category"></span>
                                <span class="text-xs font-bold mt-1 line-clamp-2" x-text="tpl.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Broadcast Form --}}
                <div class="rounded-[2rem] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <form method="POST" action="{{ route('school.broadcast.send') }}" class="space-y-4 text-xs">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Target Audiens:</label>
                                <select name="recipient_type" x-model="recipientType" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold dark:border-slate-700 dark:bg-slate-800">
                                    <option value="parents">Wali Murid / Orang Tua</option>
                                    <option value="teachers">Dewan Guru &amp; Pengawas</option>
                                    <option value="group_link">Grup WhatsApp Sekolah</option>
                                    <option value="single">Nomor Khusus Tertentu</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Pengiriman:</label>
                                <select name="send_method" x-model="sendMethod" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold dark:border-slate-700 dark:bg-slate-800">
                                    <option value="wa_link">Buka Aplikasi WhatsApp (Langsung)</option>
                                    @if ($isGatewayActive)
                                        <option value="gateway">Kirim Otomatis Server (Gateway API)</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div x-show="recipientType === 'single'" x-transition>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp Tujuan (Awali 08 atau 62, pisahkan koma jika lebih dari satu):</label>
                            <input
                                type="text"
                                name="phone"
                                x-model="phone"
                                placeholder="Contoh: 081234567890, 081398765432"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono dark:border-slate-700 dark:bg-slate-800"
                            >
                        </div>

                        <div x-show="recipientType === 'teachers'" x-transition class="rounded-xl border border-brand-200/80 dark:border-slate-700 bg-brand-50/50 dark:bg-slate-800/60 p-3 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    👥 Kontak Guru &amp; Staf Terdaftar ({{ isset($teacherContacts) ? $teacherContacts->count() : 0 }} Nomor)
                                </span>
                                @if (isset($teacherContacts) && $teacherContacts->isNotEmpty())
                                    <button
                                        type="button"
                                        data-all-phones="{{ $teacherContacts->pluck('phone')->implode(', ') }}"
                                        @click="phone = $el.dataset.allPhones; recipientType = 'single'"
                                        class="text-[11px] font-bold text-brand-700 hover:text-brand-800 dark:text-brand-400 underline cursor-pointer"
                                    >
                                        Pilih Semua ke Input Nomor
                                    </button>
                                @endif
                            </div>
                            @if (isset($teacherContacts) && $teacherContacts->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($teacherContacts as $contact)
                                        <button
                                            type="button"
                                            data-phone="{{ $contact->phone }}"
                                            @click="phone = $el.dataset.phone; recipientType = 'single'"
                                            class="inline-flex items-center gap-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-2.5 py-1 text-[11px] font-medium text-slate-700 dark:text-slate-300 hover:border-brand-400 transition cursor-pointer"
                                            title="Klik untuk kirim ke nomor ini"
                                        >
                                            <span class="font-semibold">{{ $contact->name }}</span>
                                            <span class="font-mono text-slate-400">({{ $contact->phone }})</span>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Belum ada nomor telepon staf/guru yang disimpan di <a href="{{ route('school.settings', ['tab' => 'tim']) }}" class="font-bold text-brand-700 dark:text-brand-400 underline">Manajemen Tim</a>.
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul / Subjek Pengumuman:</label>
                            <input
                                type="text"
                                name="title"
                                x-model="title"
                                required
                                placeholder="Masukkan judul pengumuman..."
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300">Isi Pesan WhatsApp:</label>
                                <span class="text-[11px] text-slate-400">Gunakan *tebal* atau _miring_ untuk format teks</span>
                            </div>
                            <textarea
                                name="message"
                                x-model="message"
                                rows="10"
                                required
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-mono text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 leading-relaxed focus:ring-1 focus:ring-brand-500"
                                placeholder="Ketik isi pesan WhatsApp di sini..."
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] text-slate-500">
                                Pesan siap dikirim atas nama <strong>{{ $school->name }}</strong>
                            </span>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.699c.971.53 1.838.814 2.8.814 3.18 0 5.768-2.587 5.768-5.766.001-3.182-2.585-5.768-5.772-5.768zm7.399 5.767c-.001 4.08-3.32 7.397-7.399 7.397-1.264 0-2.457-.323-3.5-.929l-4.531 1.189 1.21-4.417c-.675-1.085-1.037-2.339-1.036-3.64.001-4.08 3.32-7.397 7.4-7.397 4.08.001 7.399 3.32 7.399 7.397z"/>
                                </svg>
                                <span>Kirim Pesan Sekarang</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Column: Live Smartphone WhatsApp Chat Mockup (5 cols) --}}
            <div class="lg:col-span-5 sticky top-6">
                <div class="rounded-[2.5rem] border-8 border-slate-900 bg-slate-900 p-2 shadow-2xl overflow-hidden max-w-[360px] mx-auto">
                    {{-- Phone Speaker & Camera Notch --}}
                    <div class="h-4 flex items-center justify-center">
                        <div class="h-3 w-28 bg-slate-800 rounded-full"></div>
                    </div>

                    {{-- WhatsApp Header --}}
                    <div class="bg-[#075e54] text-white p-3 rounded-t-2xl flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold text-white">
                            🏫
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold truncate">{{ $school->name }}</h4>
                            <p class="text-[10px] text-emerald-200">Official School Channel</p>
                        </div>
                    </div>

                    {{-- WhatsApp Chat Screen Body --}}
                    <div class="bg-[#ece5dd] p-3 min-h-[380px] max-h-[460px] overflow-y-auto space-y-3 flex flex-col justify-end text-xs">
                        {{-- Security notice pill --}}
                        <div class="text-center">
                            <span class="inline-block bg-[#fedc9a]/60 text-[#553c07] text-[9px] px-2.5 py-1 rounded-lg font-medium shadow-2xs">
                                🔒 Pesan dan panggilan terenkripsi secara end-to-end.
                            </span>
                        </div>

                        {{-- Sent WhatsApp Bubble --}}
                        <div class="self-end max-w-[90%] bg-[#dcf8c6] rounded-2xl rounded-tr-xs p-3 shadow-sm text-slate-800 space-y-1">
                            <p class="font-bold text-[11px] text-[#075e54] border-b border-emerald-600/20 pb-1" x-text="title || 'Judul Pengumuman'"></p>
                            <p class="whitespace-pre-wrap text-[11px] leading-relaxed text-slate-800" x-text="message || 'Ketik pesan Anda untuk melihat tampilan pratinjau...'"></p>
                            <div class="text-right text-[9px] text-slate-400 flex items-center justify-end gap-1">
                                <span>{{ now()->format('H:i') }}</span>
                                <span class="text-sky-600 font-bold">✓✓</span>
                            </div>
                        </div>
                    </div>

                    {{-- Mock Input Footer --}}
                    <div class="bg-slate-100 p-2 rounded-b-2xl flex items-center gap-2 text-slate-400">
                        <div class="flex-1 bg-white rounded-full px-3 py-1.5 text-[10px] text-slate-400 border border-slate-200">
                            Pesan WhatsApp...
                        </div>
                        <div class="h-7 w-7 rounded-full bg-[#128c7e] text-white flex items-center justify-center text-xs">
                            ➤
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Broadcast Logs --}}
        <div class="rounded-[2rem] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋</span> Riwayat Broadcast Terkirim
                </h3>
                @if (! empty($recentBroadcasts))
                    <form method="POST" action="{{ route('school.broadcast.clear') }}" onsubmit="return confirm('Bersihkan seluruh riwayat broadcast sekolah?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/30 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:text-rose-300 hover:bg-rose-100 transition cursor-pointer">
                            Bersihkan Riwayat
                        </button>
                    </form>
                @endif
            </div>

            @if (empty($recentBroadcasts))
                <div class="rounded-2xl border-2 border-dashed border-slate-200 p-6 text-center text-slate-400 text-xs dark:border-slate-800">
                    Belum ada riwayat pesan yang dikirimkan. Gunakan formulir di atas untuk mengirimkan broadcast pertama sekolah Anda.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 font-bold dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-300">
                                <th class="px-3.5 py-2.5 rounded-l-xl">Tanggal</th>
                                <th class="px-3.5 py-2.5">Judul Pengumuman</th>
                                <th class="px-3.5 py-2.5">Tujuan</th>
                                <th class="px-3.5 py-2.5">Metode</th>
                                <th class="px-3.5 py-2.5">Status</th>
                                <th class="px-3.5 py-2.5 rounded-r-xl text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach (array_reverse($recentBroadcasts) as $log)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                    <td class="px-3.5 py-2.5 font-mono text-slate-500">{{ $log['date'] ?? '-' }}</td>
                                    <td class="px-3.5 py-2.5 font-bold text-slate-900 dark:text-white">{{ $log['title'] ?? '-' }}</td>
                                    <td class="px-3.5 py-2.5 text-slate-600 dark:text-slate-400">{{ $log['recipient'] ?? '-' }}</td>
                                    <td class="px-3.5 py-2.5 text-slate-500 font-mono text-[11px]">{{ $log['method'] ?? '-' }}</td>
                                    <td class="px-3.5 py-2.5 font-bold text-emerald-600 dark:text-emerald-400">{{ $log['status'] ?? 'Selesai' }}</td>
                                    <td class="px-3.5 py-2.5 text-right">
                                        <button
                                            type="button"
                                            data-title="{{ $log['title'] ?? '' }}"
                                            data-message="{{ $log['message'] ?? '' }}"
                                            data-type="{{ $log['type'] ?? 'parents' }}"
                                            @click="loadFromLog($el)"
                                            class="text-xs font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 cursor-pointer"
                                        >
                                            Muat Ulang
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
