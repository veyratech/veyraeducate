<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow">Dokumentasi & Manual</p>
            <h2 class="mt-1 text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Buku Panduan Aplikasi</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pusat berkas pedoman penggunaan dan tutorial resmi dari VeyraEducate</p>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Statistics Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-850 p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-3.5 sm:gap-4">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Buku Panduan</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ $stats['total_guides'] ?? 0 }}</p>
                </div>
            </div>

            <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-850 p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-3.5 sm:gap-4">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Unduhan</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ $stats['total_downloads'] ?? 0 }}</p>
                </div>
            </div>

            <div class="rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-850 p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-3.5 sm:gap-4">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aplikasi Terdokumentasi</p>
                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ $stats['total_apps_with_guide'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- School Starter Kit & Template Downloads --}}
        <div class="rounded-3xl bg-gradient-to-r from-brand-600 via-brand-700 to-indigo-800 p-5 sm:p-6 text-white shadow-md">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold uppercase tracking-wider mb-2">
                        🚀 Starter Kit Operator &amp; Guru
                    </span>
                    <h3 class="text-lg sm:text-xl font-black">Berkas Format Siap Unduh</h3>
                    <p class="text-xs text-brand-100 max-w-xl mt-1">
                        Unduh template standar untuk mempercepat impor data siswa, guru, soal ujian CBT, dan tata tertib ruang ujian.
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 w-full md:w-auto">
                    <a
                        href="{{ route('school.settings.template-dapodik', 'soal') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-brand-800 text-xs font-bold shadow-xs transition"
                    >
                        <span>📝</span> Template Soal CBT
                    </a>
                    <a
                        href="{{ route('school.settings.template-dapodik', 'siswa') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-brand-800 text-xs font-bold shadow-xs transition"
                    >
                        <span>👥</span> Format Siswa
                    </a>
                    <a
                        href="{{ route('school.settings.template-dapodik', 'guru') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-brand-800 text-xs font-bold shadow-xs transition"
                    >
                        <span>👨‍🏫</span> Format Guru
                    </a>
                    <a
                        href="{{ route('school.settings.template-dapodik', 'tatatertib') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-xs transition"
                    >
                        <span>📋</span> Tata Tertib Ujian
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter & Actions Toolbar Directly Above Grid --}}
        <div class="rounded-3xl bg-white dark:bg-slate-850 p-4 sm:p-5 shadow-sm border border-slate-100 dark:border-slate-800">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <form method="GET" action="{{ route('guides.index') }}" class="flex flex-col sm:flex-row items-center gap-3 flex-1">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari judul buku panduan atau modul..."
                            class="w-full pl-10 pr-4 py-2.5 text-xs rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <select
                            name="app"
                            class="text-xs rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition flex-1 sm:flex-initial"
                        >
                            <option value="">Semua Aplikasi</option>
                            @foreach ($webApps as $app)
                                <option value="{{ $app->id }}" {{ request('app') == $app->id ? 'selected' : '' }}>
                                    {{ $app->name }}
                                </option>
                            @endforeach
                        </select>

                        <select
                            name="category"
                            class="text-xs rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition flex-1 sm:flex-initial"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach (['umum' => 'Panduan Umum', 'operator' => 'Operator Sekolah', 'guru' => 'Guru & Wali Kelas', 'siswa' => 'Siswa & Peserta Ujian', 'teknis' => 'Teknis & Server'] as $catKey => $catLabel)
                                <option value="{{ $catKey }}" @selected(request('category') === $catKey)>
                                    {{ $catLabel }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            type="submit"
                            class="rounded-2xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-700 dark:hover:bg-slate-600 px-4 py-2.5 text-xs font-bold transition shadow-sm"
                        >
                            Filter
                        </button>

                        @if(request()->hasAny(['search', 'app', 'category']))
                            <a
                                href="{{ route('guides.index') }}"
                                class="rounded-2xl border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            >
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Upload Button (Khusus Admin Veyra) diletakkan tepat di atas grid --}}
                @if(auth()->user()?->isAdmin())
                    <div class="flex items-center gap-2 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 dark:border-slate-800">
                        <a
                            href="{{ route('admin.guides.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-bold border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 bg-emerald-50/60 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Kelola di Panel Admin &rarr;</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Guide List Grid --}}
        @if ($guides->isEmpty())
            <div class="rounded-[2.5rem] bg-white dark:bg-slate-850 p-12 text-center border border-dashed border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-4">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Belum Ada Buku Panduan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Admin VeyraEducate belum mengunggah buku panduan baru. Buku panduan resmi yang dirilis akan tampil di halaman ini.
                </p>
                @if(auth()->user()?->isAdmin())
                    <a
                        href="{{ route('admin.guides.index') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-2xl px-5 py-2.5 text-xs font-bold text-white !text-white shadow-md transition transform hover:-translate-y-0.5"
                        :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2') + '; color: #ffffff !important;'"
                        style="color: #ffffff !important;"
                    >
                        <span class="text-white !text-white font-bold" style="color: #ffffff !important;">Unggah di Panel Admin &rarr;</span>
                    </a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach ($guides as $guide)
                    <div class="group rounded-2xl sm:rounded-[2rem] bg-white dark:bg-slate-850 p-5 sm:p-6 shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between hover:shadow-xl hover:border-emerald-200 dark:hover:border-emerald-900/60 transition-all duration-300">
                        <div>
                            {{-- Header: File Type Badge & Version --}}
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="inline-flex items-center gap-1.5 rounded-xl px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ in_array($guide->file_type, ['pdf']) ? 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }}">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    {{ strtoupper($guide->file_type) }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    @if ($guide->version)
                                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                             {{ $guide->version }}
                                        </span>
                                    @endif
                                    <span class="rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                                        {{ $guide->webApp?->name ?? 'Umum' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Title & Description --}}
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-snug line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $guide->title }}
                            </h3>

                            @if ($guide->description)
                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 line-clamp-2 font-medium">
                                    {{ $guide->description }}
                                </p>
                            @endif

                            {{-- File Info Meta --}}
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
                                <span class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ $guide->download_count }}x diunduh
                                </span>
                                <span>{{ $guide->formatFileSize() }} &middot; {{ $guide->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                            @if (in_array(strtolower((string) $guide->file_type), ['pdf', 'txt'], true))
                                <a
                                    href="{{ route('guides.preview', $guide) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl py-2.5 px-3 text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Baca</span>
                                </a>
                            @endif
                            <a
                                href="{{ route('guides.download', $guide) }}"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl py-2.5 px-3 text-xs font-bold text-white shadow-sm transition hover:opacity-95"
                                :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#3ec5a2')"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh Panduan</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $guides->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
