<x-admin-layout title="Pengguna — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Akun</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Pengguna</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ modalOpen: {{ $errors->any() ? 'true' : 'false' }}, selectedRole: '{{ old('role', 'school') }}' }">
        <div class="container-site space-y-6">

            @if (session('status'))
                <div class="alert-success stagger-in">
                    <p class="text-xs font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error stagger-in">
                    <p class="text-xs font-semibold">Data pengguna belum tersimpan</p>
                    <ul class="mt-2 space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>&bull; {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Toolbar: Filter & Pencarian --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-1 flex-wrap gap-2 max-w-xl stagger-in">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <x-text-input class="form-input-minimal !pl-11" type="search" name="q" :value="$search" placeholder="Cari nama, email, sekolah…" />
                    </div>
                    <select name="role" class="form-input-minimal appearance-none bg-white dark:bg-slate-800 py-2 text-xs w-auto">
                        <option value="">Semua Peran</option>
                        <option value="school" {{ $roleFilter === 'school' ? 'selected' : '' }}>Kepala Sekolah / Admin Utama</option>
                        <option value="operator" {{ $roleFilter === 'operator' ? 'selected' : '' }}>Operator Dapodik &amp; IT</option>
                        <option value="bendahara" {{ $roleFilter === 'bendahara' ? 'selected' : '' }}>Bendahara BOS</option>
                        <option value="teacher" {{ $roleFilter === 'teacher' ? 'selected' : '' }}>Guru / Pengajar</option>
                        <option value="staff" {{ $roleFilter === 'staff' ? 'selected' : '' }}>Staf Tata Usaha</option>
                        <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin Sistem</option>
                        <option value="super_admin" {{ $roleFilter === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    <x-primary-button class="btn-minimal-primary whitespace-nowrap">Cari</x-primary-button>
                    @if ($search !== '' || $roleFilter !== '')
                        <a href="{{ route('admin.users.index') }}" class="btn-minimal-secondary whitespace-nowrap">Reset</a>
                    @endif
                </form>

                <button
                    type="button"
                    @click="modalOpen = true"
                    class="btn-minimal-primary whitespace-nowrap"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pengguna
                </button>
            </div>

            {{-- Popup Modal Tambah Pengguna --}}
            <div
                x-show="modalOpen"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
                role="dialog"
                aria-modal="true"
                @keydown.escape.window="modalOpen = false"
            >
                {{-- Backdrop Blur --}}
                <div
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
                    @click="modalOpen = false"
                ></div>

                {{-- Modal Card Content --}}
                <div
                    x-show="modalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="relative w-full max-w-lg card-minimal p-6 sm:p-7 shadow-2xl z-10 my-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800"
                    @click.stop
                >
                    <div class="flex items-start justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-ink dark:text-slate-100">Tambah Pengguna Baru</h3>
                            <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Buat akun untuk staf sekolah atau administrator platform.</p>
                        </div>
                        <button
                            type="button"
                            @click="modalOpen = false"
                            class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            aria-label="Tutup"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                        @csrf

                        <div class="form-group-minimal">
                            <x-input-label for="user_name" value="Nama Lengkap" />
                            <x-text-input id="user_name" class="mt-1 form-input-minimal w-full" name="name" :value="old('name')" required placeholder="Nama lengkap pengguna" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group-minimal">
                                <x-input-label for="user_email" value="Alamat Email" />
                                <x-text-input id="user_email" type="email" class="mt-1 form-input-minimal w-full" name="email" :value="old('email')" required placeholder="nama@email.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="user_password" value="Kata Sandi" />
                                <x-text-input id="user_password" type="password" class="mt-1 form-input-minimal w-full" name="password" required placeholder="Minimal 8 karakter" />
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group-minimal">
                                <x-input-label for="user_role" value="Peran / Role" />
                                <select id="user_role" name="role" x-model="selectedRole" class="mt-1 form-input-minimal w-full appearance-none bg-white dark:bg-slate-800">
                                    <option value="school">Kepala Sekolah / Admin Utama</option>
                                    <option value="operator">Operator Dapodik &amp; IT</option>
                                    <option value="bendahara">Bendahara BOS</option>
                                    <option value="teacher">Guru / Pengajar</option>
                                    <option value="staff">Staf Tata Usaha</option>
                                    <option value="admin">Administrator Sistem</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-1" />
                            </div>

                            <div class="form-group-minimal">
                                <x-input-label for="user_phone" value="No. Telepon / WhatsApp (Opsional)" />
                                <x-text-input id="user_phone" class="mt-1 form-input-minimal w-full" name="phone" :value="old('phone')" placeholder="08xxxxxxxxxx" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                            </div>
                        </div>

                        <div class="form-group-minimal" x-show="selectedRole !== 'admin'">
                            <x-input-label for="user_school_id" value="Sekolah (Tenant)" />
                            <select id="user_school_id" name="school_id" class="mt-1 form-input-minimal w-full appearance-none bg-white dark:bg-slate-800">
                                <option value="">— Tanpa Sekolah / Bukan Staf —</option>
                                @foreach ($schools as $school)
                                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('school_id')" class="mt-1" />
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="modalOpen = false" class="btn-minimal-secondary text-sm">
                                Batal
                            </button>
                            <x-primary-button class="btn-minimal-primary text-sm">
                                Simpan Pengguna
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="card-minimal overflow-hidden stagger-in" style="animation-delay: 60ms;">
                <div class="overflow-x-auto">
                    <table class="table-minimal table-responsive-card">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Pengguna</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Peran</th>
                                <th class="px-4 py-3">Sekolah</th>
                                <th class="px-4 py-3">Terdaftar</th>
                                <th class="px-4 py-3">Login Terakhir</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td data-label="Pengguna" class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="avatar-minimal-md {{ $user->role === 'admin' ? 'avatar-minimal-brand' : 'avatar-minimal-sky' }}">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-ink dark:text-slate-100">{{ $user->name }}</p>
                                                @if ($user->phone)
                                                    <p class="truncate text-[11px] text-muted dark:text-slate-500">{{ $user->phone }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Email" class="px-4 py-3 text-muted dark:text-slate-500">{{ $user->email }}</td>
                                    <td data-label="Peran" class="px-4 py-3">
                                        @if ($user->role === 'admin')
                                            <span class="badge-minimal badge-minimal-active">Admin</span>
                                        @else
                                            <span class="badge-minimal badge-minimal-draft">{{ $user->roleLabel() }}</span>
                                        @endif
                                    </td>
                                    <td data-label="Sekolah" class="px-4 py-3 text-muted dark:text-slate-500">{{ $user->school?->name ?? '—' }}</td>
                                    <td data-label="Terdaftar" class="px-4 py-3 text-xs text-muted dark:text-slate-500">{{ optional($user->created_at)->format('d M Y') ?? '—' }}</td>
                                    <td data-label="Login Terakhir" class="px-4 py-3 text-xs text-muted dark:text-slate-500">
                                        @if ($user->last_login_at)
                                            {{ $user->last_login_at->format('d M Y, H:i') }}
                                        @else
                                            <span class="text-slate-400 dark:text-slate-600">Belum pernah</span>
                                        @endif
                                    </td>
                                    <td data-label="Aksi" class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center justify-end gap-3">
                                            <button
                                                type="button"
                                                data-update-url="{{ route('admin.users.update', $user) }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone ?? '' }}"
                                                data-role="{{ $user->role }}"
                                                data-school-id="{{ $user->school_id ?? '' }}"
                                                onclick="openEditUserModal(this)"
                                                class="text-xs text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 transition font-medium"
                                                title="Edit pengguna / reset kata sandi"
                                            >
                                                Edit
                                            </button>
                                            @if (auth()->id() !== $user->id)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 transition font-medium" title="Hapus pengguna">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[11px] text-slate-400 dark:text-slate-600 italic">Akun Anda</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="empty-minimal py-4">
                                            <div class="empty-minimal-icon avatar-minimal-sky">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            </div>
                                            <p class="empty-minimal-title">Belum ada pengguna</p>
                                            <p class="empty-minimal-desc">Tambahkan pengguna baru atau pengguna akan muncul saat sekolah terdaftar</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Pengguna & Reset Kata Sandi --}}
    <dialog id="editUserModal" class="modal">
        <div class="card-minimal p-6 sm:p-7 max-w-lg w-[calc(100vw-2rem)] sm:w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl">
            <div class="flex items-start justify-between gap-3 mb-5 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-ink dark:text-slate-100">Edit Pengguna &amp; Reset Kata Sandi</h3>
                    <p class="mt-0.5 text-xs text-muted dark:text-slate-400">Perbarui profil pengguna, penempatan sekolah, atau atur ulang kata sandi.</p>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('editUserModal').close()"
                    class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editUserForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="form-group-minimal">
                    <x-input-label for="edit_user_name" value="Nama Lengkap" />
                    <x-text-input id="edit_user_name" class="mt-1 form-input-minimal w-full" name="name" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group-minimal">
                        <x-input-label for="edit_user_email" value="Alamat Email" />
                        <x-text-input id="edit_user_email" type="email" class="mt-1 form-input-minimal w-full" name="email" required />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="edit_user_phone" value="No. Telepon / WhatsApp" />
                        <x-text-input id="edit_user_phone" class="mt-1 form-input-minimal w-full" name="phone" placeholder="08xxxxxxxxxx" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group-minimal">
                        <x-input-label for="edit_user_role" value="Peran / Role" />
                        <select id="edit_user_role" name="role" onchange="toggleEditSchoolSelect(this.value)" class="mt-1 form-input-minimal w-full appearance-none bg-white dark:bg-slate-800">
                            <option value="school">Kepala Sekolah / Admin Utama</option>
                            <option value="operator">Operator Dapodik &amp; IT</option>
                            <option value="bendahara">Bendahara BOS</option>
                            <option value="teacher">Guru / Pengajar</option>
                            <option value="staff">Staf Tata Usaha</option>
                            <option value="admin">Administrator Sistem</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="form-group-minimal" id="edit_user_school_wrap">
                        <x-input-label for="edit_user_school_id" value="Sekolah (Tenant)" />
                        <select id="edit_user_school_id" name="school_id" class="mt-1 form-input-minimal w-full appearance-none bg-white dark:bg-slate-800">
                            <option value="">— Tanpa Sekolah —</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group-minimal pt-2 border-t border-slate-100 dark:border-slate-800">
                    <x-input-label for="edit_user_password" value="Reset Kata Sandi Baru (Opsional)" />
                    <x-text-input id="edit_user_password" type="password" class="mt-1 form-input-minimal w-full" name="password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" />
                    <p class="mt-1 text-[11px] text-muted dark:text-slate-500">Minimal 8 karakter apabila diisi.</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('editUserModal').close()" class="btn-minimal-secondary text-sm">
                        Batal
                    </button>
                    <x-primary-button class="btn-minimal-primary text-sm">
                        Simpan Perubahan
                    </x-primary-button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function toggleEditSchoolSelect(role) {
            const wrap = document.getElementById('edit_user_school_wrap');
            if (wrap) {
                wrap.style.display = role === 'admin' ? 'none' : '';
            }
        }

        function openEditUserModal(btn) {
            const form = document.getElementById('editUserForm');
            form.action = btn.dataset.updateUrl || '';
            document.getElementById('edit_user_name').value = btn.dataset.name || '';
            document.getElementById('edit_user_email').value = btn.dataset.email || '';
            document.getElementById('edit_user_phone').value = btn.dataset.phone || '';
            document.getElementById('edit_user_role').value = btn.dataset.role || 'school';
            document.getElementById('edit_user_school_id').value = btn.dataset.schoolId || '';
            document.getElementById('edit_user_password').value = '';
            toggleEditSchoolSelect(btn.dataset.role || 'school');
            document.getElementById('editUserModal').showModal();
        }
    </script>
</x-admin-layout>