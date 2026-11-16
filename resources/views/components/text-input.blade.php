@props(['type' => 'text', 'name', 'value' => null, 'autocomplete' => null, 'autofocus' => false, 'placeholder' => null, 'required' => false, 'id' => null])

<input
    {{ $attributes->merge([
        'type' => $type,
        'name' => $name,
        'id' => $id ?? $name,
        'value' => old($name, $value),
        'required' => $required,
        'autofocus' => $autofocus,
        'autocomplete' => $autocomplete,
        'placeholder' => $placeholder,
        'class' => 'block w-full rounded-xl border-brand-200 bg-white text-ink shadow-sm placeholder:text-muted/70 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:placeholder:text-slate-500',
    ]) }}
/>
