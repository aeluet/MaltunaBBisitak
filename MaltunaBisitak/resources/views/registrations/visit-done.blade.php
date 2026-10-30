<x-layouts.public :title="__('kiosk.mobile.thanks', ['name' => $name])">
    <x-card class="p-8 text-center">
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('kiosk.mobile.thanks', ['name' => $name]) }}</h1>
        <p class="mt-3 text-slate-600">{{ __('kiosk.mobile.visit_done') }}</p>

        <x-qr-code :data="$code" :size="240" class="mx-auto mt-6" />
        <p class="mt-3 font-mono text-xl tracking-[0.3em]">{{ $code }}</p>

        <p class="mt-6 text-sm text-slate-500">{{ $visit->origin }} · {{ $visit->visit_date->isoFormat('dddd, LL') }}</p>
    </x-card>
</x-layouts.public>
