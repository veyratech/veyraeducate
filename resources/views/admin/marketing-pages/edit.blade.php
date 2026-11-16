<x-admin-layout title="Edit {{ $marketingPage->slug }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <p class="eyebrow">Halaman Marketing</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Edit /{{ $marketingPage->slug }}</h2>
            </div>
            <a href="{{ route('admin.marketing-pages.index') }}" class="btn-minimal-secondary text-xs py-1.5 px-3 mt-1">Kembali</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-2xl">
            <form method="POST" action="{{ route('admin.marketing-pages.update', $marketingPage) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="form-group-minimal">
                    <x-input-label for="slug" value="Slug (Tidak bisa diubah)" />
                    <x-text-input id="slug" class="mt-1 form-input-minimal font-mono bg-slate-100 dark:bg-slate-800" name="slug" value="{{ $marketingPage->slug }}" readonly />
                    <p class="text-xs text-muted dark:text-slate-500 mt-1">URL publik: <code class="font-mono">{{ url($marketingPage->slug) }}</code></p>
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="title" value="Judul Halaman" />
                    <x-text-input id="title" class="mt-1 form-input-minimal w-full" name="title" :value="old('title', $marketingPage->title)" required maxlength="255" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea id="description" name="description" rows="3" class="mt-1 form-input-minimal w-full resize-none">{{ old('description', $marketingPage->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="eyebrow" value="Eyebrow (Label Kecil)" />
                    <x-text-input id="eyebrow" class="mt-1 form-input-minimal w-full" name="eyebrow" :value="old('eyebrow', $marketingPage->eyebrow)" maxlength="100" />
                    <x-input-error :messages="$errors->get('eyebrow')" class="mt-1" />
                </div>

                <div class="form-group-minimal">
                    <x-input-label for="hero_image" value="Gambar Hero (URL)" />
                    <x-text-input id="hero_image" class="mt-1 form-input-minimal w-full" name="hero_image" :value="old('hero_image', $marketingPage->hero_image)" maxlength="255" />
                    <p class="text-xs text-muted dark:text-slate-500 mt-1">Biarkan kosong untuk menggunakan layout bawaan tanpa gambar.</p>
                    <x-input-error :messages="$errors->get('hero_image')" class="mt-1" />
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="form-group-minimal">
                        <x-input-label for="cta_text" value="Teks Tombol CTA" />
                        <x-text-input id="cta_text" class="mt-1 form-input-minimal w-full" name="cta_text" :value="old('cta_text', $marketingPage->cta_text)" maxlength="100" />
                        <x-input-error :messages="$errors->get('cta_text')" class="mt-1" />
                    </div>
                    <div class="form-group-minimal">
                        <x-input-label for="cta_url" value="URL Tombol CTA" />
                        <x-text-input id="cta_url" class="mt-1 form-input-minimal w-full" name="cta_url" :value="old('cta_url', $marketingPage->cta_url)" maxlength="255" />
                        <x-input-error :messages="$errors->get('cta_url')" class="mt-1" />
                    </div>
                </div>

                <div class="flex items-center pt-2">
                    <label for="is_active" class="flex cursor-pointer items-center gap-2">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $marketingPage->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-500" />
                        <span class="text-xs font-semibold text-ink dark:text-slate-200">Halaman aktif ditampilkan ke publik</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('admin.marketing-pages.index') }}" class="btn-minimal-secondary text-sm">Batal</a>
                    <x-primary-button class="btn-minimal-primary text-sm">Simpan Perubahan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
