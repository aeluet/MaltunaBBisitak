@props(['title' => null])
@use('App\Enums\Role')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/miguel-altuna-logoa.svg') }}" alt="Miguel Altuna Lanbide Heziketa" class="h-11 w-auto">
                <span class="hidden border-l border-slate-200 pl-3 text-lg font-semibold text-brand-800 sm:inline">Bisitak</span>
            </a>

            <div class="flex items-center gap-4 text-sm">
                <span class="hidden text-brand-800 sm:inline">
                    {{ auth()->user()->name }}
                    <span class="ml-1 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 ring-1 ring-brand-100">{{ auth()->user()->role->label() }}</span>
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-1.5 font-medium text-brand-800 ring-1 ring-slate-200 hover:bg-brand-50">Saioa itxi</button>
                </form>
            </div>
        </div>

        @php
            $staff = [Role::Admin, Role::Harrera];
            $navigation = [
                ['route' => 'dashboard', 'pattern' => 'dashboard', 'label' => 'Panela', 'roles' => null],
                ['route' => 'visits.index', 'pattern' => 'visits.*', 'label' => 'Bisitak', 'roles' => $staff],
                ['route' => 'visitors.index', 'pattern' => 'visitors.*', 'label' => 'Bisitariak', 'roles' => $staff],
                ['route' => 'meetings.index', 'pattern' => 'meetings.*', 'label' => 'Bilerak', 'roles' => $staff],
                ['route' => 'contractor-shifts.index', 'pattern' => 'contractor-shifts.*', 'label' => 'Kanpo langileak', 'roles' => $staff],
                ['route' => 'reports.index', 'pattern' => 'reports.*', 'label' => 'Txostenak', 'roles' => [Role::Admin]],
                ['route' => 'available-dates.index', 'pattern' => 'available-dates.*', 'label' => 'Egutegia', 'roles' => [Role::Admin]],
                ['route' => 'visit-types.index', 'pattern' => 'visit-types.*', 'label' => 'Bisita motak', 'roles' => [Role::Admin]],
                ['route' => 'users.index', 'pattern' => 'users.*', 'label' => 'Erabiltzaileak', 'roles' => [Role::Admin]],
            ];
        @endphp

        <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-4 text-[15px]">
            @foreach ($navigation as $item)
                @continue($item['roles'] && ! auth()->user()->hasRole(...$item['roles']))

                <a href="{{ route($item['route']) }}"
                   @class([
                       'shrink-0 border-b-[3px] px-3 py-2.5 font-medium text-brand-800 transition-colors',
                       'border-brand-500' => request()->routeIs($item['pattern']),
                       'border-transparent hover:border-brand-200 hover:text-brand-600' => ! request()->routeIs($item['pattern']),
                   ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @if ($title)
            <h1 class="mb-6 text-3xl font-bold text-brand-800">{{ $title }}</h1>
        @endif

        <x-flash />

        {{ $slot }}
    </main>

    <footer class="bg-brand-gradient text-sm text-white/80">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-4">
            <span>&copy; {{ now()->year }} Miguel Altuna Lanbide Heziketa</span>
            <a href="https://maltuna.eus" class="font-medium text-white hover:underline">maltuna.eus</a>
        </div>
    </footer>
</body>
</html>
