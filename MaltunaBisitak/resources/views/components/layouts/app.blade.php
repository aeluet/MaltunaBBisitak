@props(['title' => null])
@use('App\Enums\Role')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "$title · " : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <header class="bg-brand-800 text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('dashboard') }}" class="font-semibold">Miguel Altuna · Bisitak</a>

            <div class="flex items-center gap-4 text-sm">
                <span class="hidden sm:inline">
                    {{ auth()->user()->name }}
                    <span class="ml-1 rounded bg-white/15 px-2 py-0.5 text-xs">{{ auth()->user()->role->label() }}</span>
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md px-3 py-1.5 hover:bg-white/10">Saioa itxi</button>
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

        <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-4 text-sm">
            @foreach ($navigation as $item)
                @continue($item['roles'] && ! auth()->user()->hasRole(...$item['roles']))

                <a href="{{ route($item['route']) }}"
                   @class([
                       'shrink-0 rounded-t-md px-3 py-2',
                       'bg-slate-100 font-medium text-brand-800' => request()->routeIs($item['pattern']),
                       'hover:bg-white/10' => ! request()->routeIs($item['pattern']),
                   ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6">
        @if ($title)
            <h1 class="mb-6 text-2xl font-semibold text-slate-900">{{ $title }}</h1>
        @endif

        <x-flash />

        {{ $slot }}
    </main>
</body>
</html>
