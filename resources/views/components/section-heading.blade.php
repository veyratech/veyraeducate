<div {{ $attributes->merge(['class' => $align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl']) }}>
    @if ($eyebrow)
        <p class="text-sm font-semibold uppercase tracking-wider text-brand-600 {{ $align === 'center' ? 'text-center' : '' }}">
            {{ $eyebrow }}
        </p>
    @endif

    <h2 class="mt-2.5 sm:mt-3 text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
        {{ $title }}
    </h2>

    @if ($lead)
        <p class="mt-3 sm:mt-4 text-sm sm:text-lg leading-relaxed text-slate-600 dark:text-slate-400 {{ $align === 'center' ? 'mx-auto' : '' }}">
            {{ $lead }}
        </p>
    @endif

    <div class="mt-6">
        {{ $slot }}
    </div>
</div>