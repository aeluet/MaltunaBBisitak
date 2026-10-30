@props(['name', 'label', 'options', 'selected' => null, 'placeholder' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">
        {{ $label }}
        @if ($attributes->get('required'))
            <span class="text-red-600">*</span>
        @endif
    </label>
    <select id="{{ $name }}" name="{{ $name }}"
            {{ $attributes->class([
                'w-full rounded-md border bg-white px-3 py-2 focus:ring-2 focus:ring-brand-100 focus:outline-none',
                'border-red-400' => $errors->has($name),
                'border-slate-300 focus:border-brand-600' => ! $errors->has($name),
            ]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
