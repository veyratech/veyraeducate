@props([
    'variant' => 'default',
])

@php
    $btnClass = match($variant) {
        'gold', '3d' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-gold-500 hover:bg-gold-400 text-slate-900 font-bold px-6 py-3 text-sm shadow-lg shadow-gold-500/25 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-gold-500/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-60',
        default => 'inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-brand-200 hover:bg-brand-50/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-60',
    };
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $btnClass]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $btnClass]) }}>
        {{ $slot }}
    </button>
@endif