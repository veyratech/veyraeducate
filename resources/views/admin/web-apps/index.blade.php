<x-admin-layout title="Semua Aplikasi — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Wadah Aplikasi</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Semua Aplikasi</h2>
                <p class="mt-1 text-sm text-muted">Kelola aplikasi: tambah, atur status, dan deployment.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container-site space-y-6">

            @if ($errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Data belum tersimpan</p>
                    <ul class="mt-2 space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4 flex flex-wrap gap-2">
                <button
                    type="button"
                    onclick="document.getElementById('linkModal').showModal(); document.body.classList.add('modal-open');"
                    class="btn-minimal-primary"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Aplikasi
                </button>
                <button
                    type="button"
                    onclick="document.getElementById('uploadModal').showModal(); document.body.classList.add('modal-open');"
                    class="btn-minimal-secondary"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Unggah Aplikasi
                </button>
            </div>

            @if (count($apps) === 0)
                <div class="empty-minimal stagger-in">
                    <div class="empty-minimal-icon">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.642-1.757a4.5 4.5 0 00-6.364 0l-4.5 4.5a4.5 4.5 0 006.364 6.364l1.757-1.757" />
                        </svg>
                    </div>
                    <p class="empty-minimal-title">Belum ada aplikasi</p>
                    <p class="empty-minimal-desc">Tambahkan aplikasi pertama melalui tombol di atas.</p>
                </div>
            @else
                <div class="grid gap-4 stagger-in sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($apps as $app)
                        @php
                            $deployed = $app['on_disk'] || $app['access_url'] !== null;
                        @endphp
                        <div
                            class="card-minimal-interactive flex flex-col p-5 {{ $app['enabled'] ? '' : 'opacity-60' }}"
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

                            @if ($app['description'])
                                <p class="mt-3 text-xs leading-relaxed text-muted line-clamp-2 flex-1">
                                    {{ $app['description'] }}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] text-muted">
                                <span class="badge-minimal badge-minimal-inactive">
                                    {{ $app['access_url'] ? 'Link eksternal' : 'Aplikasi di server' }}
                                </span>
                                @if ($app['category'] && $app['category'] !== 'umum')
                                    <span class="badge-minimal badge-minimal-pending">
                                        {{ $categories[$app['category']] ?? $app['category'] }}
                                    </span>
                                @endif
                                @if (!empty($app['plans']) && $app['plans']->isNotEmpty())
                                    @foreach ($app['plans'] as $plan)
                                        <span class="badge-minimal bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                                            {{ $plan->label }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="badge-minimal badge-minimal-active">
                                        Semua Paket
                                    </span>
                                @endif
                            </div>

                            @if (! $deployed)
                                <div class="mt-3 rounded-lg border border-dashed border-slate-300 bg-white p-3 dark:border-slate-700 dark:bg-slate-800/40">
                                    <p class="eyebrow">Status</p>
                                    <p class="mt-1.5 text-xs text-muted dark:text-slate-400">
                                        Belum di-deploy — aplikasi belum muncul di App Hub dan belum bisa dibuka.
                                    </p>
                                </div>
                            @endif

                            <div class="mt-auto flex flex-wrap items-center gap-x-2 gap-y-1 pt-4 border-t border-slate-200 text-xs font-semibold dark:border-slate-800">
                                <form method="POST" action="{{ route('admin.web-apps.toggle', $app['slug']) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-minimal-ghost text-xs px-2.5 py-1.5">
                                        {{ $app['enabled'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <span class="flex-1"></span>
                                <a href="{{ route('admin.web-apps.show', $app['slug']) }}" class="btn-minimal-ghost text-xs px-2.5 py-1.5">
                                    Detail
                                </a>
                                <a href="{{ route('admin.web-apps.edit', $app['slug']) }}" class="btn-minimal-secondary text-xs px-2.5 py-1.5">
                                    Edit
                                </a>
                                <form
                                    method="POST"
                                    action="{{ route('admin.web-apps.uninstall', $app['slug']) }}"
                                    data-confirm="Hapus aplikasi {{ $app['name'] }}?"
                                    onsubmit="return confirm(this.dataset.confirm);"
                                    class="inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-minimal-ghost text-xs px-2.5 py-1.5 text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-minimal p-4 stagger-in" style="animation-delay: 200ms;">
                    <p class="eyebrow">Status &amp; akses</p>
                    <ul class="mt-2 space-y-1 text-xs text-muted dark:text-slate-400">
                        <li>&bull; <strong class="font-semibold text-ink dark:text-slate-200">Deployed</strong>: file ada di <code class="font-mono text-slate-600 dark:text-slate-400">school-app/{slug}/</code> atau URL akses terisi — muncul di App Hub dengan tombol Buka Aplikasi.</li>
                        <li>&bull; <strong class="font-semibold text-ink dark:text-slate-200">Draft / Not Deployed</strong>: tetap terlihat di halaman ini, tetapi tidak muncul di App Hub dan belum bisa dibuka.</li>
                        <li>&bull; URL akses bawaan aplikasi di server: <code class="font-mono text-slate-600 dark:text-slate-400">/apps/sekolah/{slug}</code> — berbeda dengan URL management <code class="font-mono text-slate-600 dark:text-slate-400">/admin/aplikasi</code>.</li>
                        <li>&bull; Atur <strong class="font-semibold text-ink dark:text-slate-200">URL tujuan</strong> untuk mengarahkan sekolah ke alamat lain, misalnya subdomain, lewat tombol <a href="{{ route('admin.link-access.index') }}" class="font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-300">Kelola link</a>.</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <style>
        body.modal-open { overflow: hidden; }
        dialog.modal[open] { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            max-height: 90vh; 
            overflow: hidden;
            margin: 0;
        }
        dialog.modal::backdrop { background: rgba(2, 6, 23, 0.6); backdrop-filter: blur(4px); }
        dialog.modal::-webkit-scrollbar { display: none; }
    </style>

    <dialog id="linkModal" class="modal">
        <div class="card-minimal p-6 sm:p-8 w-full max-w-lg">
            <h3 class="text-base font-bold text-ink dark:text-slate-100">Tambah aplikasi</h3>
            <p class="mt-1 text-sm text-muted dark:text-slate-500">Daftarkan aplikasi yang dikelola di sini, lalu tampilkan di App Hub sekolah.</p>

            <form method="POST" action="{{ route('admin.web-apps.store') }}" class="mt-5 space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group-minimal">
                        <x-input-label for="name" value="Nama aplikasi" />
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            autofocus
                            placeholder="Contoh: Rapor Digital"
                            value="{{ old('name') }}"
                            class="mt-1 form-input-minimal"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="slug" value="Slug" />
                        <p class="mt-1 text-xs text-muted">Identitas unik, misalnya: <code class="font-mono">rapor</code></p>
                        <input
                            id="slug"
                            name="slug"
                            type="text"
                            required
                            placeholder="contoh: rapor"
                            value="{{ old('slug') }}"
                            pattern="[a-z0-9\-_]+"
                            class="mt-1 form-input-minimal"
                        />
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="access_url" value="URL tujuan" />
                        <p class="mt-1 text-xs text-muted">Isi bila aplikasi sudah bisa dibuka (subdomain/domain lain). Kosongkan untuk menyimpan sebagai draft.</p>
                    <input
                        id="access_url"
                        name="access_url"
                        type="url"
                        placeholder="https://rapor.sekolah.sch.id"
                        value="{{ old('access_url') }}"
                        class="mt-1 form-input-minimal"
                    />
                    <x-input-error :messages="$errors->get('access_url')" class="mt-2" />
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Deskripsi singkat aplikasi..."
                        class="mt-1 form-input-minimal resize-none"
                    >{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="form-group-minimal">
                        <x-input-label for="category" value="Kategori" />
                        <select
                            id="category"
                            name="category"
                            class="mt-1 form-input-minimal appearance-none bg-white dark:bg-slate-800"
                        >
                            @foreach ($categories as $key => $label)
                                <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label for="icon" value="Ikon" />
                        <select
                            id="icon"
                            name="icon"
                            class="mt-1 form-input-minimal appearance-none bg-white dark:bg-slate-800"
                        >
                            @foreach ($icons as $key => $label)
                                <option value="{{ $key }}" @selected(old('icon') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group-minimal">
                    <x-input-label value="Akses Paket Langganan" />
                    <p class="mt-0.5 text-xs text-muted">Pilih paket yang mendapatkan akses. Kosongkan jika berlaku untuk semua paket.</p>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($plans as $plan)
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200/80 dark:border-slate-700/60 bg-white dark:bg-slate-800 hover:border-brand-300 dark:hover:border-slate-600 transition cursor-pointer text-xs font-medium text-ink dark:text-slate-200">
                                <input
                                    type="checkbox"
                                    name="plan_ids[]"
                                    value="{{ $plan->id }}"
                                    class="rounded border-brand-200 text-brand-700 focus:ring-brand-500 dark:border-slate-600 dark:text-brand-400"
                                >
                                <span class="font-bold truncate">{{ $plan->label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('linkModal').close(); document.body.classList.remove('modal-open');" class="btn-minimal-secondary">Batal</button>
                    <x-primary-button class="btn-minimal-primary">Simpan URL</x-primary-button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="uploadModal" class="modal">
        <div class="card-minimal p-6 sm:p-8 w-full max-w-lg">
            <h3 class="text-base font-bold text-ink dark:text-slate-100">Unggah aplikasi (ZIP)</h3>
            <p class="mt-1 text-sm text-muted dark:text-slate-500">
                File diekstrak ke <code class="font-mono">school-app/{slug}</code> lalu langsung bisa dibuka di App Hub.
            </p>

            <form
                method="POST"
                action="{{ route('admin.web-apps.upload') }}"
                enctype="multipart/form-data"
                class="mt-5 space-y-4"
                onsubmit="
                    const btn = this.querySelector('button[type=submit]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<svg class=\'animate-spin -ml-1 mr-2 h-4 w-4 text-white inline\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Memproses Ekstraksi &amp; Database...';
                    }
                    const note = document.getElementById('uploadProgressNote');
                    if (note) { note.classList.remove('hidden'); }
                "
            >
                @csrf

                <div class="form-group-minimal">
                    <x-input-label for="upload_slug" value="Slug" />
                    <p class="mt-1 text-xs text-muted">
                        Identitas folder aplikasi, misalnya <code class="font-mono">ujian</code>. Isi slug yang sudah ada bila ingin mengganti versi.
                    </p>
                    <input
                        id="upload_slug"
                        name="slug"
                        type="text"
                        required
                        autofocus
                        placeholder="contoh: ujian"
                        value="{{ old('slug') }}"
                        pattern="[a-z0-9\-_]+"
                        class="mt-1 form-input-minimal"
                    />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="app_zip" value="File aplikasi (ZIP)" />
                    <p class="mt-1 text-xs text-muted">
                        Maksimal 64MB. Database dibuat otomatis dari <code class="font-mono">database/migrations/*.php</code> atau file <code class="font-mono">.sql</code> — kalau gagal, instalasi dibatalkan. File <code class="font-mono">.env</code> tidak diizinkan.
                    </p>
                    <input
                        id="app_zip"
                        name="app_zip"
                        type="file"
                        accept=".zip,application/zip"
                        required
                        class="mt-1 form-input-minimal"
                    />
                    <x-input-error :messages="$errors->get('app_zip')" class="mt-2" />
                </div>

                <p id="uploadProgressNote" class="hidden text-xs text-brand-600 dark:text-brand-400 font-medium animate-pulse">
                    Sedang mengunggah, mengekstrak file, dan menjalankan skema database. Mohon tunggu, proses ini membutuhkan beberapa detik...
                </p>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('uploadModal').close(); document.body.classList.remove('modal-open');" class="btn-minimal-secondary">Batal</button>
                    <x-primary-button class="btn-minimal-primary">Unggah &amp; Pasang</x-primary-button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function openModal(id) {
            document.getElementById(id).showModal();
            document.body.classList.add('modal-open');
            history.replaceState(null, '', window.location.pathname + window.location.search);
        }

        if (window.location.hash === '#tambah') {
            openModal('linkModal');
        } else if (window.location.hash === '#unggah') {
            openModal('uploadModal');
        }

        ['linkModal', 'uploadModal'].forEach(function (id) {
            document.getElementById(id).addEventListener('close', function () {
                document.body.classList.remove('modal-open');
            });
        });
    </script>
</x-admin-layout>