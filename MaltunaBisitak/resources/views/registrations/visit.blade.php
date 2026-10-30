<x-layouts.public :title="__('kiosk.mobile.visit_heading')">
    <h1 class="text-3xl font-semibold text-slate-900">{{ __('kiosk.mobile.visit_heading') }}</h1>

    <x-card class="mt-4 p-5">
        <p class="text-lg font-semibold">{{ $visit->origin }}</p>
        <p class="text-slate-600">{{ $visit->visit_date->isoFormat('dddd, LL') }}</p>
    </x-card>

    @if ($isOpen)
        <form method="POST" action="{{ route('registrations.visits.store', $visit) }}" class="mt-6 space-y-6">
            @csrf
            <x-card class="space-y-5 p-6">
                @include('registrations._fields', ['origin' => $visit->origin])
            </x-card>
            <x-button size="lg" class="w-full sm:w-auto">{{ __('kiosk.mobile.submit') }}</x-button>
        </form>
    @else
        <x-card class="mt-6 p-6 text-slate-600">{{ __('kiosk.mobile.closed') }}</x-card>
    @endif
</x-layouts.public>
