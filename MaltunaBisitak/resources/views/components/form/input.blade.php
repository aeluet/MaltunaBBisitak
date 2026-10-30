@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">
        {{ $label }}
        @if ($attributes->get('required'))
            <span class="text-red-600">*</span>
        @endif
    </label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
           {{ $attributes->class([
               'w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-brand-100 focus:outline-none',
               'border-red-400' => $errors->has($name),
               'border-slate-300 focus:border-brand-600' => ! $errors->has($name),
           ]) }}>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
