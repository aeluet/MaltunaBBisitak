@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="mb-6 text-center">
            <img src="{{ asset('images/miguel-altuna-logoa.svg') }}" alt="Miguel Altuna Lanbide Heziketa" class="mx-auto mb-4 h-16 w-auto">
            <h1 class="text-2xl font-bold text-brand-800">Bisiten kudeaketa</h1>
        </div>

        <div class="w-full max-w-sm rounded-xl border-t-4 border-brand-500 bg-white p-6 shadow-md ring-1 ring-slate-200 sm:p-8">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
