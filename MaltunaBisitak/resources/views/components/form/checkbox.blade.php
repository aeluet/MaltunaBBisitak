@props(['name', 'label', 'checked' => false])

<div>
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked))
               {{ $attributes->class('rounded border-slate-300 text-brand-600') }}>
        {{ $label }}
    </label>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
