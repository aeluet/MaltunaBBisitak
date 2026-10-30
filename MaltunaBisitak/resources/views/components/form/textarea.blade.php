@props(['name', 'label', 'value' => null, 'rows' => 3, 'id' => null])

{{-- Pass an id when several forms on one page post the same field name. --}}
<div>
    <label for="{{ $id ?? $name }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <textarea id="{{ $id ?? $name }}" name="{{ $name }}" rows="{{ $rows }}"
              {{ $attributes->class([
                  'w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-brand-100 focus:outline-none',
                  'border-red-400' => $errors->has($name),
                  'border-slate-300 focus:border-brand-600' => ! $errors->has($name),
              ]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
