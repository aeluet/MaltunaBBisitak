@props(['title' => null, 'idleSeconds' => 90, 'labelUrl' => null])

{{-- Full-screen touch layout for the entrance kiosk; it returns to the start screen when left alone. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ __('public.school') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col" data-kiosk data-home-url="{{ route('kiosk.home') }}" data-idle-seconds="{{ $idleSeconds }}"
         @if ($labelUrl) data-label-url="{{ $labelUrl }}" @endif>
        <header class="bg-brand-800 text-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-6 py-5">
                <a href="{{ route('kiosk.home') }}" class="text-2xl font-semibold">{{ __('public.school') }}</a>

                <nav aria-label="{{ __('public.language') }}" class="flex gap-2">
                    @foreach (['eu' => 'Euskara', 'es' => 'Castellano', 'en' => 'English'] as $locale => $languageName)
                        <a href="{{ route(Route::currentRouteName(), [...request()->route()->parameters(), 'locale' => $locale]) }}"
                           lang="{{ $locale }}" @if (app()->isLocale($locale)) aria-current="true" @endif
                           @class([
                               'rounded-lg px-4 py-2 text-lg font-medium',
                               'bg-white text-brand-800' => app()->isLocale($locale),
                               'hover:bg-white/10' => ! app()->isLocale($locale),
                           ])>{{ $languageName }}</a>
                    @endforeach
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-5xl flex-1 px-6 py-10">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
