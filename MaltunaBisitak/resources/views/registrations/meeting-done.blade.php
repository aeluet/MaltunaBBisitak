<x-layouts.public :title="__('kiosk.mobile.thanks', ['name' => $name])">
    <x-card class="p-8 text-center">
        <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-700" aria-hidden="true">&check;</div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('kiosk.mobile.thanks', ['name' => $name]) }}</h1>
        <p class="mt-3 text-slate-600">{{ __('kiosk.mobile.meeting_done', ['meeting' => $meeting->title]) }}</p>
    </x-card>
</x-layouts.public>
