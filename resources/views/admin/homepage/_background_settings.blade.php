@php
    $currentBgType = old('bg_type', $content['bg_type'] ?? 'default');
    $currentBgColor = old('bg_color', $content['bg_color'] ?? '#0f172a');
    $currentBgOverlay = old('bg_overlay', $content['bg_overlay'] ?? 'dark-60');
    $currentTextMode = old('text_mode', $content['text_mode'] ?? 'auto');
    $currentBgImageUrl = $content['bg_image_url'] ?? null;
@endphp

<div
    class="card-minimal p-6 space-y-6"
    x-data="{
        bgType: '{{ $currentBgType }}',
        customColor: '{{ $currentBgColor }}',
        bgOverlay: '{{ $currentBgOverlay }}',
        textMode: '{{ $currentTextMode }}',
        setColor(hex) {
            this.customColor = hex;
        }
    }"
>
    <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/60 dark:text-brand-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4 4 4 0 014-4h4a4 4 0 014 4 4 4 0 01-4 4H7zm0 0l9.5-9.5a2.5 2.5 0 013.5 3.5L10.5 24.5M19 6l-3-3m0 0L8.5 10.5m7.5-7.5l3 3" />
                </svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-ink dark:text-slate-100">
                    Pengaturan Background (Latar Belakang Container)
                </h3>
                <p class="text-xs text-muted">
                    Sesuaikan warna latar belakang, gradien modern, atau pasang gambar background sendiri untuk container ini.
                </p>
            </div>
        </div>
    </div>

    {{-- Pilihan Mode Background --}}
    <div class="space-y-3">
        <label class="form-label">Pilih Gaya Background</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            {{-- 1. Default --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'default' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="default" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[11px] font-medium text-slate-500 mb-2">
                    Bawaan Asli
                </div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">⚡ Bawaan (Default)</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Style asli container</span>
            </label>

            {{-- 2. Dark Gradient --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'dark' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="dark" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-gradient-to-r from-slate-950 via-brand-950 to-slate-900 mb-2 shadow-inner"></div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">🌙 Gelap Premium</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Gradien midnight navy</span>
            </label>

            {{-- 3. Clean White --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'white' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="white" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-white border border-slate-200 mb-2 shadow-inner"></div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">⚪ Putih Bersih</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Latar putih minimalis</span>
            </label>

            {{-- 4. Soft Slate --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'slate' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="slate" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 mb-2 shadow-inner"></div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">🌫️ Abu-abu Slate</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Pemisah lembut elegan</span>
            </label>

            {{-- 5. Brand Blue --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'brand' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="brand" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-gradient-to-r from-brand-950 via-brand-900 to-sky-950 mb-2 shadow-inner"></div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">🔷 Royal Blue Brand</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Nuansa biru khas Veyra</span>
            </label>

            {{-- 6. Gold Elegance --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'gold' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="gold" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-gradient-to-r from-amber-950 via-yellow-950 to-amber-900 mb-2 shadow-inner"></div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">✨ Emas Mewah</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Untuk highlight / promo</span>
            </label>

            {{-- 7. Emerald --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'emerald' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="emerald" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-gradient-to-r from-emerald-950 via-teal-950 to-emerald-900 mb-2 shadow-inner"></div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">🌿 Emerald Segar</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Gradien hijau alami</span>
            </label>

            {{-- 8. Custom Color --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'custom_color' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="custom_color" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md border border-dashed border-slate-300 dark:border-slate-700 mb-2 flex items-center justify-center text-xs" :style="customColor ? 'background-color: ' + customColor : ''">
                    🎨
                </div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">🎨 Warna Kustom HEX</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Pilih warna sesuka hati</span>
            </label>

            {{-- 9. Custom Image --}}
            <label
                class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150"
                :class="bgType === 'custom_image' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/20 dark:bg-brand-950/20' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
            >
                <input type="radio" name="bg_type" value="custom_image" x-model="bgType" class="sr-only">
                <div class="h-6 w-full rounded-md bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs mb-2">
                    🖼️
                </div>
                <span class="text-xs font-bold text-ink dark:text-slate-200">🖼️ Gambar Latar</span>
                <span class="text-[11px] text-muted leading-tight mt-0.5">Upload foto background</span>
            </label>
        </div>
    </div>

    {{-- Panel Khusus: Warna HEX Kustom --}}
    <div x-show="bgType === 'custom_color'" x-transition class="p-4 rounded-2xl border border-brand-200 dark:border-brand-900 bg-brand-50/40 dark:bg-brand-950/30 space-y-4">
        <h4 class="text-sm font-bold text-ink dark:text-slate-200 flex items-center gap-2">
            <span>🎨</span> Pilih Warna Latar Belakang
        </h4>
        <div class="flex items-center gap-3">
            <input
                type="color"
                x-model="customColor"
                class="h-10 w-14 rounded-lg border border-slate-300 dark:border-slate-700 p-0.5 cursor-pointer bg-white"
            >
            <div class="flex-1 max-w-xs">
                <input
                    type="text"
                    name="bg_color"
                    x-model="customColor"
                    placeholder="#0f172a"
                    class="form-input-minimal font-mono text-sm uppercase"
                >
            </div>
            <div class="h-10 w-10 rounded-lg shadow-inner border border-slate-300 dark:border-slate-700" :style="'background-color: ' + customColor"></div>
        </div>

        <div>
            <p class="text-xs text-muted mb-2">Preset Warna Populer:</p>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="setColor('#0f172a')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-900 text-white shadow-sm hover:scale-105 transition-transform">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span> #0F172A (Midnight)
                </button>
                <button type="button" @click="setColor('#1e1b4b')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-indigo-950 text-white shadow-sm hover:scale-105 transition-transform">
                    <span class="h-2 w-2 rounded-full bg-indigo-400"></span> #1E1B4B (Deep Indigo)
                </button>
                <button type="button" @click="setColor('#0a2540')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-[#0a2540] text-white shadow-sm hover:scale-105 transition-transform">
                    <span class="h-2 w-2 rounded-full bg-sky-400"></span> #0A2540 (Navy Stripe)
                </button>
                <button type="button" @click="setColor('#064e3b')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-emerald-950 text-white shadow-sm hover:scale-105 transition-transform">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span> #064E3B (Forest)
                </button>
                <button type="button" @click="setColor('#3b0764')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-purple-950 text-white shadow-sm hover:scale-105 transition-transform">
                    <span class="h-2 w-2 rounded-full bg-purple-400"></span> #3B0764 (Deep Purple)
                </button>
                <button type="button" @click="setColor('#f8fafc')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-100 text-slate-800 border border-slate-300 shadow-sm hover:scale-105 transition-transform">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span> #F8FAFC (Soft Light)
                </button>
            </div>
        </div>
    </div>

    {{-- Panel Khusus: Upload Gambar Background --}}
    <div x-show="bgType === 'custom_image'" x-transition class="p-4 rounded-2xl border border-brand-200 dark:border-brand-900 bg-brand-50/40 dark:bg-brand-950/30 space-y-4">
        <h4 class="text-sm font-bold text-ink dark:text-slate-200 flex items-center gap-2">
            <span>🖼️</span> Pengaturan Gambar Latar Belakang
        </h4>

        @if ($currentBgImageUrl)
            <div class="flex items-center gap-4 p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                <img src="{{ \App\Models\HomepageSection::resolveImageUrl($currentBgImageUrl) }}" alt="Current Background" class="h-16 w-28 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-ink dark:text-slate-200">Gambar background saat ini aktif</p>
                    <p class="text-[11px] text-muted truncate max-w-sm">{{ $currentBgImageUrl }}</p>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-red-600 hover:text-red-700 font-semibold pt-1">
                        <input type="checkbox" name="remove_bg_image" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-3.5 w-3.5">
                        <span>Hapus gambar latar ini</span>
                    </label>
                </div>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="form-label" for="bg_image_file">Upload File Gambar Baru</label>
                <input
                    type="file"
                    name="bg_image_file"
                    id="bg_image_file"
                    accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                    class="form-input-minimal text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-700"
                >
                <p class="text-[11px] text-muted mt-1">Format: JPG, PNG, WebP (Rekomendasi rasio lebar 1920x1080px)</p>
            </div>

            <div>
                <label class="form-label" for="bg_image_url_manual">Atau Masukkan Link / URL Gambar</label>
                <input
                    type="text"
                    name="bg_image_url_manual"
                    id="bg_image_url_manual"
                    placeholder="Contoh: https://domain.com/foto-sekolah.jpg"
                    class="form-input-minimal text-sm"
                >
            </div>
        </div>

        <div>
            <label class="form-label" for="bg_overlay">Lapisan Filter (Overlay) di Atas Gambar</label>
            <select
                name="bg_overlay"
                id="bg_overlay"
                x-model="bgOverlay"
                class="form-input-minimal text-sm"
            >
                <option value="dark-80">Lapisan Gelap 80% (Paling Kontras — Teks putih sangat jelas & tajam)</option>
                <option value="dark-60">Lapisan Gelap 60% (Standar Rekomendasi — Seimbang & Elegan)</option>
                <option value="dark-40">Lapisan Gelap 40% (Gambar lebih terang terlihat)</option>
                <option value="light-80">Lapisan Terang 80% (Nuansa putih — Teks gelap)</option>
                <option value="light-60">Lapisan Terang 60%</option>
                <option value="none">Tanpa Lapisan (Tampilkan gambar asli apa adanya)</option>
            </select>
            <p class="text-[11px] text-muted mt-1">
                Filter overlay membantu agar teks judul dan tombol di atas foto tetap nyaman dibaca oleh pengunjung.
            </p>
        </div>
    </div>

    {{-- Mode Warna Teks --}}
    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
        <div class="grid gap-3 sm:grid-cols-2 items-center">
            <div>
                <label class="form-label" for="text_mode">Kontras Teks (Font Color Mode)</label>
                <select name="text_mode" id="text_mode" x-model="textMode" class="form-input-minimal text-sm">
                    <option value="auto">⚡ Otomatis (Direkomendasikan — Menyesuaikan background)</option>
                    <option value="light">☀️ Paksa Teks Putih / Terang</option>
                    <option value="dark">🌑 Paksa Teks Gelap / Hitam</option>
                </select>
            </div>
            <p class="text-xs text-muted leading-relaxed sm:pt-4">
                Pilih <em>Otomatis</em> agar sistem pintar memilih warna teks yang paling mudah dibaca sesuai kecerahan background yang Anda tentukan.
            </p>
        </div>
    </div>
</div>
