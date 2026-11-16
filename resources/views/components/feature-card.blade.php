@props([
    'title',
    'body' => null,
    'icon' => null,
    'image' => null,
    'accent' => 'brand',
])

@php
    $accentStyles = [
        'brand'  => ['icon-bg' => 'bg-brand-100 dark:bg-brand-950/50',        'icon-text' => 'text-brand-700 dark:text-brand-300',  'border' => 'border-brand-200 dark:border-brand-800',   'bg-tint' => 'bg-brand-50/50 dark:bg-brand-950/20'],
        'gold'   => ['icon-bg' => 'bg-gold-100 dark:bg-gold-950/50',           'icon-text' => 'text-gold-700 dark:text-gold-300',    'border' => 'border-gold-200 dark:border-gold-800',     'bg-tint' => 'bg-gold-50/60 dark:bg-gold-950/20'],
        'teal'   => ['icon-bg' => 'bg-teal-100 dark:bg-teal-950/50',            'icon-text' => 'text-teal-700 dark:text-teal-300',    'border' => 'border-teal-200 dark:border-teal-800',     'bg-tint' => 'bg-teal-50/50 dark:bg-teal-950/20'],
        'sky'    => ['icon-bg' => 'bg-sky-100 dark:bg-sky-950/50',              'icon-text' => 'text-sky-700 dark:text-sky-300',      'border' => 'border-sky-200 dark:border-sky-800',       'bg-tint' => 'bg-sky-50/50 dark:bg-sky-950/20'],
        'amber'  => ['icon-bg' => 'bg-amber-100 dark:bg-amber-950/50',          'icon-text' => 'text-amber-700 dark:text-amber-300',  'border' => 'border-amber-200 dark:border-amber-800',   'bg-tint' => 'bg-amber-50/50 dark:bg-amber-950/20'],
        'emerald'=> ['icon-bg' => 'bg-emerald-100 dark:bg-emerald-950/50',      'icon-text' => 'text-emerald-700 dark:text-emerald-300','border' => 'border-emerald-200 dark:border-emerald-800','bg-tint' => 'bg-emerald-50/50 dark:bg-emerald-950/20'],
    ];
    $styles = $accentStyles[$accent] ?? $accentStyles['brand'];
@endphp

<article {{ $attributes->merge(['class' => 'flex h-full flex-col rounded-2xl border bg-white dark:bg-slate-900 p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md']) }}>
    @if ($image)
        <div class="mb-4 overflow-hidden rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 aspect-video">
            <img src="{{ \App\Models\HomepageSection::resolveImageUrl($image) }}" alt="{{ $title }}" class="h-full w-full object-cover transition-transform duration-300 hover:scale-105">
        </div>
    @endif
    <div class="flex items-start gap-3">
        @if ($icon)
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $styles['icon-bg'] }} {{ $styles['icon-text'] }}" aria-hidden="true">
                {{ $icon }}
            </span>
        @else
            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $styles['border'] }} {{ str_replace('border', 'text', $styles['border']) }} opacity-60" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </span>
        @endif
        <div class="min-w-0">
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 sm:text-lg">
                {{ $title }}
            </h3>
        </div>
    </div>

    @if ($body)
        <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base">
            {{ $body }}
        </p>
    @endif
</article>