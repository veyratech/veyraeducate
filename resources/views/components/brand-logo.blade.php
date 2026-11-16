@props([
    'size' => 'md',
    'withText' => true,
    'loading' => 'eager',
    'fetchPriority' => 'high',
])

@php
    $sizes = [
        'sm' => 'h-8',
        'md' => 'h-10',
        'lg' => 'h-12',
        'xl' => 'h-16',
    ];
    $logoClass = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <img
        src="{{ asset('images/logo-veyra-educate.png') }}"
        alt="{{ config('app.name') }}"
        width="852"
        height="838"
        class="{{ $logoClass }} w-auto shrink-0 object-contain"
        loading="{{ $loading ?? 'eager' }}"
        fetchpriority="{{ $fetchPriority ?? 'high' }}"
        decoding="async"
    />
    @if ($withText)
        <span class="flex min-w-0 flex-col leading-tight">
            <span class="truncate text-base font-extrabold tracking-tight text-ink sm:text-lg dark:text-slate-100">
                {{ config('app.name') }}
            </span>
            <span class="hidden text-[11px] font-medium uppercase tracking-[0.16em] text-brand-700 sm:block">
                Wadah Aplikasi Sekolah
            </span>
        </span>
    @endif
</span>
