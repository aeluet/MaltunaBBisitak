<x-layouts.kiosk :idle-seconds="10" :label-url="$labelUrl">
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200">
        <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-emerald-100 text-4xl text-emerald-700" aria-hidden="true">&check;</div>

        <p class="text-3xl font-semibold text-slate-900" role="status">{{ $message }}</p>
        <p class="mt-4 text-xl text-slate-600">{{ __('kiosk.done.label') }}</p>

        <a href="{{ $labelUrl }}" class="mt-8 inline-block text-lg text-brand-700 underline">{{ __('kiosk.done.label_retry') }}</a>
    </div>
</x-layouts.kiosk>
