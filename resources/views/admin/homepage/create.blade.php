<x-admin-layout title="Tambah Container Homepage — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.homepage.index') }}" class="btn-minimal-ghost p-1.5 text-muted hover:text-ink">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <p class="eyebrow">Container Baru</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Tambah Container Baru ke Homepage</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        sectionType: '{{ old('section_type', 'custom_banner') }}',
        faqItems: [{ q: '', a: '' }],
        featureItems: [{ title: '', body: '', accent: 'brand', image_url: '' }],
        testimonials: [{ name: '', role: '', school: '', quote: '', rating: 5, avatar_url: '' }],
        appItems: [{ name: '', category: 'Akademik', description: '', status_badge: 'Siap Pakai', accent: 'brand', tags: '', icon: 'academic-cap', image_url: '' }],
        addFaq() { this.faqItems.push({ q: '', a: '' }); },
        removeFaq(idx) { if (this.faqItems.length > 1) this.faqItems.splice(idx, 1); },
        addFeature() { this.featureItems.push({ title: '', body: '', accent: 'brand', image_url: '' }); },
        removeFeature(idx) { if (this.featureItems.length > 1) this.featureItems.splice(idx, 1); },
        addTestimonial() { this.testimonials.push({ name: '', role: '', school: '', quote: '', rating: 5, avatar_url: '' }); },
        removeTestimonial(idx) { if (this.testimonials.length > 1) this.testimonials.splice(idx, 1); },
        addApp() { this.appItems.push({ name: '', category: 'Akademik', description: '', status_badge: 'Siap Pakai', accent: 'brand', tags: '', icon: 'academic-cap', image_url: '' }); },
        removeApp(idx) { if (this.appItems.length > 1) this.appItems.splice(idx, 1); }
    }" x-init="trustItems = [{title:'',desc:'',color:'brand'}]; workflowSteps = [{title:'',body:'',image_url:''}]; ecosystemItems = [{title:'',body:'',accent:'brand'}];">
        <div class="container-site max-w-4xl space-y-6">
            <form action="{{ route('admin.homepage.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Pengaturan Utama Container --}}
                <div class="card-minimal p-6 space-y-5">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        1. Informasi Dasar Container
                    </h3>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="form-label" for="title">Judul / Label Container (untuk panel admin) <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title') }}"
                                required
                                placeholder="Contoh: Banner Promo Awal Tahun / Fitur Modul Ujian"
                                class="form-input-minimal"
                            >
                            @error('title')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="section_type">Jenis Container <span class="text-red-500">*</span></label>
                            <select
                                name="section_type"
                                id="section_type"
                                x-model="sectionType"
                                required
                                class="form-input-minimal"
                            >
                                <option value="custom_banner">📢 Banner Promo / Pengumuman</option>
                                <option value="custom_content">📝 Container Bebas (Teks & Gambar)</option>
                                <option value="features">✨ Daftar Fitur Tambahan</option>
                                <option value="app_showcase">🚀 Showcase Aplikasi Sekolah</option>
                                <option value="testimonials">⭐ Testimoni & Ulasan Sekolah</option>
                                <option value="faq">❓ Daftar Tanya Jawab (FAQ)</option>
                                <option value="cta">🎯 Blok Ajakan (Call to Action)</option>
                                <option value="trust_strip">🛡️ Baris 3 Pilar Info / Kepercayaan</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" for="order_position">Nomor Urutan Tampil</label>
                            <input
                                type="number"
                                name="order_position"
                                id="order_position"
                                value="{{ old('order_position', $nextOrder) }}"
                                min="1"
                                class="form-input-minimal"
                            >
                            <p class="text-xs text-muted mt-1">Urutan 1 tampil paling atas setelah header.</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                checked
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4"
                            >
                            <span class="text-sm font-semibold text-ink dark:text-slate-200">Langsung Aktifkan dan Tampilkan di Web Depan</span>
                        </label>
                    </div>
                </div>

                {{-- FORM DINAMIS SESUAI TIPE CONTAINER --}}

                {{-- TIPE 1: CUSTOM BANNER --}}
                <div x-show="sectionType === 'custom_banner'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Banner Promo / Pengumuman
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge Kecil (Pill Text)</label>
                            <input type="text" name="badge" placeholder="Contoh: Promo Spesial / Baru" class="form-input-minimal">
                        </div>

                        <div>
                            <label class="form-label">Warna Tema Latar</label>
                            <select name="theme" class="form-input-minimal">
                                <option value="brand">Biru Veyra (Brand)</option>
                                <option value="dark">Dark Gradient (Elegan Gelap)</option>
                                <option value="emerald">Hijau (Fresh / Promo)</option>
                                <option value="gold">Emas / Amber (Eksklusif)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Judul Utama Banner <span class="text-red-500">*</span></label>
                            <input type="text" name="title_text" placeholder="Contoh: Diskon 30% untuk 10 Sekolah Pertama Bulan Ini" class="form-input-minimal">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Deskripsi / Penjelasan Singkat</label>
                            <textarea name="subtitle" rows="2" placeholder="Jelaskan detail penawaran atau pengumuman Anda..." class="form-input-minimal"></textarea>
                        </div>

                        <div>
                            <label class="form-label">Teks Tombol Aksi</label>
                            <input type="text" name="btn_text" placeholder="Contoh: Klaim Promo Sekarang" class="form-input-minimal">
                        </div>

                        <div>
                            <label class="form-label">Link Tombol Aksi</label>
                            <input type="text" name="btn_url" placeholder="Contoh: /register atau https://wa.me/..." class="form-input-minimal">
                        </div>

                        <div class="sm:col-span-2 border-t border-slate-100 dark:border-slate-800 pt-3 space-y-3">
                            <label class="form-label">Foto Model / Grafis Promo (Opsional)</label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs text-muted block mb-1">Upload File Foto/Grafis</label>
                                    <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                </div>
                                <div>
                                    <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                    <input type="text" name="image_url_manual" placeholder="https://..." class="form-input-minimal text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TIPE 2: CUSTOM CONTENT --}}
                <div x-show="sectionType === 'custom_content'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Container Bebas (Teks & Gambar)
                    </h3>

                    <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge Kecil</label>
                                <input type="text" name="badge" placeholder="Contoh: Keunggulan" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Tema Latar Belakang</label>
                                <select name="theme" class="form-input-minimal">
                                    <option value="light">Terang (Putih / Slate)</option>
                                    <option value="slate">Abu Lembut (Slate 50)</option>
                                    <option value="dark">Gelap (Dark Mode)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" placeholder="Contoh: Dirancang Khusus untuk Sekolah Modern Indonesia" class="form-input-minimal">
                        </div>

                        <div>
                            <label class="form-label">Subjudul / Paragraf Pengantar</label>
                            <input type="text" name="subtitle" placeholder="Penjelasan satu atau dua kalimat..." class="form-input-minimal">
                        </div>

                        <div>
                            <label class="form-label">Isi Konten Penjelasan</label>
                            <textarea name="body_html" rows="4" placeholder="Tuliskan penjelasan detail atau deskripsi produk di sini..." class="form-input-minimal"></textarea>
                        </div>

                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-3">
                            <label class="form-label">Foto Model / Ilustrasi Container (Opsional)</label>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="text-xs text-muted block mb-1">Upload File Foto Baru</label>
                                    <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                </div>
                                <div>
                                    <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                    <input type="text" name="image_url_manual" placeholder="https://..." class="form-input-minimal text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-muted block mb-1">Posisi Foto</label>
                                    <select name="image_position" class="form-input-minimal text-sm">
                                        <option value="right">Kanan Teks</option>
                                        <option value="left">Kiri Teks</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Teks Tombol (Opsional)</label>
                                <input type="text" name="btn_text" placeholder="Contoh: Pelajari Selengkapnya" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Link Tombol (Opsional)</label>
                                <input type="text" name="btn_url" placeholder="Contoh: /fitur atau https://..." class="form-input-minimal">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TIPE 3: FEATURES --}}
                <div x-show="sectionType === 'features'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Daftar Fitur Tambahan
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="Fitur" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" placeholder="Contoh: Fitur Unggulan Terbaru" class="form-input-minimal">
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="form-label">Daftar Kartu Fitur</label>
                        <template x-for="(feat, fIdx) in featureItems" :key="fIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Kartu #' + (fIdx + 1)"></span>
                                    <button type="button" @click="removeFeature(fIdx)" class="text-xs text-red-600 hover:underline" x-show="featureItems.length > 1">Hapus</button>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div class="sm:col-span-2">
                                        <input type="text" :name="'items[' + fIdx + '][title]'" x-model="feat.title" placeholder="Judul fitur..." class="form-input-minimal text-sm">
                                    </div>
                                    <div>
                                        <select :name="'items[' + fIdx + '][accent]'" x-model="feat.accent" class="form-input-minimal text-sm">
                                            <option value="brand">Biru (Brand)</option>
                                            <option value="gold">Kuning (Gold)</option>
                                            <option value="sky">Biru Muda (Sky)</option>
                                            <option value="teal">Hijau Toska (Teal)</option>
                                            <option value="amber">Amber (Oranye)</option>
                                            <option value="emerald">Hijau (Emerald)</option>
                                        </select>
                                    </div>
                                </div>
                                <textarea :name="'items[' + fIdx + '][body]'" x-model="feat.body" rows="2" placeholder="Deskripsi fitur..." class="form-input-minimal text-sm"></textarea>
                                <div class="grid gap-3 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Foto / Ilustrasi Kartu (URL)</label>
                                        <input type="text" :name="'items[' + fIdx + '][image_url]'" x-model="feat.image_url" placeholder="https://..." class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Atau Upload Gambar Kartu</label>
                                        <input type="file" :name="'item_images[' + fIdx + ']'" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="addFeature()" class="btn-minimal-secondary text-xs">
                            + Tambah Kartu Fitur
                        </button>
                    </div>
                </div>

                {{-- TIPE APP SHOWCASE --}}
                <div x-show="sectionType === 'app_showcase'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Showcase Katalog Aplikasi Sekolah
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="Katalog Aplikasi" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" value="Ragam Aplikasi Siap Tanam untuk Sekolah Anda" class="form-input-minimal">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Subjudul Penjelasan</label>
                            <textarea name="subtitle" rows="2" class="form-input-minimal" placeholder="Contoh: Satu wadah terintegrasi untuk berbagai kebutuhan digitalisasi sekolah."></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Sumber Data Aplikasi</label>
                            <select name="source_mode" class="form-input-minimal text-sm">
                                <option value="custom">Katalog Kurasi Manual (Gunakan daftar kartu di bawah)</option>
                                <option value="database">Otomatis dari Database App Hub (Tabel WebApps yang aktif)</option>
                            </select>
                            <p class="text-[11px] text-muted mt-1">Jika memilih otomatis, sistem akan menampilkan modul yang saat ini aktif di menu App Hub admin.</p>
                        </div>
                    </div>

                    <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <label class="form-label">Daftar Modul Aplikasi</label>
                        <template x-for="(app, aIdx) in appItems" :key="aIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Aplikasi #' + (aIdx + 1)"></span>
                                    <button type="button" @click="removeApp(aIdx)" class="text-xs text-red-600 hover:underline" x-show="appItems.length > 1">Hapus</button>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="lg:col-span-2">
                                        <label class="text-[11px] text-muted block mb-1">Nama Aplikasi</label>
                                        <input type="text" :name="'items[' + aIdx + '][name]'" x-model="app.name" placeholder="CBT & Ujian Online" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Kategori</label>
                                        <input type="text" :name="'items[' + aIdx + '][category]'" x-model="app.category" placeholder="Akademik" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Status Badge</label>
                                        <select :name="'items[' + aIdx + '][status_badge]'" x-model="app.status_badge" class="form-input-minimal text-xs">
                                            <option value="Siap Pakai">Siap Pakai</option>
                                            <option value="Bisa Ditanam">Bisa Ditanam</option>
                                            <option value="Segera Hadir">Segera Hadir</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Tema Warna Aksen</label>
                                        <select :name="'items[' + aIdx + '][accent]'" x-model="app.accent" class="form-input-minimal text-xs">
                                            <option value="brand">Biru (Brand)</option>
                                            <option value="emerald">Hijau (Emerald)</option>
                                            <option value="gold">Kuning (Gold)</option>
                                            <option value="amber">Oranye (Amber)</option>
                                            <option value="sky">Biru Muda (Sky)</option>
                                            <option value="teal">Toska (Teal)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Ikon Standar</label>
                                        <select :name="'items[' + aIdx + '][icon]'" x-model="app.icon" class="form-input-minimal text-xs">
                                            <option value="academic-cap">Wisuda / Akademik</option>
                                            <option value="clipboard">Clipboard / Presensi</option>
                                            <option value="document-text">Dokumen / Rapor</option>
                                            <option value="wallet">Dompet / Keuangan</option>
                                            <option value="book-open">Buku / Perpustakaan</option>
                                            <option value="user-group">Pengguna / Guru</option>
                                            <option value="app">Grid Aplikasi Umum</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Tag Fitur (Pisahkan koma)</label>
                                        <input type="text" :name="'items[' + aIdx + '][tags]'" x-model="app.tags" placeholder="Anti-Curang, Bank Soal" class="form-input-minimal text-xs">
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[11px] text-muted block mb-1">Deskripsi Singkat</label>
                                    <textarea :name="'items[' + aIdx + '][description]'" x-model="app.description" rows="2" placeholder="Jelaskan fungsi aplikasi untuk sekolah..." class="form-input-minimal text-xs"></textarea>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">URL Ikon Khusus (Opsional)</label>
                                        <input type="text" :name="'items[' + aIdx + '][image_url]'" x-model="app.image_url" placeholder="https://..." class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Atau Upload Gambar Ikon</label>
                                        <input type="file" :name="'item_images[' + aIdx + ']'" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addApp()" class="btn-minimal-secondary text-xs">
                            + Tambah Aplikasi
                        </button>
                    </div>
                </div>

                {{-- TIPE TESTIMONIALS --}}
                <div x-show="sectionType === 'testimonials'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Ulasan & Testimoni Sekolah
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="Testimoni" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" value="Dipercaya oleh puluhan sekolah & yayasan di Indonesia" class="form-input-minimal">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Subjudul Penjelasan</label>
                            <textarea name="subtitle" rows="2" class="form-input-minimal" placeholder="Contoh: Pengalaman nyata kepala sekolah dan staf IT yang merasakan efisiensi Veyra Educate."></textarea>
                        </div>
                    </div>

                    <div class="space-y-4 pt-2">
                        <label class="form-label">Daftar Testimoni Sekolah</label>
                        <template x-for="(item, tIdx) in testimonials" :key="tIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Ulasan #' + (tIdx + 1)"></span>
                                    <button type="button" @click="removeTestimonial(tIdx)" class="text-xs text-red-600 hover:underline" x-show="testimonials.length > 1">Hapus</button>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Nama Tokoh / Guru</label>
                                        <input type="text" :name="'testimonials[' + tIdx + '][name]'" x-model="item.name" placeholder="Drs. H. Mulyadi, M.Pd." class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Jabatan / Role</label>
                                        <input type="text" :name="'testimonials[' + tIdx + '][role]'" x-model="item.role" placeholder="Kepala Sekolah" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Nama Sekolah</label>
                                        <input type="text" :name="'testimonials[' + tIdx + '][school]'" x-model="item.school" placeholder="SMA Bina Bangsa" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Rating Bintang</label>
                                        <select :name="'testimonials[' + tIdx + '][rating]'" x-model="item.rating" class="form-input-minimal text-xs">
                                            <option value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
                                            <option value="4">⭐⭐⭐⭐ (4 Bintang)</option>
                                            <option value="3">⭐⭐⭐ (3 Bintang)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[11px] text-muted block mb-1">Kutipan Ulasan / Testimoni</label>
                                    <textarea :name="'testimonials[' + tIdx + '][quote]'" x-model="item.quote" rows="2" placeholder="Tuliskan kutipan ulasan atau pengalaman mereka..." class="form-input-minimal text-xs"></textarea>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Foto Profil / Avatar (URL)</label>
                                        <input type="text" :name="'testimonials[' + tIdx + '][avatar_url]'" x-model="item.avatar_url" placeholder="https://..." class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Atau Upload File Foto</label>
                                        <input type="file" :name="'testimonial_avatars[' + tIdx + ']'" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addTestimonial()" class="btn-minimal-secondary text-xs">
                            + Tambah Ulasan Sekolah
                        </button>
                    </div>
                </div>

                {{-- TIPE 4: FAQ --}}
                <div x-show="sectionType === 'faq'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Tanya Jawab (FAQ)
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="FAQ" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" value="Pertanyaan yang Sering Diajukan" class="form-input-minimal">
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="form-label">Daftar Pertanyaan & Jawaban</label>
                        <template x-for="(faq, fIdx) in faqItems" :key="fIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Tanya Jawab #' + (fIdx + 1)"></span>
                                    <button type="button" @click="removeFaq(fIdx)" class="text-xs text-red-600 hover:underline" x-show="faqItems.length > 1">Hapus</button>
                                </div>
                                <input type="text" :name="'items[' + fIdx + '][q]'" x-model="faq.q" placeholder="Pertanyaan..." class="form-input-minimal text-sm">
                                <textarea :name="'items[' + fIdx + '][a]'" x-model="faq.a" rows="2" placeholder="Jawaban..." class="form-input-minimal text-sm"></textarea>
                            </div>
                        </template>
                        <button type="button" @click="addFaq()" class="btn-minimal-secondary text-xs">
                            + Tambah Tanya Jawab
                        </button>
                    </div>
                </div>

                {{-- TIPE 5: CTA --}}
                <div x-show="sectionType === 'cta'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Blok Ajakan (Call to Action)
                    </h3>

                    <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="Siap mulai" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Ajakan</label>
                                <input type="text" name="title" placeholder="Siap memulai digitalisasi sekolah?" class="form-input-minimal">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Subjudul Penjelasan</label>
                            <textarea name="subtitle" rows="2" placeholder="Pendaftaran kurang dari 5 menit..." class="form-input-minimal"></textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Teks Tombol Utama</label>
                                <input type="text" name="btn_text" value="Daftar sekolah" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Link Tombol Utama</label>
                                <input type="text" name="btn_url" value="/register" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Teks Tombol WhatsApp</label>
                                <input type="text" name="wa_text" value="Konsultasi WhatsApp" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Nomor / Link WhatsApp</label>
                                <input type="text" name="wa_url" value="https://wa.me/6285707074778" class="form-input-minimal">
                            </div>
                        </div>

                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-3">
                            <label class="form-label">Foto Model / Grafis CTA (Opsional)</label>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="text-xs text-muted block mb-1">Upload File Foto/Grafis</label>
                                    <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                </div>
                                <div>
                                    <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                    <input type="text" name="image_url_manual" placeholder="https://..." class="form-input-minimal text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-muted block mb-1">Posisi Foto</label>
                                    <select name="image_position" class="form-input-minimal text-sm">
                                        <option value="right">Kanan Teks</option>
                                        <option value="left">Kiri Teks</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TIPE TRUST STRIP --}}
                <div x-show="sectionType === 'trust_strip'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Pilar Kepercayaan (Trust Strip)
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="Pilar Kepercayaan" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" value="Tanpa server lokal, akun aman, langganan jelas" class="form-input-minimal">
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="form-label">Daftar Pilar Kepercayaan</label>
                        <template x-for="(item, tIdx) in trustItems" :key="tIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Pilar #' + (tIdx + 1)"></span>
                                    <button type="button" @click="removeTrust(tIdx)" class="text-xs text-red-600 hover:underline" x-show="trustItems.length > 1">Hapus</button>
                                </div>
                                <input type="text" :name="'items[' + tIdx + '][title]'" x-model="item.title" placeholder="Judul pilar..." class="form-input-minimal text-sm">
                                <input type="text" :name="'items[' + tIdx + '][desc]'" x-model="item.desc" placeholder="Keterangan..." class="form-input-minimal text-sm">
                                <select :name="'items[' + tIdx + '][color]'" x-model="item.color" class="form-input-minimal text-xs">
                                    <option value="brand">Biru (Brand)</option>
                                    <option value="gold">Kuning (Gold)</option>
                                    <option value="emerald">Hijau (Emerald)</option>
                                    <option value="sky">Biru Muda (Sky)</option>
                                </select>
                            </div>
                        </template>
                        <button type="button" @click="addTrust()" class="btn-minimal-secondary text-xs">
                            + Tambah Pilar
                        </button>
                    </div>
                </div>

                {{-- TIPE WORKFLOW --}}
                <div x-show="sectionType === 'workflow'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Alur Langkah (Cara Kerja)
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="Cara kerja" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" placeholder="Contoh: Dari daftar hingga panel aktif" class="form-input-minimal">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Subjudul</label>
                            <textarea name="subtitle" rows="2" placeholder="Jelaskan alur langkah secara singkat..." class="form-input-minimal"></textarea>
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <label class="form-label">Daftar Langkah</label>
                        <template x-for="(step, sIdx) in workflowSteps" :key="sIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Langkah #' + (sIdx + 1)"></span>
                                    <button type="button" @click="removeWorkflowStep(sIdx)" class="text-xs text-red-600 hover:underline" x-show="workflowSteps.length > 1">Hapus</button>
                                </div>
                                <input type="text" :name="'steps[' + sIdx + '][title]'" x-model="step.title" placeholder="Judul langkah..." class="form-input-minimal text-sm">
                                <textarea :name="'steps[' + sIdx + '][body]'" x-model="step.body" rows="2" placeholder="Penjelasan langkah..." class="form-input-minimal text-sm"></textarea>
                                <div class="grid gap-3 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Foto / Ilustrasi Langkah (URL)</label>
                                        <input type="text" :name="'steps[' + sIdx + '][image_url]'" x-model="step.image_url" placeholder="https://..." class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-muted block mb-1">Atau Upload Gambar</label>
                                        <input type="file" :name="'step_images[' + sIdx + ']'" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="addWorkflowStep()" class="btn-minimal-secondary text-xs">
                            + Tambah Langkah
                        </button>
                    </div>
                </div>

                {{-- TIPE ECOSYSTEM --}}
                <div x-show="sectionType === 'ecosystem'" class="card-minimal p-6 space-y-4">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        2. Isi Tiga Pilar Ekosistem
                    </h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Badge</label>
                            <input type="text" name="badge" value="Ekosistem" class="form-input-minimal">
                        </div>
                        <div>
                            <label class="form-label">Judul Bagian</label>
                            <input type="text" name="title" placeholder="Contoh: Berbayar resmi, aplikasi bertambah, akses terpusat" class="form-input-minimal">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Subjudul</label>
                            <textarea name="subtitle" rows="2" placeholder="Penjelasan singkat ekosistem layanan..." class="form-input-minimal"></textarea>
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <label class="form-label">Daftar Pilar Layanan</label>
                        <template x-for="(item, eIdx) in ecosystemItems" :key="eIdx">
                            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500" x-text="'Pilar #' + (eIdx + 1)"></span>
                                    <button type="button" @click="removeEcosystemItem(eIdx)" class="text-xs text-red-600 hover:underline" x-show="ecosystemItems.length > 1">Hapus</button>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div class="sm:col-span-2">
                                        <input type="text" :name="'items[' + eIdx + '][title]'" x-model="item.title" placeholder="Judul pilar..." class="form-input-minimal text-sm">
                                    </div>
                                    <div>
                                        <select :name="'items[' + eIdx + '][accent]'" x-model="item.accent" class="form-input-minimal text-sm">
                                            <option value="brand">Biru (Brand)</option>
                                            <option value="gold">Kuning (Gold)</option>
                                            <option value="sky">Biru Muda (Sky)</option>
                                            <option value="teal">Hijau Toska (Teal)</option>
                                            <option value="amber">Amber (Oranye)</option>
                                            <option value="emerald">Hijau (Emerald)</option>
                                        </select>
                                    </div>
                                </div>
                                <textarea :name="'items[' + eIdx + '][body]'" x-model="item.body" rows="2" placeholder="Deskripsi pilar..." class="form-input-minimal text-sm"></textarea>
                            </div>
                        </template>
                        <button type="button" @click="addEcosystemItem()" class="btn-minimal-secondary text-xs">
                            + Tambah Pilar Layanan
                        </button>
                    </div>
                </div>

                {{-- Pengaturan Background (Latar Belakang Container) --}}
                @include('admin.homepage._background_settings', ['content' => []])

                {{-- Tombol Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.homepage.index') }}" class="btn-minimal-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn-minimal-primary">
                        Simpan Container
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
