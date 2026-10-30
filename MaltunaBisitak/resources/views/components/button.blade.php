@props(['href' => null, 'variant' => 'primary', 'size' => 'md'])

@php
    $classes = match ($variant) {
        'secondary' => 'bg-white text-slate-700 ring-1 ring-slate-300 hover:bg-slate-50',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        default => 'bg-brand-600 text-white hover:bg-brand-700',
    };

    // Size classes live here because extra "text-lg px-8" classes would lose against "text-sm px-4" in the CSS.
    $classes .= match ($size) {
        'lg' => ' px-6 py-3 text-base',
        'xl' => ' px-8 py-4 text-lg',
        default => ' px-4 py-2 text-sm',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class("inline-flex items-center justify-center rounded-md font-medium $classes") }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class("inline-flex items-center justify-center rounded-md font-medium $classes") }}>{{ $slot }}</button>
@endif
