@props(['for' => null, 'value' => null])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-ink dark:text-slate-200']) }}@if ($for) for="{{ $for }}"@endif>
    {{ $value }}
</label>
