@props(['title' => null])
@use('App\Http\Middleware\SetPublicLocale')

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ __('public.school') }}</title>
    @foreach (SetPublicLocale::LOCALES as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ route(Route::currentRouteName(), [...request()->route()->parameters(), 'locale' => $locale]) }}">
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <header class="bg-brand-800 text-white">
        <div class="mx-auto flex max-w-3xl items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('bookings.create') }}" class="text-lg font-semibold">{{ __('public.school') }}</a>

            <nav aria-label="{{ __('public.language') }}" class="flex gap-1 text-sm">
                @foreach (['eu' => 'Euskara', 'es' => 'Castellano', 'en' => 'English'] as $locale => $languageName)
                    <a href="{{ route(Route::currentRouteName(), [...request()->route()->parameters(), 'locale' => $locale]) }}"
                       lang="{{ $locale }}" hreflang="{{ $locale }}" title="{{ $languageName }}"
                       @if (app()->isLocale($locale)) aria-current="true" @endif
                       @class([
                           'rounded px-2 py-1 font-medium uppercase',
                           'bg-white text-brand-800' => app()->isLocale($locale),
                           'hover:bg-white/10' => ! app()->isLocale($locale),
                       ])>{{ $locale }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="mx-auto max-w-3xl px-4 pb-8 text-center text-xs text-slate-500">
        &copy; {{ now()->year }} {{ __('public.school') }}
    </footer>
</body>
</html>
