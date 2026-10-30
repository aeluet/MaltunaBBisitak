@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="mb-6 text-center">
            <p class="text-sm font-medium tracking-wide text-brand-700 uppercase">Miguel Altuna</p>
            <h1 class="text-2xl font-semibold text-slate-900">Bisiten kudeaketa</h1>
        </div>

        <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
