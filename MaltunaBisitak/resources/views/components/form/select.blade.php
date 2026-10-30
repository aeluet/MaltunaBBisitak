@props(['name', 'label', 'options', 'selected' => null, 'placeholder' => null])

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
    <select id="{{ $id }}" name="{{ $name }}"
            {{ $attributes->class([
                'w-full rounded-md border bg-white px-3 py-2 focus:ring-2 focus:ring-brand-100 focus:outline-none',
                'border-red-400' => $errors->has($key),
                'border-slate-300 focus:border-brand-600' => ! $errors->has($key),
            ]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($key, $selected) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
