@props(['title' => null, 'idleSeconds' => 90, 'labelUrl' => null, 'labelUrls' => []])

@php
    $labelUrls = $labelUrls ?: array_filter([$labelUrl]);
@endphp

{{--
    Full-screen touch layout for the entrance kiosk; it returns to the start screen when left alone.
    It always fills the screen exactly and the page never scrolls: pages share the height of <main>
    with flex-1/min-h-0, and only long lists scroll inside their own box.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ __('public.school') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-dvh overflow-hidden bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="flex h-dvh flex-col overflow-hidden" data-kiosk data-home-url="{{ route('kiosk.home') }}" data-idle-seconds="{{ $idleSeconds }}"
         data-keyboard="{{ json_encode(__('kiosk.keyboard')) }}"
         @if (count($labelUrls) === 1) data-label-url="{{ reset($labelUrls) }}" @endif
         @if ($labelUrls) data-label-urls="{{ json_encode(array_values($labelUrls)) }}" @endif>
        <header class="shrink-0 border-b border-slate-200 bg-white shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-8 py-3">
                <a href="{{ route('kiosk.home') }}" class="shrink-0">
                    <img src="{{ asset('images/miguel-altuna-logoa.svg') }}" alt="{{ __('public.school') }}" class="h-14 w-auto">
                </a>

                <nav aria-label="{{ __('public.language') }}" class="flex gap-2">
                    @foreach (['eu' => 'Euskara', 'es' => 'Castellano', 'en' => 'English'] as $locale => $languageName)
                        <a href="{{ route(Route::currentRouteName(), [...request()->route()->parameters(), 'locale' => $locale]) }}"
                           lang="{{ $locale }}" @if (app()->isLocale($locale)) aria-current="true" @endif
                           @class([
                               'rounded-lg px-4 py-2 text-lg font-medium',
                               'btn-brand' => app()->isLocale($locale),
                               'text-brand-800 hover:bg-brand-50' => ! app()->isLocale($locale),
                           ])>{{ $languageName }}</a>
                    @endforeach
                </nav>
            </div>
        </header>

        <main class="mx-auto flex min-h-0 w-full max-w-7xl flex-1 flex-col px-8 py-6">
            @if (session('kiosk_error'))
                <div class="mb-4 shrink-0 rounded-2xl bg-red-50 px-6 py-4 text-xl text-red-800 ring-1 ring-red-200" role="alert">
                    {{ session('kiosk_error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="bg-brand-gradient shrink-0 text-white/80">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-x-6 gap-y-1 px-8 py-3 text-sm">
                <span>&copy; {{ now()->year }} {{ __('public.school') }}</span>
                <span class="text-white/70">{{ __('public.credits') }}</span>
            </div>
        </footer>
    </div>
</body>
</html>
