@props(['href' => null, 'variant' => 'primary'])

@php
    $classes = match ($variant) {
        'secondary' => 'bg-white text-slate-700 ring-1 ring-slate-300 hover:bg-slate-50',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        default => 'bg-brand-600 text-white hover:bg-brand-700',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class("inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium $classes") }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class("inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium $classes") }}>{{ $slot }}</button>
@endif
