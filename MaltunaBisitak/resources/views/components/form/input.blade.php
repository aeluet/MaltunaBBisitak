@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

@php
    // "visitors[0][email]" is posted as visitors.0.email: old input and errors use the dotted key.
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = str_replace('.', '-', $key);
@endphp

<div>
    <label for="{{ $id }}" class="mb-1 block text-sm font-medium">
        {{ $label }}
        @if ($attributes->get('required'))
            <span class="text-red-600">*</span>
        @endif
    </label>
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
           @if ($type !== 'password') value="{{ old($key, $value) }}" @endif
           {{ $attributes->class([
               'w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-brand-100 focus:outline-none',
               'border-red-400' => $errors->has($key),
               'border-slate-300 focus:border-brand-600' => ! $errors->has($key),
           ]) }}>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
