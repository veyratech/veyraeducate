<x-app-layout>
    <div
        x-data="{
            activeTab: '{{ $errors->hasBag('updatePassword') ? 'akun' : request('tab', ($errors->hasAny(['name', 'email', 'phone']) && !$errors->has('school_name') ? 'akun' : 'profil')) }}',
            logoPreviewUrl: null,
            handleLogoPreview(event) {
                const file = event.target.files[0];
                if (file) {
                    this.logoPreviewUrl = URL.createObjectURL(file);
                }
            }
        }"
        class="space-y-6"
    >
        {{-- Header Overview Card with Dynamic Theme --}}
        <div
            :class="'bg-gradient-to-r ' + (themeConfig[schoolTheme]?.hero || themeConfig.teal.hero)"
            class="relative overflow-hidden rounded-[2rem] p-5 sm:p-8 text-white shadow-xl shadow-black/10 transition-all duration-300"
        >
            {{-- Decorative blurs --}}
            <div class="pointer-events-none absolute -right-12 -top-12 h-56 w-56 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6">
                {{-- Left: School Avatar / Logo & Quick Info --}}
                <div class="flex items-start sm:items-center gap-3.5 sm:gap-6">
                    <div class="relative group shrink-0">
                        <div class="h-16 w-16 sm:h-24 sm:w-24 rounded-2xl bg-white/20 p-1 backdrop-blur-md border border-white/30 shadow-lg flex items-center justify-center overflow-hidden">
                            @if ($school->logo_path)
                                <img
                                    src="{{ asset('storage/' . $school->logo_path) }}"
                                    alt="Logo {{ $school->name }}"
                                    class="h-full w-full object-cover rounded-xl bg-white"
                                />
                            @else
                                <div class="h-full w-full rounded-xl bg-white/25 flex flex-col items-center justify-center text-white font-extrabold text-xl sm:text-2xl tracking-wider">
                                    {{ collect(explode(' ', trim((string) $school->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'SK' }}
                                </div>
                            @endif
                        </div>
                        <button
                            type="button"
                            @click="activeTab = 'profil'"
                            class="absolute -bottom-1.5 -right-1.5 sm:-bottom-2 sm:-right-2 h-6 w-6 sm:h-7 sm:w-7 rounded-full bg-white text-slate-700 shadow-md flex items-center justify-center hover:scale-110 transition cursor-pointer"
                            title="Ganti Logo Sekolah"
                        >
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-bold bg-white/20 backdrop-blur-md border border-white/20 text-white">
                                <span class="h-1.5 w-1.5 sm:h-2 sm:w-2 rounded-full bg-[#fecf59] animate-pulse"></span>
                                {{ $school->meta['status'] ?? 'Sekolah Terdaftar' }}
                            </span>
                            @if ($school->npsn)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-black/20 backdrop-blur-md text-white">
                                    NPSN: {{ $school->npsn }}
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-white/15 text-white">
                                {{ $school->meta['level'] ?? 'SMA/SMK' }}
                            </span>
                        </div>

                        <h1 class="text-xl sm:text-3xl font-black tracking-tight text-white mt-1 sm:mt-1.5 truncate">
                            {{ $school->name }}
                        </h1>

                        <p class="text-xs sm:text-sm text-white/80 font-medium mt-1 flex flex-wrap items-center gap-x-3 sm:gap-x-4 gap-y-0.5 sm:gap-y-1">
                            @if ($school->city)
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $school->city }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Admin: {{ $user->name }}
                            </span>
                            @if (isset($school->meta['academic_year']))
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    TA {{ $school->meta['academic_year'] }} ({{ $school->meta['semester'] ?? 'Ganjil' }})
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Right: Quick Stats Pills --}}
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <div class="rounded-2xl bg-black/20 backdrop-blur-md px-3.5 sm:px-4 py-2 sm:py-3 border border-white/10 text-left sm:text-right">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-white/70">Langganan</p>
                        <p class="text-xs sm:text-sm font-black text-white">
                            {{ $activeSubscription?->plan?->label ?? 'Belum Aktif' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-black/20 backdrop-blur-md px-3.5 sm:px-4 py-2 sm:py-3 border border-white/10 text-left sm:text-right">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-white/70">Modul WebApp</p>
                        <p class="text-xs sm:text-sm font-black text-white">
                            {{ $webApps->count() }} Terpasang
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Settings Navigation Tabs --}}
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto no-scrollbar">
            <button
                type="button"
                @click="activeTab = 'profil'"
                class="flex items-center gap-2.5 px-4 sm:px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0"
                :class="activeTab === 'profil' ? ('bg-white dark:bg-slate-800 shadow-md shadow-slate-200/50 dark:shadow-none ' + (themeConfig[schoolTheme]?.activeText || 'text-teal-700') + ' dark:text-white border border-slate-200/70 dark:border-slate-700') : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60'"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Profil & Identitas</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'tampilan'"
                class="flex items-center gap-2.5 px-4 sm:px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0"
                :class="activeTab === 'tampilan' ? ('bg-white dark:bg-slate-800 shadow-md shadow-slate-200/50 dark:shadow-none ' + (themeConfig[schoolTheme]?.activeText || 'text-teal-700') + ' dark:text-white border border-slate-200/70 dark:border-slate-700') : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60'"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                <span>Tampilan & Tema</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'tim'"
                class="flex items-center gap-2.5 px-4 sm:px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0"
                :class="activeTab === 'tim' ? ('bg-white dark:bg-slate-800 shadow-md shadow-slate-200/50 dark:shadow-none ' + (themeConfig[schoolTheme]?.activeText || 'text-teal-700') + ' dark:text-white border border-slate-200/70 dark:border-slate-700') : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60'"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Tim Guru &amp; Dapodik</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'akun'"
                class="flex items-center gap-2.5 px-4 sm:px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0"
                :class="activeTab === 'akun' ? ('bg-white dark:bg-slate-800 shadow-md shadow-slate-200/50 dark:shadow-none ' + (themeConfig[schoolTheme]?.activeText || 'text-teal-700') + ' dark:text-white border border-slate-200/70 dark:border-slate-700') : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60'"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Akun & Keamanan</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'langganan'"
                class="flex items-center gap-2.5 px-4 sm:px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all shrink-0"
                :class="activeTab === 'langganan' ? ('bg-white dark:bg-slate-800 shadow-md shadow-slate-200/50 dark:shadow-none ' + (themeConfig[schoolTheme]?.activeText || 'text-teal-700') + ' dark:text-white border border-slate-200/70 dark:border-slate-700') : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-slate-800/60'"
            >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Langganan & Modul</span>
            </button>
        </div>

        {{-- TAB 1: PROFIL & IDENTITAS SEKOLAH --}}
        <div x-show="activeTab === 'profil'" x-cloak class="space-y-6">
            
            {{-- Logo Uploader Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Logo Resmi Sekolah
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Logo ini akan ditampilkan pada kop surat, laporan rapor digital, serta kop dashboard sekolah.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('school.settings.logo') }}" enctype="multipart/form-data" class="mt-6">
                    @csrf
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        {{-- Preview Box --}}
                        <div class="shrink-0 text-center">
                            <div class="relative h-28 w-28 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-2 flex items-center justify-center overflow-hidden shadow-inner">
                                <template x-if="logoPreviewUrl">
                                    <img :src="logoPreviewUrl" alt="Preview Logo" class="h-full w-full object-contain rounded-xl" />
                                </template>
                                <template x-if="!logoPreviewUrl">
                                    <div>
                                        @if ($school->logo_path)
                                            <img
                                                src="{{ asset('storage/' . $school->logo_path) }}"
                                                alt="Logo {{ $school->name }}"
                                                class="h-full w-full object-contain rounded-xl"
                                            />
                                        @else
                                            <div class="flex flex-col items-center justify-center text-slate-400">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-[10px] mt-1 font-semibold">Belum Ada</span>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 mt-1 block">Pratinjau Logo</span>
                        </div>

                        {{-- Input & Upload Action --}}
                        <div class="flex-1 space-y-3 w-full">
                            <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/50">
                                <label for="logo_file" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                                    Pilih Berkas Logo
                                </label>
                                <input
                                    id="logo_file"
                                    name="logo"
                                    type="file"
                                    accept="image/png,image/jpeg,image/webp"
                                    @change="handleLogoPreview"
                                    class="block w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white dark:file:bg-slate-700 file:text-slate-800 dark:file:text-white hover:file:bg-slate-100 dark:hover:file:bg-slate-600 transition cursor-pointer"
                                    required
                                />
                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                                    Format didukung: <strong>PNG, JPG, WEBP</strong>. Ukuran maksimal <strong>2MB</strong>. Disarankan gambar persegi dengan latar belakang transparan.
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:opacity-95 active:scale-95"
                                    :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#0f766e')"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Unggah & Simpan Logo
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- School Profile & Academic Form Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <form method="POST" action="{{ route('school.settings.school') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- Section: Identitas Utama --}}
                    <div>
                        <div class="pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Identitas Resmi & Data Akademik
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                Informasi legalitas lembaga pendidikan dan kalender tahun ajaran aktif.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            {{-- Nama Resmi Sekolah --}}
                            <div class="sm:col-span-2">
                                <label for="school_name" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Nama Resmi Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="school_name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $school->name) }}"
                                    placeholder="Contoh: SMAN 1 Jakarta"
                                    required
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            {{-- NPSN --}}
                            <div>
                                <label for="school_npsn" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    NPSN (Nomor Pokok Sekolah Nasional)
                                </label>
                                <input
                                    id="school_npsn"
                                    name="npsn"
                                    type="text"
                                    value="{{ old('npsn', $school->npsn) }}"
                                    placeholder="8 digit angka, contoh: 20101234"
                                    maxlength="30"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                                <x-input-error :messages="$errors->get('npsn')" class="mt-1" />
                            </div>

                            {{-- Jenjang Pendidikan --}}
                            <div>
                                <label for="school_level" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Jenjang Pendidikan
                                </label>
                                <select
                                    id="school_level"
                                    name="level"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                >
                                    @php
                                        $currentLevel = old('level', $school->meta['level'] ?? 'SMA/SMK');
                                        $levels = ['SD / MI', 'SMP / MTs', 'SMA / MA', 'SMK', 'SLB', 'Pesantren / PKBM', 'Perguruan Tinggi / Akademi'];
                                    @endphp
                                    @foreach ($levels as $lvl)
                                        <option value="{{ $lvl }}" {{ $currentLevel === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Sekolah --}}
                            <div>
                                <label for="school_status" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Status Sekolah
                                </label>
                                <select
                                    id="school_status"
                                    name="status"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                >
                                    @php
                                        $currentStatus = old('status', $school->meta['status'] ?? 'Swasta');
                                    @endphp
                                    <option value="Negeri" {{ $currentStatus === 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                    <option value="Swasta" {{ $currentStatus === 'Swasta' ? 'selected' : '' }}>Swasta</option>
                                </select>
                            </div>

                            {{-- Nama Kepala Sekolah --}}
                            <div>
                                <label for="school_headmaster" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Nama Kepala Sekolah
                                </label>
                                <input
                                    id="school_headmaster"
                                    name="headmaster"
                                    type="text"
                                    value="{{ old('headmaster', $school->meta['headmaster'] ?? $school->meta['headmaster_name'] ?? '') }}"
                                    placeholder="Contoh: Drs. H. Ahmad Sudirman, M.Pd."
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- NIP Kepala Sekolah --}}
                            <div>
                                <label for="school_headmaster_nip" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    NIP Kepala Sekolah <span class="font-normal text-slate-400">(Opsional)</span>
                                </label>
                                <input
                                    id="school_headmaster_nip"
                                    name="headmaster_nip"
                                    type="text"
                                    value="{{ old('headmaster_nip', $school->meta['headmaster_nip'] ?? '') }}"
                                    placeholder="Contoh: 19780512 200312 1 002"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- Tahun Ajaran --}}
                            <div>
                                <label for="school_academic_year" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Tahun Ajaran Aktif
                                </label>
                                <input
                                    id="school_academic_year"
                                    name="academic_year"
                                    type="text"
                                    value="{{ old('academic_year', $school->meta['academic_year'] ?? '2026/2027') }}"
                                    placeholder="2026/2027"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- Semester --}}
                            <div>
                                <label for="school_semester" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Semester Aktif
                                </label>
                                <select
                                    id="school_semester"
                                    name="semester"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                >
                                    @php
                                        $currentSemester = old('semester', $school->meta['semester'] ?? 'Ganjil');
                                    @endphp
                                    <option value="Ganjil" {{ $currentSemester === 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                                    <option value="Genap" {{ $currentSemester === 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Kontak & Lokasi Lembaga --}}
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                        <div class="pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Kontak & Alamat Lembaga
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                Alamat fisik sekolah dan nomor kontak korespondensi resmi.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            {{-- Email Resmi Sekolah --}}
                            <div>
                                <label for="school_email" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Email Resmi Sekolah
                                </label>
                                <input
                                    id="school_email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $school->email) }}"
                                    placeholder="info@sekolah.sch.id"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            {{-- No. Telepon Sekolah --}}
                            <div>
                                <label for="school_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Telepon / Hotline Sekolah
                                </label>
                                <input
                                    id="school_phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone', $school->phone) }}"
                                    placeholder="(021) 1234567 atau 0812..."
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- Kota / Kabupaten --}}
                            <div>
                                <label for="school_city" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Kota / Kabupaten
                                </label>
                                <input
                                    id="school_city"
                                    name="city"
                                    type="text"
                                    value="{{ old('city', $school->city) }}"
                                    placeholder="Contoh: Jakarta Selatan"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- Kecamatan --}}
                            <div>
                                <label for="school_district" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Kecamatan
                                </label>
                                <input
                                    id="school_district"
                                    name="district"
                                    type="text"
                                    value="{{ old('district', $school->district) }}"
                                    placeholder="Contoh: Kebayoran Baru"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- Kode Pos --}}
                            <div>
                                <label for="school_postal_code" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Kode Pos
                                </label>
                                <input
                                    id="school_postal_code"
                                    name="postal_code"
                                    type="text"
                                    value="{{ old('postal_code', $school->postal_code) }}"
                                    placeholder="12130"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                />
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label for="school_address" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                    Alamat Lengkap (Jalan, RT/RW, Kelurahan)
                                </label>
                                <textarea
                                    id="school_address"
                                    name="address"
                                    rows="2"
                                    placeholder="Jl. Pendidikan No. 12, RT 01 / RW 05..."
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                                >{{ old('address', $school->meta['address'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Submit button --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md transition hover:opacity-95 active:scale-95"
                            :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#0f766e')"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan Identitas Sekolah
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB 2: TAMPILAN & TEMA DASHBOARD --}}
        <div x-show="activeTab === 'tampilan'" x-cloak class="space-y-6">
            
            {{-- Theme Palettes Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="pb-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                            Pilihan Palet Tema Dashboard
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Pilih suasana warna visual identitas sekolah Anda. Pilihan tema ini tersimpan permanen di cloud akun sekolah dan otomatis diterapkan di seluruh browser, PC, laptop guru, maupun smartphone siswa.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="selectTheme('teal')"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-900/50 px-3 py-1.5 text-xs font-bold transition shadow-sm self-start sm:self-center cursor-pointer shrink-0"
                    >
                        <span>🌿 Reset ke Default (Vitality Mint)</span>
                    </button>
                </div>

                {{-- Interactive Palette Grid --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="(cfg, key) in themeConfig" :key="key">
                        <div
                            @click="selectTheme(key)"
                            class="relative cursor-pointer rounded-2xl border-2 p-4 transition-all duration-200 hover:shadow-lg"
                            :class="schoolTheme === key ? 'border-brand-500 bg-brand-50/20 dark:bg-slate-800/80 shadow-md ring-2 ring-brand-500/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-800/40'"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xl" x-text="cfg.icon"></span>
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="cfg.name"></p>
                                            <span x-show="key === 'teal'" class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 text-[9px] px-1.5 py-0.5 font-black uppercase tracking-wider">Default</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-medium" x-text="key.toUpperCase()"></p>
                                    </div>
                                </div>
                                <div
                                    class="h-5 w-5 rounded-full flex items-center justify-center transition-all"
                                    :class="schoolTheme === key ? 'bg-brand-500 text-white shadow-sm' : 'border border-slate-300 dark:border-slate-600'"
                                >
                                    <svg x-show="schoolTheme === key" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Gradient Bar Preview --}}
                            <div
                                :class="'bg-gradient-to-r ' + cfg.hero"
                                class="h-10 w-full rounded-xl shadow-sm flex items-center justify-between px-3 text-white"
                            >
                                <span class="text-[10px] font-bold">Pratinjau Hero</span>
                                <span class="h-3 w-3 rounded-full border border-white/60" :style="'background-color: ' + cfg.accent"></span>
                            </div>

                            {{-- Sidebar Palette Dots --}}
                            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                <span>Aksen Utama</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-full" :style="'background-color: ' + cfg.accent"></span>
                                    <span class="font-mono text-[10px] font-semibold uppercase" x-text="cfg.accent"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Dark / Light Mode Selector Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="pb-4 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Mode Gelap & Terang
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Pilih mode kontras yang nyaman bagi mata saat mengelola data sekolah di malam hari maupun siang hari.
                    </p>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Light Mode Card --}}
                    <div
                        @click="theme = 'light'; document.documentElement.classList.remove('dark'); localStorage.setItem('theme', 'light')"
                        class="cursor-pointer rounded-2xl border-2 p-5 transition-all"
                        :class="theme === 'light' ? 'border-brand-500 bg-brand-50/20 shadow-md ring-2 ring-brand-500/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300'"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Mode Terang (Light)</p>
                                    <p class="text-xs text-slate-500">Tampilan bersih dengan latar cerah</p>
                                </div>
                            </div>
                            <div
                                class="h-5 w-5 rounded-full flex items-center justify-center"
                                :class="theme === 'light' ? 'bg-brand-500 text-white' : 'border border-slate-300'"
                            >
                                <svg x-show="theme === 'light'" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dark Mode Card --}}
                    <div
                        @click="theme = 'dark'; document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark')"
                        class="cursor-pointer rounded-2xl border-2 p-5 transition-all bg-slate-900 text-white"
                        :class="theme === 'dark' ? 'border-brand-500 shadow-md ring-2 ring-brand-500/20' : 'border-slate-800 hover:border-slate-700'"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-slate-800 text-indigo-400 flex items-center justify-center">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Mode Gelap (Dark)</p>
                                    <p class="text-xs text-slate-400">Elegan & minim silau untuk malam hari</p>
                                </div>
                            </div>
                            <div
                                class="h-5 w-5 rounded-full flex items-center justify-center"
                                :class="theme === 'dark' ? 'bg-brand-500 text-white' : 'border border-slate-700'"
                            >
                                <svg x-show="theme === 'dark'" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: AKUN & KEAMANAN --}}
        <div x-show="activeTab === 'akun'" x-cloak class="space-y-6">
            
            {{-- Admin Profile Form Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="pb-4 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Akun Administrator
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Kelola data pribadi administrator yang bertugas mengelola portal sekolah ini.
                    </p>
                </div>

                <form method="POST" action="{{ route('school.settings.profile') }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Nama Admin --}}
                        <div>
                            <label for="admin_name" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                Nama Lengkap Administrator <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="admin_name"
                                name="name"
                                type="text"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        {{-- Email Admin --}}
                        <div>
                            <label for="admin_email" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                Alamat Email Login <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="admin_email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        {{-- Telepon / WhatsApp Admin --}}
                        <div class="sm:col-span-2">
                            <label for="admin_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input
                                id="admin_phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone', $user->phone ?? '') }}"
                                placeholder="Contoh: 081234567890"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:opacity-95 active:scale-95"
                            :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#0f766e')"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan Akun
                        </button>
                    </div>
                </form>
            </div>

            {{-- Update Password Form Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="pb-4 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Keamanan & Perbarui Kata Sandi
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk menjaga keamanan portal sekolah.
                    </p>
                </div>

                <form method="POST" action="{{ route('school.settings.password') }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        {{-- Password Sekarang --}}
                        <div>
                            <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                Kata Sandi Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                        </div>

                        {{-- Password Baru --}}
                        <div>
                            <label for="new_password" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="new_password"
                                name="password"
                                type="password"
                                required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"
                            />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:opacity-95 active:scale-95"
                            :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#0f766e')"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB 4: LANGGANAN & MODUL TERPASANG --}}
        <div x-show="activeTab === 'langganan'" x-cloak class="space-y-6">
            
            {{-- Subscription Overview Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Paket Langganan Sekolah
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Status aktif akun sekolah dan akses modul ekosistem Veyra Educate.
                        </p>
                    </div>

                    <a
                        href="{{ route('checkout.show', 'super') }}"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:opacity-95 self-start sm:self-auto"
                        :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#0f766e')"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Upgrade / Perpanjang Paket
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Status Card --}}
                    <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 p-5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Langganan</span>
                        <div class="mt-2 flex items-center gap-2">
                            @if ($activeSubscription)
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">Aktif & Berjalan</span>
                            @else
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                <span class="text-base font-extrabold text-amber-600 dark:text-amber-400">Belum Ada Paket Aktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            {{ $activeSubscription ? 'Akses penuh ke seluruh modul terintegrasi' : 'Selesaikan pembayaran untuk akses penuh' }}
                        </p>
                    </div>

                    {{-- Nama Paket --}}
                    <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 p-5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama Paket</span>
                        <p class="text-base font-extrabold text-slate-900 dark:text-white mt-2">
                            {{ $activeSubscription?->plan?->label ?? 'Trial / Reguler' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Durasi: {{ $activeSubscription?->plan?->duration_label ?? '6 Bulan' }}
                        </p>
                    </div>

                    {{-- Masa Berlaku --}}
                    <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 p-5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masa Berlaku</span>
                        <p class="text-base font-extrabold text-slate-900 dark:text-white mt-2">
                            {{ $activeSubscription?->ends_at ? $activeSubscription->ends_at->translatedFormat('d F Y') : '—' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            @if ($activeSubscription?->ends_at)
                                Sisa {{ now()->diffInDays($activeSubscription->ends_at, false) > 0 ? now()->diffInDays($activeSubscription->ends_at) . ' hari lagi' : 'Telah berakhir' }}
                            @else
                                Hubungi tim Veyra Educate
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Installed WebApps Grid Card --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Modul Aplikasi Web Terpasang ({{ $webApps->count() }})
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Daftar aplikasi terhubung yang aktif untuk sekolah Anda.
                        </p>
                    </div>

                    <a
                        href="{{ route('apps.index') }}"
                        class="text-xs font-bold text-brand-600 hover:text-brand-700 dark:text-brand-400 flex items-center gap-1"
                    >
                        Katalog Aplikasi &rarr;
                    </a>
                </div>

                @if ($webApps->isNotEmpty())
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($webApps as $app)
                            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between hover:shadow-md transition">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 shrink-0 rounded-xl bg-white dark:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-600 flex items-center justify-center text-xl">
                                        {{ $app->icon ?: '📱' }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                            {{ $app->name }}
                                        </h3>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">
                                            {{ $app->description ?: 'Modul aplikasi pendidikan terintegrasi' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                    <a
                                        href="{{ route('apps.show', $app->slug) }}"
                                        class="text-[11px] font-bold text-brand-600 hover:underline dark:text-brand-400 flex items-center gap-1"
                                    >
                                        Buka Aplikasi &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 text-center py-10 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                        <div class="h-12 w-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="mt-3 text-sm font-bold text-slate-900 dark:text-white">Belum Ada Modul Khusus Ditambahkan</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                            Anda dapat menambahkan dan mengaktifkan aplikasi pendidikan dari katalog aplikasi Veyra Educate.
                        </p>
                        <div class="mt-4">
                            <a
                                href="{{ route('apps.index') }}"
                                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold text-white shadow-sm"
                                :style="'background-color: ' + (themeConfig[schoolTheme]?.accent || '#0f766e')"
                            >
                                Jelajahi Katalog Aplikasi
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 5: TIM GURU & DAPODIK --}}
        <div x-show="activeTab === 'tim'" x-cloak class="space-y-6">

            {{-- Sub-section 1: Template Import Dapodik Kemendikbudristek & Kemenag --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📊</span>
                            Pusat Berkas &amp; Template Dapodik / EMIS
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Unduh berkas standar CSV/Excel resmi untuk migrasi data siswa dan profil pendidik ke CBT Online &amp; e-Rapor.
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    {{-- Card Template Siswa --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-5 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 01-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Template Data Siswa (Dapodik/EMIS)</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                Berisi kolom NISN, NIK, Nama Siswa, Jenis Kelamin, Tingkat Kelas, Rombongan Belajar, Tanggal Lahir, dan No WhatsApp Siswa/Wali.
                            </p>
                        </div>
                        <a
                            href="{{ route('school.settings.template-dapodik', 'siswa') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh Template CSV Siswa</span>
                        </a>
                    </div>

                    {{-- Card Template Guru --}}
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 p-5 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col justify-between gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                <span>Template Data Guru &amp; Tenaga Pendidik</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                Berisi kolom NUPTK, NIP, Nama Lengkap, Gelar, Mata Pelajaran Diampu, Email Akun, dan No WhatsApp Pengajar.
                            </p>
                        </div>
                        <a
                            href="{{ route('school.settings.template-dapodik', 'guru') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh Template CSV Guru</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sub-section 2: Daftar Anggota Tim Sekolah --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>👥</span>
                            Daftar Guru &amp; Staf Terdaftar ({{ $teamMembers->count() }} Akun)
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            Akun-akun berikut memiliki akses login ke portal sekolah dan aplikasi CBT Veyra Educate.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40 text-muted dark:text-slate-400 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="py-3 px-4">Nama Lengkap</th>
                                <th class="py-3 px-4">Email Akun</th>
                                <th class="py-3 px-4 text-center">Peran / Jabatan</th>
                                <th class="py-3 px-4 text-center">No WhatsApp</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($teamMembers as $member)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $member->name }}
                                        @if((int) $member->id === (int) $school->owner_user_id)
                                            <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                Penanggung Jawab / Pemilik
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                                        {{ $member->email }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($member->role === 'bendahara')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                Bendahara BOS
                                            </span>
                                        @elseif($member->role === 'operator')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                Operator Dapodik & IT
                                            </span>
                                        @elseif($member->role === 'teacher')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300">
                                                Guru / Pengajar
                                            </span>
                                        @elseif($member->role === 'staff')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300">
                                                Staf Tata Usaha
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                                {{ $member->roleLabel() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-600 dark:text-slate-300">
                                        {{ $member->phone ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if((int) $member->id !== (int) $school->owner_user_id && (int) $member->id !== (int) $user->id)
                                            <form method="POST" action="{{ route('school.settings.team.destroy', $member->id) }}" onsubmit="return confirm('Hapus akses akun {{ $member->name }} dari sekolah ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300 hover:underline cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">Akun Utama</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Form Tambah Akun Baru --}}
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <span>➕</span> Daftarkan Akun Pengajar / Staf Baru
                    </h3>

                    <form method="POST" action="{{ route('school.settings.team.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap &amp; Gelar</label>
                            <input type="text" name="name" required placeholder="Contoh: Siti Rahmawati, S.Pd." class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Email (Untuk Login)</label>
                            <input type="email" name="email" required placeholder="guru@sekolah.sch.id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Peran / Hak Akses</label>
                            <select name="role" required class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <option value="teacher">Guru / Pengajar</option>
                                <option value="bendahara">Bendahara BOS (Akses Keuangan & SPJ)</option>
                                <option value="operator">Operator Dapodik & IT (Akses Siswa & Cetak Kartu)</option>
                                <option value="staff">Staf Tata Usaha</option>
                                <option value="school">Kepala Sekolah / Admin Utama</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">No WhatsApp (Opsional)</label>
                            <input type="text" name="phone" placeholder="081234567890" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Awal</label>
                            <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
                        </div>

                        <div class="flex items-end">
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs py-2.5 px-4 shadow-sm transition cursor-pointer"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Simpan &amp; Aktifkan Akun</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
