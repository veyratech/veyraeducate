<x-admin-layout title="Halaman Marketing — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <p class="eyebrow">Halaman Marketing</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Demo, Brosur &amp; Proposal</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-5">

            <a href="{{ route('admin.marketing-pages.create') }}" class="btn-minimal-primary inline-flex items-center gap-1.5 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Halaman
            </a>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($pages as $page)
                    <article class="card-minimal overflow-hidden stagger-in flex flex-col">
                        <div class="px-5 py-4 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-ink dark:text-slate-100 truncate">{{ $page->title }}</h3>
                                    <p class="mt-1 text-xs text-muted dark:text-slate-500 font-mono">/{{ $page->slug }}</p>
                                </div>
                                <span class="badge-minimal shrink-0 {{ $page->is_active ? 'badge-minimal-active' : 'badge-minimal-inactive' }}">
                                    {{ $page->is_active ? 'Aktif' : 'Draft' }}
                                </span>
                            </div>
                            @if ($page->description)
                                <p class="mt-3 text-xs text-muted dark:text-slate-500 whitespace-pre-line line-clamp-3">{{ Str::limit($page->description, 160) }}</p>
                            @endif
                            @if ($page->cta_text)
                                <p class="mt-2 text-xs text-brand-600 dark:text-brand-400">CTA: {{ $page->cta_text }}</p>
                            @endif
                        </div>
                        <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3 flex items-center justify-between gap-2">
                            <a href="{{ url($page->slug) }}" target="_blank" rel="noopener noreferrer" class="btn-minimal-ghost text-xs py-1.5 px-2.5">Pratinjau</a>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.marketing-pages.edit', $page) }}" class="btn-minimal-secondary text-xs py-1.5 px-3">Edit</a>
                                <form method="POST" action="{{ route('admin.marketing-pages.destroy', $page) }}" onsubmit="return confirm('Hapus halaman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-minimal-danger text-xs py-1.5 px-3">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-minimal stagger-in col-span-full py-10">
                        <div class="empty-minimal-icon">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="empty-minimal-title">Belum ada halaman marketing.</p>
                        <p class="empty-minimal-desc">
                            <a href="{{ route('admin.marketing-pages.create') }}" class="font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">+ Buat halaman baru</a>
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                <p class="text-xs text-muted dark:text-slate-500">
                    <strong>Catatan:</strong> Halaman Demo, Brosur, dan Proposal tersedia secara default setelah seeder berjalan. Anda dapat mengedit judul, deskripsi, dan CTA dari sini tanpa menyentuh file Blade.
                </p>
            </div>

        </div>
    </div>
</x-admin-layout>
