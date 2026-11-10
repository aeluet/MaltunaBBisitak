{{--
    Shared by meetings and groups: fill in the form here or continue on the phone through the QR.
    Everything fits the screen: the form scrolls inside its card (for example while the on-screen
    keyboard is open), and the optional $aside (the group's sign-in list) takes the rest of the column.
--}}
<div class="flex shrink-0 flex-wrap items-baseline gap-x-6 gap-y-1">
    <a href="{{ $backUrl ?? route('kiosk.home') }}" class="text-lg text-brand-700 hover:underline">&larr; {{ isset($backUrl) ? __('kiosk.register.back_list') : __('kiosk.register.back') }}</a>
    <h1 class="text-4xl font-bold text-brand-800">{{ $heading }}</h1>
    <p class="text-xl text-slate-600">{{ $subheading }}</p>
</div>

<div class="mt-5 grid min-h-0 flex-1 grid-cols-12 gap-6 portrait:grid-cols-1 portrait:grid-rows-[minmax(0,auto)_minmax(0,1fr)]">
    <form method="POST" action="{{ $action }}"
          class="col-span-7 min-h-0 overflow-y-auto overscroll-contain rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 portrait:col-span-1">
        @csrf
        <h2 class="mb-4 text-2xl font-semibold text-brand-800">{{ __('kiosk.register.heading') }}</h2>

        <div class="grid grid-cols-2 gap-x-5 gap-y-4">
            @include('registrations._fields', ['origin' => $origin])

            <x-button size="xl" class="col-span-full w-full">{{ __('kiosk.register.submit') }}</x-button>
        </div>
    </form>

    <div class="col-span-5 flex min-h-0 flex-col gap-6 portrait:col-span-1">
        <div @class([
            'flex shrink-0 items-center gap-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200',
            'flex-1 flex-col justify-center text-center' => empty($aside),
        ])>
            <x-qr-code :data="$phoneUrl" :size="empty($aside) ? 240 : 150" class="shrink-0" />
            <p class="text-lg text-slate-600">{{ __('kiosk.register.scan_phone') }}</p>
        </div>

        {!! $aside ?? '' !!}
    </div>
</div>
