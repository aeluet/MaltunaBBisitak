<x-layouts.public :title="__('public.thanks.title')">
    <x-card class="p-8 text-center">
        <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-700" aria-hidden="true">&check;</div>

        <h1 class="text-2xl font-semibold text-slate-900">{{ __('public.thanks.title') }}</h1>
        <p class="mt-3 text-slate-600">{{ __('public.thanks.message') }}</p>

        @if (session('booking_date'))
            <p class="mt-2 font-medium">
                {{ __('public.thanks.date', ['date' => \Carbon\CarbonImmutable::parse(session('booking_date'))->isoFormat('dddd, LL')]) }}
            </p>
        @endif

        <a href="{{ route('bookings.create') }}" class="mt-6 inline-block text-brand-700 hover:underline">{{ __('public.thanks.back') }}</a>
    </x-card>
</x-layouts.public>
