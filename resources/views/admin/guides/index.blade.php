<x-admin-layout title="Buku Panduan Aplikasi — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div>
            <p class="eyebrow">Dokumentasi & Pedoman</p>
            <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Buku Panduan Aplikasi</h2>
            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Kelola dan unggah buku panduan resmi yang dapat diunduh oleh admin sekolah.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card-minimal p-5 flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-muted dark:text-slate-400">Total Buku Panduan</p>
                        <p class="text-xl font-black text-ink dark:text-slate-100">{{ $stats['total_guides'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="card-minimal p-5 flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-muted dark:text-slate-400">Total Diunduh Sekolah</p>
                        <p class="text-xl font-black text-ink dark:text-slate-100">{{ $stats['total_downloads'] ?? 0 }}x</p>
                    </div>
                </div>

                <div class="card-minimal p-5 flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-muted dark:text-slate-400">Modul Terdokumentasi</p>
                        <p class="text-xl font-black text-ink dark:text-slate-100">{{ $stats['total_apps_with_guide'] ?? 0 }} Modul</p>
                    </div>
                </div>
            </div>

            {{-- Table / Grid of Guides --}}
            <div class="card-minimal overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-ink dark:text-slate-100">Daftar Buku Panduan Rilis</h3>
                        <p class="text-xs text-muted dark:text-slate-400">Berkas panduan yang aktif dan tersedia untuk seluruh sekolah.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <form method="GET" action="{{ route('admin.guides.index') }}" class="flex flex-wrap items-center gap-2">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari buku panduan..."
                                class="text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-850 px-3 py-1.5 text-ink dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <select
                                name="app"
                                class="text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-850 px-2.5 py-1.5 text-ink dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500"
                            >
                                <option value="">Semua Aplikasi</option>
                                @foreach ($webApps as $app)
                                    <option value="{{ $app->id }}" @selected(request('app') == $app->id)>{{ $app->name }}</option>
                                @endforeach
                            </select>
                            <select
                                name="category"
                                class="text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-850 px-2.5 py-1.5 text-ink dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500"
                            >
                                <option value="">Semua Kategori</option>
                                @foreach (['umum' => 'Panduan Umum', 'operator' => 'Operator Sekolah', 'guru' => 'Guru & Wali Kelas', 'siswa' => 'Siswa & Peserta Ujian', 'teknis' => 'Teknis & Server'] as $catKey => $catLabel)
                                    <option value="{{ $catKey }}" @selected(request('category') === $catKey)>{{ $catLabel }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-minimal-secondary text-xs py-1.5 px-3">Filter</button>
                            @if (request()->hasAny(['search', 'app', 'category']))
                                <a href="{{ route('admin.guides.index') }}" class="btn-minimal-ghost text-xs py-1.5 px-2.5">Reset</a>
                            @endif
                        </form>
                        <button
                            type="button"
                            onclick="document.getElementById('adminUploadModal').showModal()"
                            class="btn-minimal-primary text-xs py-1.5 px-3.5 inline-flex items-center gap-1.5 shadow-sm text-white !text-white font-bold"
                            style="color: #ffffff !important;"
                        >
                            <svg class="h-3.5 w-3.5 text-white !text-white" style="color: #ffffff !important;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-white !text-white font-bold" style="color: #ffffff !important;">+ Upload Buku Panduan</span>
                        </button>
                    </div>
                </div>

                @if($guides->isEmpty())
                    <div class="empty-minimal py-12">
                        <div class="empty-minimal-icon">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <p class="empty-minimal-title">Belum ada buku panduan yang diunggah.</p>
                        <p class="empty-minimal-desc">
                            Klik tombol di bawah untuk mengunggah dokumen tutorial atau pedoman aplikasi bagi sekolah.
                        </p>
                        <button
                            type="button"
                            onclick="document.getElementById('adminUploadModal').showModal()"
                            class="mt-4 btn-minimal-primary text-xs text-white !text-white font-bold"
                            style="color: #ffffff !important;"
                        >
                            <span class="text-white !text-white font-bold" style="color: #ffffff !important;">+ Upload Buku Panduan Sekarang</span>
                        </button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-muted dark:text-slate-400 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-5">Dokumen & Judul</th>
                                    <th class="py-3 px-4">Aplikasi / Modul</th>
                                    <th class="py-3 px-4">Versi & Ukuran</th>
                                    <th class="py-3 px-4">Unduhan</th>
                                    <th class="py-3 px-4">Tanggal Rilis</th>
                                    <th class="py-3 px-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-ink dark:text-slate-200">
                                @foreach($guides as $guide)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                        <td class="py-3.5 px-5">
                                            <div class="flex items-start gap-3">
                                                <span class="mt-0.5 inline-flex items-center justify-center rounded-lg px-2 py-0.5 text-[10px] font-black uppercase {{ in_array($guide->file_type, ['pdf']) ? 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }}">
                                                    {{ strtoupper($guide->file_type) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-sm text-ink dark:text-slate-100">{{ $guide->title }}</p>
                                                    @if($guide->description)
                                                        <p class="text-xs text-muted dark:text-slate-400 truncate max-w-md mt-0.5">{{ $guide->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 font-medium">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="badge-minimal">
                                                    {{ $guide->webApp?->name ?? 'Panduan Umum' }}
                                                </span>
                                                @if ($guide->category && $guide->category !== 'umum')
                                                    <span class="badge-minimal badge-minimal-pending capitalize">
                                                        {{ $guide->category }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-muted dark:text-slate-400 font-medium">
                                            <span>{{ $guide->version ?: 'v1.0' }}</span>
                                            <span class="text-slate-300 dark:text-slate-600">&middot;</span>
                                            <span>{{ $guide->formatFileSize() }}</span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $guide->download_count }}x</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-muted dark:text-slate-400">
                                            {{ $guide->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right whitespace-nowrap space-x-1.5">
                                            <a
                                                href="{{ route('guides.download', $guide) }}"
                                                class="btn-minimal-secondary text-xs py-1 px-2.5 inline-flex items-center gap-1"
                                                title="Unduh Berkas"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                <span>Unduh</span>
                                            </a>

                                            <button
                                                type="button"
                                                data-action="{{ route('admin.guides.update', $guide) }}"
                                                data-title="{{ $guide->title }}"
                                                data-web-app-id="{{ $guide->web_app_id ?? '' }}"
                                                data-category="{{ $guide->category ?? 'umum' }}"
                                                data-version="{{ $guide->version ?? '' }}"
                                                data-description="{{ $guide->description ?? '' }}"
                                                data-file-name="{{ $guide->file_name }}"
                                                onclick="window.openEditGuideModal(this.dataset)"
                                                class="btn-minimal-ghost text-xs py-1 px-2.5"
                                            >
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.guides.destroy', $guide) }}" class="inline-block" onsubmit="return confirm('Hapus buku panduan ini dari portal sekolah?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-minimal-danger text-xs py-1 px-2.5">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
                        {{ $guides->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Admin Upload Modal --}}
    {{-- Admin Upload Modal --}}
    <dialog id="adminUploadModal" class="modal max-w-lg" onclick="if(event.target === this) this.close()" style="overflow: hidden;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xl w-full">
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <p class="eyebrow text-[10px]">Admin VeyraEducate</p>
                    <h2 class="text-base font-bold text-ink dark:text-slate-100">Upload Buku Panduan Baru</h2>
                </div>
                <button
                    type="button"
                    onclick="event.preventDefault(); event.stopPropagation(); (this.closest('dialog') || document.getElementById('adminUploadModal')).close();"
                    class="rounded-full p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    aria-label="Tutup"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.guides.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf

                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 dark:border-red-800/60 bg-red-50 dark:bg-red-950/30 p-3 text-xs text-red-700 dark:text-red-300">
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50 dark:bg-emerald-950/30 p-3 text-xs text-emerald-700 dark:text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Judul Buku Panduan <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        required
                        placeholder="Contoh: Buku Panduan Lengkap Pengelolaan Rapor Siswa"
                        class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                            Aplikasi Terkait
                        </label>
                        <select
                            name="web_app_id"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="">Panduan Umum / Semua</option>
                            @foreach ($webApps as $app)
                                <option value="{{ $app->id }}">{{ $app->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                            Kategori Sasaran
                        </label>
                        <select
                            name="category"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="umum">Panduan Umum</option>
                            <option value="operator">Operator Sekolah</option>
                            <option value="guru">Guru &amp; Wali Kelas</option>
                            <option value="siswa">Siswa &amp; Peserta Ujian</option>
                            <option value="teknis">Teknis &amp; Server</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                            Versi / Edisi
                        </label>
                        <input
                            type="text"
                            name="version"
                            placeholder="Contoh: v1.0.0 atau 2026"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Deskripsi / Petunjuk Singkat
                    </label>
                    <textarea
                        name="description"
                        rows="2"
                        placeholder="Ringkasan isi dan sasaran pengguna buku panduan ini..."
                        class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Pilih Berkas Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 p-2.5 hover:border-brand-500 transition">
                        <label class="cursor-pointer shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-400 border border-brand-200 dark:border-brand-800/60 px-3 py-1.5 text-xs font-bold hover:bg-brand-100 dark:hover:bg-brand-900/60 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Pilih Berkas</span>
                            <input
                                type="file"
                                name="file"
                                required
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.txt"
                                class="sr-only"
                                onchange="document.getElementById('adminFileNamePreview').innerText = this.files[0] ? (this.files[0].name + ' (' + (this.files[0].size / 1024 / 1024).toFixed(2) + ' MB)') : 'Belum ada berkas dipilih'"
                            >
                        </label>
                        <div class="min-w-0 flex-1">
                            <p id="adminFileNamePreview" class="text-xs font-semibold text-brand-700 dark:text-brand-400 truncate">
                                Belum ada berkas dipilih
                            </p>
                            <p class="text-[10px] text-slate-400">Format: PDF, Word, Excel, PPT, ZIP (Maksimal 25MB)</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button
                        type="button"
                        id="adminCancelUploadBtn"
                        onclick="event.preventDefault(); event.stopPropagation(); (this.closest('dialog') || document.getElementById('adminUploadModal')).close();"
                        class="btn-minimal-secondary text-xs py-2 px-4 rounded-xl font-medium cursor-pointer"
                    >
                        Batal
                    </button>
                    <button type="submit" class="btn-minimal-primary text-xs py-2 px-4 rounded-xl">
                        Unggah & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="adminEditModal" class="modal max-w-lg" onclick="if(event.target === this) this.close()" style="overflow: hidden;">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xl w-full">
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <p class="eyebrow text-[10px]">Admin VeyraEducate</p>
                    <h2 class="text-base font-bold text-ink dark:text-slate-100">Edit Buku Panduan</h2>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('adminEditModal').close()"
                    class="rounded-full p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    aria-label="Tutup"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="adminEditGuideForm" method="POST" action="" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Judul Buku Panduan <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="editGuideTitle"
                        type="text"
                        name="title"
                        required
                        class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                            Aplikasi Terkait
                        </label>
                        <select
                            id="editGuideApp"
                            name="web_app_id"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="">Panduan Umum / Semua</option>
                            @foreach ($webApps as $app)
                                <option value="{{ $app->id }}">{{ $app->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                            Kategori Sasaran
                        </label>
                        <select
                            id="editGuideCategory"
                            name="category"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-2.5 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="umum">Panduan Umum</option>
                            <option value="operator">Operator Sekolah</option>
                            <option value="guru">Guru &amp; Wali Kelas</option>
                            <option value="siswa">Siswa &amp; Peserta Ujian</option>
                            <option value="teknis">Teknis &amp; Server</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                            Versi / Edisi
                        </label>
                        <input
                            id="editGuideVersion"
                            type="text"
                            name="version"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Deskripsi / Petunjuk Singkat
                    </label>
                    <textarea
                        id="editGuideDescription"
                        name="description"
                        rows="2"
                        class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-ink dark:text-slate-100 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                        Ganti Berkas Dokumen (Opsional)
                    </label>
                    <p id="editGuideCurrentFile" class="text-[11px] text-muted mb-1"></p>
                    <input
                        type="file"
                        name="file"
                        accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.txt"
                        class="w-full text-xs form-input-minimal"
                    />
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button
                        type="button"
                        onclick="document.getElementById('adminEditModal').close()"
                        class="btn-minimal-secondary text-xs py-2 px-4 rounded-xl font-medium cursor-pointer"
                    >
                        Batal
                    </button>
                    <button type="submit" class="btn-minimal-primary text-xs py-2 px-4 rounded-xl">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        window.openEditGuideModal = function(dataset) {
            const modal = document.getElementById('adminEditModal');
            const form = document.getElementById('adminEditGuideForm');
            if (!modal || !form) return;

            form.action = dataset.action || '';
            document.getElementById('editGuideTitle').value = dataset.title || '';
            document.getElementById('editGuideApp').value = dataset.webAppId || '';
            document.getElementById('editGuideCategory').value = dataset.category || 'umum';
            document.getElementById('editGuideVersion').value = dataset.version || '';
            document.getElementById('editGuideDescription').value = dataset.description || '';
            document.getElementById('editGuideCurrentFile').innerText = 'Berkas saat ini: ' + (dataset.fileName || '-');
            modal.showModal();
        };

        (function() {
            function setupAdminModalClose() {
                const modal = document.getElementById('adminUploadModal');
                const cancelBtn = document.getElementById('adminCancelUploadBtn');
                if (cancelBtn && modal) {
                    cancelBtn.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        modal.close();
                    };
                }
            }

            function maybeReopenModalAfterSubmit() {
                const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
                const hasStatus = {!! session('status') ? 'true' : 'false' !!};
                if (hasErrors || hasStatus) {
                    const modal = document.getElementById('adminUploadModal');
                    if (modal) modal.showModal();
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupAdminModalClose);
                document.addEventListener('DOMContentLoaded', maybeReopenModalAfterSubmit);
            } else {
                setupAdminModalClose();
                maybeReopenModalAfterSubmit();
            }
        })();
    </script>
</x-admin-layout>
