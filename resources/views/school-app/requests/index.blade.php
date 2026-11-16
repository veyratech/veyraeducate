<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg sm:text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
                Permintaan Aplikasi
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Ajukan aplikasi baru atau pantau status permohonan sekolah</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="container-site max-w-3xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <a href="{{ route('apps.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                    <span>← Kembali ke App Hub</span>
                </a>
                <button type="button" onclick="document.getElementById('requestModal').showModal()" class="btn-primary inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 !text-white font-bold px-4 py-2.5 text-xs shadow-md shadow-brand-600/20 transition active:scale-95 cursor-pointer w-full sm:w-auto" style="color: #ffffff !important;">
                    <svg class="h-4 w-4 !text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color: #ffffff !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="!text-white font-bold" style="color: #ffffff !important;">+ Request Aplikasi Baru</span>
                </button>
            </div>
            @if (session('status'))
                <div class="rounded-xl border border-brand-200 bg-brand-50 px-5 py-4 dark:border-slate-700/50 dark:bg-slate-700/30">
                    <p class="text-sm font-semibold text-brand-800 dark:text-brand-300">{{ session('status') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 dark:border-red-900/50 dark:bg-red-950/30">
                    <p class="text-sm font-semibold text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($pendingCount > 0)
                 <div class="rounded-xl border border-gold-200 bg-gold-50 px-5 py-4 dark:border-slate-700/50 dark:bg-slate-700/30">
                     <p class="text-sm font-semibold text-gold-800 dark:text-gold-300">
                        Ada {{ $pendingCount }} permintaan menunggu persetujuan.
                    </p>
                </div>
            @endif

             <div class="card overflow-hidden dark:bg-slate-850 dark:border-slate-700/50">
                 <div class="border-b border-brand-100 dark:border-slate-700/50 px-5 py-4">
                     <h3 class="text-sm font-bold text-ink dark:text-slate-100">Riwayat permintaan Anda</h3>
                 </div>
                 <div class="divide-y divide-brand-50 dark:divide-slate-700/50">
                    @forelse ($requests as $req)
                         <div class="px-5 py-4 dark:text-slate-200">
                             <div class="flex items-start justify-between gap-4">
                                 <div class="min-w-0 flex-1">
                                     <div class="flex flex-wrap items-center gap-2">
                                         <p class="text-sm font-bold text-ink dark:text-slate-100">{{ $req->name }}</p>
                                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold
                                            {{ match ($req->status) {
                                                'approved' => 'bg-brand-100 text-brand-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                default => 'bg-gold-100 text-gold-800',
                                            } }}">
                                            {{ match ($req->status) {
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                default => 'Menunggu',
                                            } }}
                                        </span>
                                        @if ($req->priority === 'mendesak' && $req->status === 'pending')
                                            <span class="shrink-0 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">
                                                Mendesak
                                            </span>
                                        @endif
                                    </div>
                                     <p class="mt-0.5 text-xs text-muted dark:text-slate-500">
                                         {{ $req->created_at->format('d M Y, H:i') }}
                                         ·
                                         @if ($req->category)
                                             <span class="font-medium text-brand-700 dark:text-brand-400">{{ $req->category }}</span>
                                             ·
                                         @endif
                                         <span>{{ $req->priority ?? 'Normal' }}</span>
                                     </p>
                                     @if ($req->description)
                                         <p class="mt-1 text-xs text-muted dark:text-slate-500">{{ $req->description }}</p>
                                     @endif
                                     @if ($req->target_date)
                                         <p class="mt-1 text-xs text-muted dark:text-slate-500">
                                             Target: <span class="font-medium text-brand-700 dark:text-brand-400">{{ $req->target_date->format('d M Y') }}</span>
                                         </p>
                                     @endif
                                     @if ($req->admin_notes)
                                         <div class="mt-2 rounded-lg border border-brand-200/80 dark:border-brand-800/50 bg-brand-50/70 dark:bg-brand-950/40 px-3 py-2 text-xs text-brand-800 dark:text-brand-300">
                                             <span class="font-semibold">Balasan Admin:</span> {{ $req->admin_notes }}
                                         </div>
                                     @endif
                                 </div>
                                 @if ($req->status === 'pending')
                                     <form method="POST" action="{{ route('school-app.requests.destroy', $req) }}" class="shrink-0" onsubmit="return confirm('Batalkan permintaan aplikasi ini?');">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/30 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-300 hover:bg-red-100 transition">
                                             Batalkan
                                         </button>
                                     </form>
                                 @endif
                             </div>
                         </div>
                     @empty
                         <p class="px-5 py-6 text-sm text-muted dark:text-slate-500">Belum ada permintaan aplikasi custom.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <dialog id="requestModal" class="modal">
        <div class="card p-5 sm:p-8 max-w-lg w-[calc(100vw-2rem)] sm:w-full">
            <div class="mb-5">
                 <h3 class="text-base font-bold text-ink dark:text-slate-100">Formulir Permintaan Aplikasi Custom</h3>
                 <p class="mt-1 text-xs text-muted dark:text-slate-500">
                    Isi form di bawah untuk meminta pengembangan aplikasi sesuai kebutuhan sekolah Anda.
                    Tim kami akan menghubungi Anda setelah permintaan diterima.
                </p>
            </div>

            <form method="POST" action="{{ route('school-app.requests.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Nama aplikasi" />
                    <x-text-input
                        id="name"
                        name="name"
                        class="mt-1 block w-full"
                        placeholder="Contoh: Sistem Rapor Digital, Absensi Online, dll"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="category" value="Kategori aplikasi" />
                    <select
                        id="category"
                        name="category"
                        required
                         class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm dark:bg-slate-800 dark:text-slate-200"
                    >
                        <option value="">— Pilih kategori —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->value }}" {{ old('category') == $cat->value ? 'selected' : '' }}>
                                {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="priority" value="Tingkat prioritas" />
                    <select
                        id="priority"
                        name="priority"
                        required
                         class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm dark:bg-slate-800 dark:text-slate-200"
                    >
                        <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal — sesuai jadwal</option>
                        <option value="tinggi" {{ old('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi — perlu percepatan</option>
                        <option value="mendesak" {{ old('priority') == 'mendesak' ? 'selected' : '' }}>Mendesak — segera mungkin</option>
                    </select>
                    <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Deskripsi kebutuhan" />
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                         class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm dark:bg-slate-800 dark:text-slate-200"
                        placeholder="Jelaskan secara detail fitur atau fungsi aplikasi yang dibutuhkan..."
                    ></textarea>
                     <p class="mt-1 text-[11px] text-muted dark:text-slate-500">Minimal 20 karakter. Jelaskan masalah yang ingin diselesaikan.</p>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="target_date" value="Target selesai (opsional)" />
                    <input
                        type="date"
                        id="target_date"
                        name="target_date"
                        min="{{ now()->toDateString() }}"
                         class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm dark:bg-slate-800 dark:text-slate-200"
                        value="{{ old('target_date') }}"
                    />
                     <p class="mt-1 text-[11px] text-muted dark:text-slate-500">Opsional. Jika ada deadline khusus, isi tanggal di sini.</p>
                    <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="additional_notes" value="Catatan tambahan (opsional)" />
                    <textarea
                        id="additional_notes"
                        name="additional_notes"
                        rows="2"
                         class="mt-1 block w-full rounded-xl border-brand-200 dark:border-slate-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm dark:bg-slate-800 dark:text-slate-200"
                        placeholder="Informasi lain yang mungkin membantu..."
                    ></textarea>
                    <x-input-error :messages="$errors->get('additional_notes')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('requestModal').close()" class="btn-secondary">Batal</button>
                    <x-primary-button>Kirim permintaan</x-primary-button>
                </div>
            </form>
        </div>
    </dialog>
</x-app-layout>
