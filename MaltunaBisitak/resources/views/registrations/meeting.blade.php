<x-layouts.public :title="__('kiosk.mobile.meeting_heading')">
    <h1 class="text-3xl font-semibold text-slate-900">{{ __('kiosk.mobile.meeting_heading') }}</h1>

    <x-card class="mt-4 p-5">
        <p class="text-lg font-semibold">{{ $meeting->title }}</p>
        <p class="text-slate-600">
            {{ $meeting->date->isoFormat('dddd, LL') }}{{ $meeting->starts_at ? ', '.$meeting->starts_at : '' }} · {{ $meeting->location }}
        </p>
    </x-card>

    @if ($isOpen)
        <form method="POST" action="{{ route('registrations.meetings.store', $meeting) }}" class="mt-6 space-y-6">
            @csrf
            <x-card class="space-y-5 p-6">
                @include('registrations._fields', ['origin' => null])
            </x-card>
            <x-button size="lg" class="w-full sm:w-auto">{{ __('kiosk.mobile.submit') }}</x-button>
        </form>
    @else
        <x-card class="mt-6 p-6 text-slate-600">{{ __('kiosk.mobile.closed') }}</x-card>
    @endif
</x-layouts.public>
