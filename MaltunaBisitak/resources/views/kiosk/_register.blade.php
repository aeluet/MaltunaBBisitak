{{-- Shared by meetings and groups: fill in the form here or continue on the phone through the QR. --}}
<a href="{{ route('kiosk.home') }}" class="text-lg text-brand-700 hover:underline">&larr; {{ __('kiosk.register.back') }}</a>

<h1 class="mt-4 text-4xl font-semibold text-slate-900">{{ $heading }}</h1>
<p class="mt-2 text-xl text-slate-600">{{ $subheading }}</p>

<div class="mt-8 grid gap-8 md:grid-cols-3">
    <form method="POST" action="{{ $action }}" class="space-y-5 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 md:col-span-2">
        @csrf
        <h2 class="text-2xl font-semibold">{{ __('kiosk.register.heading') }}</h2>

        @include('registrations._fields', ['origin' => $origin])

        <x-button size="xl" class="w-full">{{ __('kiosk.register.submit') }}</x-button>
    </form>

    <div class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
        <x-qr-code :data="$phoneUrl" :size="220" class="mx-auto" />
        <p class="mt-4 text-lg text-slate-600">{{ __('kiosk.register.scan_phone') }}</p>
    </div>
</div>
