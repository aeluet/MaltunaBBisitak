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
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-3xl items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('bookings.create') }}" class="shrink-0">
                <img src="{{ asset('images/miguel-altuna-logoa.svg') }}" alt="{{ __('public.school') }}" class="h-12 w-auto">
            </a>

            <nav aria-label="{{ __('public.language') }}" class="flex gap-1 text-sm">
                @foreach (['eu' => 'Euskara', 'es' => 'Castellano', 'en' => 'English'] as $locale => $languageName)
                    <a href="{{ route(Route::currentRouteName(), [...request()->route()->parameters(), 'locale' => $locale]) }}"
                       lang="{{ $locale }}" hreflang="{{ $locale }}" title="{{ $languageName }}"
                       @if (app()->isLocale($locale)) aria-current="true" @endif
                       @class([
                           'rounded-lg px-2.5 py-1 font-medium uppercase',
                           'btn-brand' => app()->isLocale($locale),
                           'text-brand-800 hover:bg-brand-50' => ! app()->isLocale($locale),
                       ])>{{ $locale }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="bg-brand-gradient text-sm text-white/80">
        <div class="mx-auto flex max-w-3xl flex-wrap items-center justify-between gap-2 px-4 py-6">
            <span>&copy; {{ now()->year }} {{ __('public.school') }}</span>
            <a href="https://maltuna.eus" class="font-medium text-white hover:underline">maltuna.eus</a>
            <p class="w-full border-t border-white/15 pt-3 text-xs text-white/70">{{ __('public.credits') }}</p>
        </div>
    </footer>
</body>
</html>
