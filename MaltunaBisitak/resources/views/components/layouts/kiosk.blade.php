@props(['title' => null, 'idleSeconds' => 90, 'labelUrl' => null])

{{-- Full-screen touch layout for the entrance kiosk; it returns to the start screen when left alone. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ __('public.school') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col" data-kiosk data-home-url="{{ route('kiosk.home') }}" data-idle-seconds="{{ $idleSeconds }}"
         @if ($labelUrl) data-label-url="{{ $labelUrl }}" @endif>
        <header class="border-b border-slate-200 bg-white shadow-sm">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-6 py-5">
                <a href="{{ route('kiosk.home') }}" class="shrink-0">
                    <img src="{{ asset('images/miguel-altuna-logoa.svg') }}" alt="{{ __('public.school') }}" class="h-16 w-auto">
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

        <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-10">
            {{ $slot }}
        </main>

        <footer class="bg-brand-gradient h-3" aria-hidden="true"></footer>
    </div>
</body>
</html>
