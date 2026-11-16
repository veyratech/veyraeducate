    <x-admin-layout title="Katalog Aplikasi — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div>
            <p class="eyebrow">Link Akses</p>
            <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Katalog Aplikasi</h2>
            <p class="mt-1 text-sm text-muted">Tambahkan dan kelola link akses aplikasi untuk sekolah mitra.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site space-y-6 max-w-2xl mx-auto">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="document.getElementById('linkModal').showModal(); document.body.classList.add('modal-open');"
                    class="btn-minimal-primary inline-flex items-center gap-2"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Link
                </button>
            </div>

            @if (session('status'))
                <div class="alert-success stagger-in">
                    <p class="text-xs font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Link belum tersimpan</p>
                    <ul class="mt-2 space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (count($apps) === 0)
                <div class="empty-minimal stagger-in">
                    <div class="empty-minimal-icon">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <p class="empty-minimal-title">Belum ada link akses</p>
                    <p class="empty-minimal-desc">Klik tombol <strong class="font-semibold text-ink dark:text-slate-200">Tambah Link</strong> untuk menambahkan.</p>
                </div>
            @else
                <div class="bento-grid stagger-in">
                    @foreach ($apps as $app)
                        @php
                            $deployed = $app['on_disk'] || $app['access_url'] !== null;
                        @endphp
                        <div
                            class="card-minimal-interactive flex flex-col p-5 bento-item {{ $app['enabled'] ? '' : 'opacity-60' }}"
                            x-data="{ copied: false, editing: false }"
                        >
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="avatar-minimal-md avatar-minimal-brand shrink-0">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-ink dark:text-slate-100 truncate">{{ $app['name'] }}</p>
                                        <p class="text-xs text-muted font-mono dark:text-slate-500 truncate">/{{ $app['slug'] }}</p>
                                    </div>
                                </div>
                                @if (! $deployed)
                                    <span class="shrink-0 badge-minimal badge-minimal-pending">Draft / Not Deployed</span>
                                @elseif ($app['enabled'])
                                    <span class="shrink-0 badge-minimal badge-minimal-active">Deployed</span>
                                @else
                                    <span class="shrink-0 badge-minimal badge-minimal-inactive">Nonaktif</span>
                                @endif
                            </div>

                            <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-muted">
                                <span class="badge-minimal badge-minimal-inactive">
                                    {{ $app['access_url'] ? 'Link eksternal' : 'Aplikasi di server' }}
                                </span>
                                @if ($app['category'] && $app['category'] !== 'umum')
                                    <span class="badge-minimal badge-minimal-pending">
                                        {{ $categories[$app['category']] ?? $app['category'] }}
                                    </span>
                                @endif
                            </div>

                            @if ($deployed)
                                <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-800/60">
                                    <p class="eyebrow">URL akses aplikasi</p>
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $app['link'] }}"
                                            x-ref="link"
                                            onclick="this.select()"
                                            aria-label="URL akses {{ $app['name'] }}"
                                            class="block w-full min-w-0 border-0 bg-transparent px-0 py-1 font-mono text-xs text-brand-800 dark:text-brand-300 focus:ring-0"
                                        />
                                        <button
                                            type="button"
                                            @click="navigator.clipboard.writeText($refs.link.value).then(() => { copied = true; setTimeout(() => copied = false, 1500); })"
                                            class="shrink-0 btn-minimal-ghost text-[11px] px-2 py-1"
                                        >
                                            <span x-text="copied ? 'Tersalin' : 'Salin'">Salin</span>
                                        </button>
                                        <a
                                            href="{{ $app['link'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="shrink-0 btn-minimal-ghost text-[11px] px-2 py-1"
                                        >
                                            Buka
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="mt-3 rounded-lg border border-dashed border-slate-300 bg-white p-3 dark:border-slate-700 dark:bg-slate-800/40">
                                    <p class="eyebrow">Status</p>
                                    <p class="mt-1.5 text-xs text-muted dark:text-slate-400">
                                        Belum di-deploy — isi <strong class="font-semibold text-ink dark:text-slate-200">URL tujuan</strong> di bawah agar link aktif.
                                    </p>
                                </div>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('admin.link-access.update', $app['slug']) }}"
                                x-show="editing"
                                x-cloak
                                x-transition:enter="transition-all duration-200 ease-out"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition-all duration-150 ease-in"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="mt-3 space-y-2 stagger-in"
                            >
                                @csrf
                                @method('PATCH')
                                <div class="form-group-minimal">
                                    <x-input-label for="access_url-{{ $app['slug'] }}" value="URL tujuan" />
                                    <input
                                        id="access_url-{{ $app['slug'] }}"
                                        name="access_url"
                                        type="url"
                                        value="{{ $app['access_url'] ?? '' }}"
                                        placeholder="https://contoh.sekolah.sch.id"
                                        class="mt-1 form-input-minimal"
                                    />
                                </div>
                                <p class="text-[11px] text-muted">
                                    Kosongkan untuk memakai alamat bawaan aplikasi di server.
                                </p>
                                <div class="flex gap-2">
                                    <button type="submit" class="btn-minimal-primary">Simpan URL</button>
                                    <button type="button" @click="editing = false" class="btn-minimal-secondary">Batal</button>
                                </div>
                            </form>

                            <div class="mt-auto flex items-center gap-x-3 gap-y-1 pt-4 border-t border-slate-200 text-xs font-semibold dark:border-slate-800">
                                <button type="button" @click="editing = ! editing" class="btn-minimal-ghost text-xs px-3 py-1.5">
                                    <span x-text="editing ? 'Tutup form' : 'Ubah link'">Ubah link</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-minimal p-4 stagger-in" style="animation-delay: 200ms;">
                    <p class="eyebrow">Tentang link akses</p>
                    <ul class="mt-2 space-y-1 text-xs text-muted dark:text-slate-400">
                        <li>&bull; Link bawaan aplikasi yang ada di server: <code class="font-mono text-slate-600 dark:text-slate-400">/apps/sekolah/{slug}</code>.</li>
                        <li>&bull; Isi <strong class="font-semibold text-ink dark:text-slate-200">URL tujuan</strong> untuk mengarahkan sekolah ke alamat lain, misalnya subdomain.</li>
                        <li>&bull; Kosongkan URL tujuan untuk kembali memakai alamat bawaan di server (hanya untuk aplikasi yang filenya ada di server).</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <style>
        body.modal-open { overflow: hidden; }
        dialog#linkModal::backdrop { background: rgba(2, 6, 23, 0.6); backdrop-filter: blur(4px); }
        dialog#linkModal::-webkit-scrollbar { display: none; }
    </style>

    <dialog id="linkModal" class="modal">
        <div class="card-minimal p-6 sm:p-8 w-full max-w-lg">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <p class="text-base font-bold text-ink dark:text-slate-100">Tambah link akses</p>
                    <p class="text-xs text-muted mt-0.5">Tambahkan link akses baru untuk aplikasi sekolah mitra.</p>
                </div>
                <button type="button" onclick="document.getElementById('linkModal').close(); document.body.classList.remove('modal-open');" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.link-access.store') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="form-group-minimal">
                        <x-input-label for="name" value="Nama aplikasi" />
                        <input id="name" name="name" type="text" required placeholder="e.g. E-Learning"
                            value="{{ old('name') }}" class="mt-1 form-input-minimal" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label for="slug" value="Slug" />
                        <p class="mt-1 text-xs text-muted">Huruf kecil, tanpa spasi. Contoh: <code class="font-mono">e-learning</code></p>
                        <input id="slug" name="slug" type="text" required placeholder="e.g. e-learning"
                            value="{{ old('slug') }}" pattern="[a-z0-9\-_]+"
                            oninput="this.value=this.value.toLowerCase().replace(/[^a-z0-9\-_]/g,'')"
                            class="mt-1 form-input-minimal font-mono" />
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>
                <div class="form-group-minimal">
                    <x-input-label for="access_url" value="URL akses (opsional)" />
                    <p class="mt-1 text-xs text-muted">Isi bila link mengarah ke alamat eksternal. Kosongkan untuk menyimpan sebagai draft.</p>
                    <input id="access_url" name="access_url" type="url" placeholder="https://contoh.sekolah.sch.id"
                        value="{{ old('access_url') }}" class="mt-1 form-input-minimal" />
                    <x-input-error :messages="$errors->get('access_url')" class="mt-2" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="form-group-minimal">
                        <x-input-label for="category" value="Kategori" />
                        <select id="category" name="category" class="mt-1 form-input-minimal appearance-none bg-white dark:bg-slate-800">
                            <option value="umum" @selected(old('category','umum') === 'umum')">Umum</option>
                            <option value="akademik" @selected(old('category') === 'akademik')">Akademik</option>
                            <option value="keuangan" @selected(old('category') === 'keuangan')">Keuangan</option>
                            <option value="perpustakaan" @selected(old('category') === 'perpustakaan')">Perpustakaan</option>
                            <option value="kesiswaan" @selected(old('category') === 'kesiswaan')">Kesiswaan</option>
                            <option value="kepegawaian" @selected(old('category') === 'kepegawaian')">Kepegawaian</option>
                            <option value="ekskul" @selected(old('category') === 'ekskul')">Ekstrakurikuler</option>
                            <option value="komunikasi" @selected(old('category') === 'komunikasi')">Komunikasi</option>
                            <option value="lainnya" @selected(old('category') === 'lainnya')">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label for="icon" value="Ikon" />
                        <select id="icon" name="icon" class="mt-1 form-input-minimal appearance-none bg-white dark:bg-slate-800">
                            @foreach (\App\Models\WebApp::iconOptions() as $key => $label)
                                <option value="{{ $key }}" @selected(old('icon') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('linkModal').close(); document.body.classList.remove('modal-open');" class="btn-minimal-secondary">Batal</button>
                    <button type="submit" class="btn-minimal-primary">Simpan link</button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        document.getElementById('linkModal').addEventListener('close', function () {
            document.body.classList.remove('modal-open');
        });
        if (window.location.hash === '#tambah') {
            document.getElementById('linkModal').showModal();
            document.body.classList.add('modal-open');
        }
    </script>
</x-admin-layout>
