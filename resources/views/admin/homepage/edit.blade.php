<x-admin-layout title="Edit Container {{ $section->title }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.homepage.index') }}" class="btn-minimal-ghost p-1.5 text-muted hover:text-ink">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <p class="eyebrow">Edit Container</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">
                    {{ $section->title }}
                    <span class="text-sm font-normal text-slate-500">({{ $section->section_type_label }})</span>
                </h2>
            </div>
        </div>
    </x-slot>

    @php
        $content = $section->content ?? [];
        $existingItems = $content['items'] ?? [];
        $existingSteps = $content['steps'] ?? [];
        $existingTestimonials = $content['testimonials'] ?? $content['items'] ?? [];
    @endphp

    <div class="py-8" x-data="{
        items: {{ json_encode(!empty($existingItems) ? array_values($existingItems) : [['title' => '', 'body' => '', 'accent' => 'brand', 'image_url' => '']]) }},
        faqItems: {{ json_encode(!empty($existingItems) ? array_values($existingItems) : [['q' => '', 'a' => '']]) }},
        steps: {{ json_encode(!empty($existingSteps) ? array_values($existingSteps) : [['title' => '', 'body' => '', 'image_url' => '']]) }},
        testimonials: {{ json_encode(!empty($existingTestimonials) ? array_values($existingTestimonials) : [['name' => '', 'role' => '', 'school' => '', 'quote' => '', 'rating' => 5, 'avatar_url' => '']]) }},
        appItems: {{ json_encode(!empty($existingItems) ? array_values($existingItems) : [['name' => '', 'category' => 'Akademik', 'description' => '', 'status_badge' => 'Siap Pakai', 'accent' => 'brand', 'tags' => '', 'icon' => 'academic-cap', 'image_url' => '']]) }},
        addItem() { this.items.push({ title: '', body: '', accent: 'brand', image_url: '' }); },
        removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); },
        addFaq() { this.faqItems.push({ q: '', a: '' }); },
        removeFaq(idx) { if (this.faqItems.length > 1) this.faqItems.splice(idx, 1); },
        addStep() { this.steps.push({ title: '', body: '', image_url: '' }); },
        removeStep(idx) { if (this.steps.length > 1) this.steps.splice(idx, 1); },
        addTestimonial() { this.testimonials.push({ name: '', role: '', school: '', quote: '', rating: 5, avatar_url: '' }); },
        removeTestimonial(idx) { if (this.testimonials.length > 1) this.testimonials.splice(idx, 1); },
        addApp() { this.appItems.push({ name: '', category: 'Akademik', description: '', status_badge: 'Siap Pakai', accent: 'brand', tags: '', icon: 'academic-cap', image_url: '' }); },
        removeApp(idx) { if (this.appItems.length > 1) this.appItems.splice(idx, 1); }
    }">
        <div class="container-site max-w-4xl space-y-6">
            <form action="{{ route('admin.homepage.update', $section) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Status & Info Dasar --}}
                <div class="card-minimal p-6 space-y-5">
                    <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                        1. Pengaturan & Urutan Tampil
                    </h3>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="form-label" for="title">Judul / Label Container (untuk panel admin) <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title', $section->title) }}"
                                required
                                class="form-input-minimal"
                            >
                            @error('title')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="order_position">Nomor Urutan Tampil <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                name="order_position"
                                id="order_position"
                                value="{{ old('order_position', $section->order_position) }}"
                                required
                                min="1"
                                class="form-input-minimal"
                            >
                        </div>
                    </div>

                    <div class="pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4"
                            >
                            <span class="text-sm font-semibold text-ink dark:text-slate-200">Aktif (Tampilkan di Web Depan)</span>
                        </label>
                    </div>
                </div>

                {{-- FORM KHUSUS SESUAI SECTION TYPE --}}

                {{-- HERO BANNER --}}
                @if ($section->section_type === 'hero')
                    <div class="card-minimal p-6 space-y-5">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Konten Hero Banner
                        </h3>

                         <div class="space-y-4">
                             <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800">
                                 <div>
                                     <p class="text-sm font-bold text-ink dark:text-slate-100">Tampilkan Banner Hero</p>
                                     <p class="text-xs text-muted mt-0.5">Nonaktifkan untuk menampilkan background-only (cosmetic section)</p>
                                 </div>
                                 <label class="inline-flex items-center gap-2 cursor-pointer">
                                     <input type="checkbox" name="show_banner" value="1" {{ (!empty($content['show_banner']) || $content['show_banner'] !== false) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4">
                                     <span class="text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                                 </label>
                             </div>

                             <div>
                                <label class="form-label">Badge Kecil (Pill Text di atas judul)</label>
                                <input type="text" name="lead" value="{{ old('lead', $content['lead'] ?? '') }}" class="form-input-minimal" placeholder="Contoh: Platform Aplikasi Sekolah">
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="form-label">Judul Highlight (Warna Emas)</label>
                                    <input type="text" name="highlight" value="{{ old('highlight', $content['highlight'] ?? '') }}" class="form-input-minimal" placeholder="Contoh: Platform aplikasi sekolah,">
                                </div>
                                <div>
                                    <label class="form-label">Judul Tail (Warna Biru Sky)</label>
                                    <input type="text" name="tail" value="{{ old('tail', $content['tail'] ?? '') }}" class="form-input-minimal" placeholder="Contoh: siap pakai.">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Deskripsi / Paragraf Pengantar</label>
                                <textarea name="description" rows="3" class="form-input-minimal">{{ old('description', $content['description'] ?? '') }}</textarea>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 border-t border-slate-100 dark:border-slate-800 pt-4">
                                <div>
                                    <label class="form-label">Teks Tombol Utama</label>
                                    <input type="text" name="primary_btn_text" value="{{ old('primary_btn_text', $content['primary_btn_text'] ?? 'Daftar sekolah') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Link Tombol Utama</label>
                                    <input type="text" name="primary_btn_url" value="{{ old('primary_btn_url', $content['primary_btn_url'] ?? '/register') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Teks Tombol Kedua</label>
                                    <input type="text" name="secondary_btn_text" value="{{ old('secondary_btn_text', $content['secondary_btn_text'] ?? 'Lihat demo') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Link Tombol Kedua</label>
                                    <input type="text" name="secondary_btn_url" value="{{ old('secondary_btn_url', $content['secondary_btn_url'] ?? '/demo') }}" class="form-input-minimal">
                                </div>
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-3">
                                <label class="form-label">Foto Model / Mockup Dashboard Hero (Sisi Kanan)</label>
                                @if (!empty($content['image_url']))
                                    <div class="flex items-center gap-4 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                        <img src="{{ \App\Models\HomepageSection::resolveImageUrl($content['image_url']) }}" alt="Hero Image" class="h-16 w-28 rounded-lg object-cover border shadow-sm">
                                        <div class="space-y-1 flex-1">
                                            <p class="text-xs font-bold text-ink dark:text-slate-200">Foto kustom Hero aktif</p>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-red-600 hover:text-red-700 font-semibold">
                                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-3.5 w-3.5">
                                                <span>Hapus & gunakan mockup default</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Upload File Foto Model / Mockup Baru</label>
                                        <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                        <input type="text" name="image_url_manual" value="{{ old('image_url_manual', $content['image_url'] ?? '') }}" placeholder="https://..." class="form-input-minimal text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- CUSTOM BANNER --}}
                @elseif ($section->section_type === 'custom_banner')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Banner Promo / Pengumuman
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge Kecil</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Warna Tema Latar</label>
                                <select name="theme" class="form-input-minimal">
                                    <option value="brand" {{ ($content['theme'] ?? '') === 'brand' ? 'selected' : '' }}>Biru Veyra (Brand)</option>
                                    <option value="dark" {{ ($content['theme'] ?? '') === 'dark' ? 'selected' : '' }}>Dark Gradient (Elegan Gelap)</option>
                                    <option value="emerald" {{ ($content['theme'] ?? '') === 'emerald' ? 'selected' : '' }}>Hijau (Fresh / Promo)</option>
                                    <option value="gold" {{ ($content['theme'] ?? '') === 'gold' ? 'selected' : '' }}>Emas / Amber (Eksklusif)</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Judul Banner</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Deskripsi / Penjelasan Singkat</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="form-label">Teks Tombol Aksi</label>
                                <input type="text" name="btn_text" value="{{ old('btn_text', $content['btn_text'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Link Tombol Aksi</label>
                                <input type="text" name="btn_url" value="{{ old('btn_url', $content['btn_url'] ?? '') }}" class="form-input-minimal">
                            </div>

                            <div class="sm:col-span-2 border-t border-slate-100 dark:border-slate-800 pt-3 space-y-3">
                                <label class="form-label">Foto Model / Grafis Promo (Opsional)</label>
                                @if (!empty($content['image_url']))
                                    <div class="flex items-center gap-4 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                        <img src="{{ \App\Models\HomepageSection::resolveImageUrl($content['image_url']) }}" alt="Banner Graphic" class="h-16 w-24 rounded-lg object-contain border shadow-sm">
                                        <div class="space-y-1">
                                            <p class="text-xs font-bold text-ink dark:text-slate-200">Grafis banner aktif</p>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-red-600 hover:text-red-700 font-semibold">
                                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-3.5 w-3.5">
                                                <span>Hapus gambar ini</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Upload File Foto/Grafis</label>
                                        <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                        <input type="text" name="image_url_manual" value="{{ old('image_url_manual', $content['image_url'] ?? '') }}" placeholder="https://..." class="form-input-minimal text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- CUSTOM CONTENT --}}
                @elseif ($section->section_type === 'custom_content')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Container Bebas (Teks & Gambar)
                        </h3>

                        <div class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="form-label">Badge Kecil</label>
                                    <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? '') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Tema Latar Belakang</label>
                                    <select name="theme" class="form-input-minimal">
                                        <option value="light" {{ ($content['theme'] ?? '') === 'light' ? 'selected' : '' }}>Terang (Putih / Slate)</option>
                                        <option value="slate" {{ ($content['theme'] ?? '') === 'slate' ? 'selected' : '' }}>Abu Lembut (Slate 50)</option>
                                        <option value="dark" {{ ($content['theme'] ?? '') === 'dark' ? 'selected' : '' }}>Gelap (Dark Mode)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>

                            <div>
                                <label class="form-label">Subjudul / Paragraf Pengantar</label>
                                <input type="text" name="subtitle" value="{{ old('subtitle', $content['subtitle'] ?? '') }}" class="form-input-minimal">
                            </div>

                            <div>
                                <label class="form-label">Isi Konten Penjelasan</label>
                                <textarea name="body_html" rows="5" class="form-input-minimal">{{ old('body_html', $content['body_html'] ?? '') }}</textarea>
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-3">
                                <label class="form-label">Foto Model / Ilustrasi Container</label>
                                @if (!empty($content['image_url']))
                                    <div class="flex items-center gap-4 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                        <img src="{{ \App\Models\HomepageSection::resolveImageUrl($content['image_url']) }}" alt="Preview" class="h-16 w-24 rounded-lg border object-cover shadow-sm">
                                        <div class="space-y-1">
                                            <p class="text-xs font-bold text-ink dark:text-slate-200">Foto aktif</p>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-red-600 hover:text-red-700 font-semibold">
                                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-3.5 w-3.5">
                                                <span>Hapus gambar ini</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Upload File Foto Baru</label>
                                        <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                        <input type="text" name="image_url_manual" value="{{ old('image_url_manual', $content['image_url'] ?? '') }}" placeholder="https://..." class="form-input-minimal text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Posisi Foto</label>
                                        <select name="image_position" class="form-input-minimal text-sm">
                                            <option value="right" {{ ($content['image_position'] ?? 'right') === 'right' ? 'selected' : '' }}>Kanan Teks</option>
                                            <option value="left" {{ ($content['image_position'] ?? '') === 'left' ? 'selected' : '' }}>Kiri Teks</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="form-label">Teks Tombol (Opsional)</label>
                                    <input type="text" name="btn_text" value="{{ old('btn_text', $content['btn_text'] ?? '') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Link Tombol (Opsional)</label>
                                    <input type="text" name="btn_url" value="{{ old('btn_url', $content['btn_url'] ?? '') }}" class="form-input-minimal">
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- FEATURES / ECOSYSTEM --}}
                @elseif (in_array($section->section_type, ['features', 'ecosystem']))
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Judul & Kartu Fitur
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Subjudul</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <label class="form-label">Daftar Kartu</label>
                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-slate-900/50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-500" x-text="'Kartu #' + (idx + 1)"></span>
                                        <button type="button" @click="removeItem(idx)" class="text-xs text-red-600 hover:underline" x-show="items.length > 1">Hapus</button>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-3">
                                        <div class="sm:col-span-2">
                                            <input type="text" :name="'items[' + idx + '][title]'" x-model="item.title" placeholder="Judul kartu..." class="form-input-minimal text-sm">
                                        </div>
                                        <div>
                                            <select :name="'items[' + idx + '][accent]'" x-model="item.accent" class="form-input-minimal text-sm">
                                                <option value="brand">Biru (Brand)</option>
                                                <option value="gold">Kuning (Gold)</option>
                                                <option value="sky">Biru Muda (Sky)</option>
                                                <option value="teal">Hijau Toska (Teal)</option>
                                                <option value="amber">Amber (Oranye)</option>
                                                <option value="emerald">Hijau (Emerald)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <textarea :name="'items[' + idx + '][body]'" x-model="item.body" rows="2" placeholder="Deskripsi..." class="form-input-minimal text-sm"></textarea>
                                    <div class="grid gap-3 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                        <div>
                                            <label class="text-[11px] text-muted block mb-1">Foto / Ilustrasi Kartu (URL)</label>
                                            <input type="text" :name="'items[' + idx + '][image_url]'" x-model="item.image_url" placeholder="https://..." class="form-input-minimal text-xs">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-muted block mb-1">Atau Upload Gambar Kartu</label>
                                            <input type="file" :name="'item_images[' + idx + ']'" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                        </div>
                                    </div>
                                    <div x-show="item.image_url" class="flex items-center gap-2 pt-1">
                                        <img :src="item.image_url" alt="Preview" class="h-8 w-14 object-cover rounded border">
                                        <button type="button" @click="item.image_url = ''" class="text-[11px] text-red-600 hover:underline">Hapus foto kartu ini</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="addItem()" class="btn-minimal-secondary text-xs">
                                + Tambah Kartu
                            </button>
                        </div>
                    </div>

                {{-- WORKFLOW --}}
                @elseif ($section->section_type === 'workflow')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Alur Langkah (Cara Kerja)
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Subjudul</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <label class="form-label">Daftar Langkah</label>
                            <template x-for="(st, sIdx) in steps" :key="sIdx">
                                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 bg-slate-50/50 dark:bg-slate-900/50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-500" x-text="'Langkah #' + (sIdx + 1)"></span>
                                        <button type="button" @click="removeStep(sIdx)" class="text-xs text-red-600 hover:underline" x-show="steps.length > 1">Hapus</button>
                                    </div>
                                    <input type="text" :name="'steps[' + sIdx + '][title]'" x-model="st.title" placeholder="Judul langkah..." class="form-input-minimal text-sm">
                                    <textarea :name="'steps[' + sIdx + '][body]'" x-model="st.body" rows="2" placeholder="Penjelasan langkah..." class="form-input-minimal text-sm"></textarea>
                                    <div class="grid gap-3 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                        <div>
                                            <label class="text-[11px] text-muted block mb-1">Foto / Ilustrasi Langkah (URL)</label>
                                            <input type="text" :name="'steps[' + sIdx + '][image_url]'" x-model="st.image_url" placeholder="https://..." class="form-input-minimal text-xs">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-muted block mb-1">Atau Upload Gambar</label>
                                            <input type="file" :name="'step_images[' + sIdx + ']'" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                        </div>
                                    </div>
                                    <div x-show="st.image_url" class="flex items-center gap-2 pt-1">
                                        <img :src="st.image_url" alt="Preview" class="h-8 w-14 object-cover rounded border">
                                        <button type="button" @click="st.image_url = ''" class="text-[11px] text-red-600 hover:underline">Hapus foto langkah ini</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="addStep()" class="btn-minimal-secondary text-xs">
                                + Tambah Langkah
                            </button>
                        </div>
                    </div>

                {{-- APP SHOWCASE --}}
                @elseif ($section->section_type === 'app_showcase')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Showcase Katalog Aplikasi Sekolah
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? 'Katalog Aplikasi') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Subjudul Penjelasan</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Sumber Data Aplikasi</label>
                                @php $sourceMode = old('source_mode', $content['source_mode'] ?? 'custom'); @endphp
                                <select name="source_mode" class="form-input-minimal text-sm">
                                    <option value="custom" {{ $sourceMode === 'custom' ? 'selected' : '' }}>Katalog Kurasi Manual (Gunakan daftar kartu di bawah)</option>
                                    <option value="database" {{ $sourceMode === 'database' ? 'selected' : '' }}>Otomatis dari Database App Hub (Tabel WebApps yang aktif)</option>
                                </select>
                                <p class="text-[11px] text-muted mt-1">Jika memilih otomatis dari database, website depan akan mengambil aplikasi yang diaktifkan di App Hub.</p>
                            </div>
                        </div>

                        <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <label class="form-label">Daftar Modul Aplikasi (Untuk Mode Kurasi Manual)</label>
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

                                    <div x-show="app.image_url" class="flex items-center gap-2 pt-1">
                                        <img :src="app.image_url" alt="Ikon App" class="h-8 w-8 object-contain rounded border">
                                        <button type="button" @click="app.image_url = ''" class="text-[11px] text-red-600 hover:underline">Hapus ikon ini</button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addApp()" class="btn-minimal-secondary text-xs">
                                + Tambah Aplikasi
                            </button>
                        </div>
                    </div>

                {{-- TESTIMONIALS --}}
                @elseif ($section->section_type === 'testimonials')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Ulasan & Testimoni Sekolah
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? 'Testimoni') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Subjudul Penjelasan</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
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

                                    <div x-show="item.avatar_url" class="flex items-center gap-2 pt-1">
                                        <img :src="item.avatar_url" alt="Foto Profil" class="h-9 w-9 object-cover rounded-full border">
                                        <button type="button" @click="item.avatar_url = ''" class="text-[11px] text-red-600 hover:underline">Hapus foto profil ini</button>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addTestimonial()" class="btn-minimal-secondary text-xs">
                                + Tambah Ulasan Sekolah
                            </button>
                        </div>
                    </div>

                {{-- FAQ --}}
                @elseif ($section->section_type === 'faq')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Tanya Jawab (FAQ)
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Subjudul</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <label class="form-label">Daftar Tanya & Jawab</label>
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

                {{-- PRICING --}}
                @elseif ($section->section_type === 'pricing')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Pengaturan Bagian Harga
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="form-label">Badge</label>
                                <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? 'Harga') }}" class="form-input-minimal">
                            </div>
                            <div>
                                <label class="form-label">Judul Bagian</label>
                                <input type="text" name="title" value="{{ old('title', $content['title'] ?? 'Paket langganan transparan') }}" class="form-input-minimal">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Subjudul</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <p class="text-xs text-muted">
                            💡 Catatan: Daftar kartu paket harga secara otomatis diambil dari menu <strong>Paket</strong> di sidebar admin.
                        </p>
                    </div>

                {{-- CTA --}}
                @elseif ($section->section_type === 'cta')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Isi Blok Ajakan (Call to Action)
                        </h3>

                        <div class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="form-label">Badge</label>
                                    <input type="text" name="badge" value="{{ old('badge', $content['badge'] ?? 'Siap mulai') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Judul Ajakan</label>
                                    <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}" class="form-input-minimal">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Subjudul Penjelasan</label>
                                <textarea name="subtitle" rows="2" class="form-input-minimal">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 border-t border-slate-100 dark:border-slate-800 pt-3">
                                <div>
                                    <label class="form-label">Teks Tombol Utama</label>
                                    <input type="text" name="btn_text" value="{{ old('btn_text', $content['btn_text'] ?? 'Daftar sekolah') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Link Tombol Utama</label>
                                    <input type="text" name="btn_url" value="{{ old('btn_url', $content['btn_url'] ?? '/register') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Teks Tombol WhatsApp</label>
                                    <input type="text" name="wa_text" value="{{ old('wa_text', $content['wa_text'] ?? 'Konsultasi WhatsApp') }}" class="form-input-minimal">
                                </div>
                                <div>
                                    <label class="form-label">Nomor / Link WhatsApp</label>
                                    <input type="text" name="wa_url" value="{{ old('wa_url', $content['wa_url'] ?? 'https://wa.me/6285707074778') }}" class="form-input-minimal">
                                </div>
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-3">
                                <label class="form-label">Foto Model / Ilustrasi Pendamping CTA (Opsional)</label>
                                @if (!empty($content['image_url']))
                                    <div class="flex items-center gap-4 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                        <img src="{{ \App\Models\HomepageSection::resolveImageUrl($content['image_url']) }}" alt="CTA Image" class="h-16 w-24 rounded-lg object-cover border shadow-sm">
                                        <div class="space-y-1">
                                            <p class="text-xs font-bold text-ink dark:text-slate-200">Foto model aktif</p>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-red-600 hover:text-red-700 font-semibold">
                                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-3.5 w-3.5">
                                                <span>Hapus foto model ini</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Upload File Foto Model</label>
                                        <input type="file" name="image" accept="image/*" class="form-input-minimal text-xs">
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Atau Link / URL Gambar</label>
                                        <input type="text" name="image_url_manual" value="{{ old('image_url_manual', $content['image_url'] ?? '') }}" placeholder="https://..." class="form-input-minimal text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted block mb-1">Posisi Foto</label>
                                        <select name="image_position" class="form-input-minimal text-sm">
                                            <option value="right" {{ ($content['image_position'] ?? 'right') === 'right' ? 'selected' : '' }}>Kanan Teks</option>
                                            <option value="left" {{ ($content['image_position'] ?? '') === 'left' ? 'selected' : '' }}>Kiri Teks</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- TRUST STRIP --}}
                @elseif ($section->section_type === 'trust_strip')
                    <div class="card-minimal p-6 space-y-4">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                            2. Tiga Pilar Kepercayaan
                        </h3>
                        <div class="space-y-3">
                            @foreach ($existingItems as $tIdx => $tItem)
                                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2 bg-slate-50/50 dark:bg-slate-900/50">
                                    <span class="text-xs font-bold text-slate-500">Pilar #{{ $tIdx + 1 }}</span>
                                    <input type="text" name="items[{{ $tIdx }}][title]" value="{{ $tItem['title'] ?? '' }}" placeholder="Judul pilar..." class="form-input-minimal text-sm">
                                    <input type="text" name="items[{{ $tIdx }}][desc]" value="{{ $tItem['desc'] ?? '' }}" placeholder="Keterangan..." class="form-input-minimal text-sm">
                                    <div class="grid gap-2 sm:grid-cols-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                                        <input type="text" name="items[{{ $tIdx }}][image_url]" value="{{ $tItem['image_url'] ?? '' }}" placeholder="Link / URL Icon/Logo khusus..." class="form-input-minimal text-xs">
                                        <input type="file" name="item_images[{{ $tIdx }}]" accept="image/*" class="form-input-minimal text-xs file:py-1 file:px-2 file:text-xs">
                                    </div>
                                    @if (!empty($tItem['image_url']))
                                        <div class="flex items-center gap-2 pt-1">
                                            <img src="{{ $tItem['image_url'] }}" alt="Pilar" class="h-8 w-8 object-cover rounded-md border">
                                            <span class="text-[11px] text-muted truncate">{{ $tItem['image_url'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 3. Pengaturan Background (Latar Belakang Container) --}}
                @include('admin.homepage._background_settings', ['content' => $content])

                {{-- Tombol Simpan --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.homepage.index') }}" class="btn-minimal-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn-minimal-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
