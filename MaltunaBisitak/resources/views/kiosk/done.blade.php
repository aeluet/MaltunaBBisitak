{{-- One label after a registration or a scanned code, or the whole group after "print all labels". --}}
@php
    $labelUrls = $labelUrls ?? [$labelUrl];
    $isGroup = count($labelUrls) > 1;
@endphp

{{-- The browser saves one label every second, so stay on this screen until all of them are downloaded. --}}
<x-layouts.kiosk :idle-seconds="max(10, count($labelUrls) + 8)" :label-urls="$labelUrls">
    <div class="flex min-h-0 flex-1 items-center justify-center">
        <div class="w-full max-w-3xl rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
            <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-emerald-100 text-4xl text-emerald-700" aria-hidden="true">&check;</div>

            <p class="text-3xl font-bold text-brand-800" role="status">{{ $message }}</p>
            <p class="mt-4 text-xl text-slate-600">{{ $isGroup ? __('kiosk.done.labels') : __('kiosk.done.label') }}</p>

            @if ($isGroup)
                <button type="button" data-kiosk-print-labels class="mt-8 inline-block text-lg text-brand-700 underline">{{ __('kiosk.done.labels_retry') }}</button>
            @else
                <a href="{{ $labelUrls[0] }}" class="mt-8 inline-block text-lg text-brand-700 underline">{{ __('kiosk.done.label_retry') }}</a>
            @endif
        </div>
    </div>
</x-layouts.kiosk>
