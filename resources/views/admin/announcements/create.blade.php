<x-admin-layout title="Tambah Pengumuman — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div>
            <p class="eyebrow">Pengumuman</p>
            <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Tambah Pengumuman</h2>
            <p class="mt-1 text-sm text-muted dark:text-slate-400"><a href="{{ route('admin.announcements.index') }}" class="link-muted">← Kembali</a></p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-3xl">
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="card-minimal space-y-5 p-6 stagger-in">
                @csrf

                <div>
                    <x-input-label for="title" value="Judul" />
                    <x-text-input id="title" class="mt-1 form-input-minimal" name="title" :value="old('title')" required maxlength="255" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="body" value="Isi pengumuman" />
                    <textarea
                        id="body"
                        name="body"
                        rows="8"
                        required
                        class="mt-1 form-input-minimal"
                        placeholder="Tulis isi pengumuman di sini..."
                    >{{ old('body') }}</textarea>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <x-input-label for="published_at" value="Tanggal terbit" />
                        <input
                            id="published_at"
                            name="published_at"
                            type="datetime-local"
                            value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                            class="mt-1 form-input-minimal"
                        >
                        <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                    </div>
                    <div class="flex items-center pt-7">
                        <label for="is_active" class="flex cursor-pointer items-center gap-2">
                            <input
                                id="is_active"
                                name="is_active"
                                type="checkbox"
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500"
                            >
                            <span class="text-sm font-semibold text-ink dark:text-slate-200">Publikasikan sekarang</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button class="btn-minimal-primary">Buat pengumuman</x-primary-button>
                    <a href="{{ route('admin.announcements.index') }}" class="link-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
