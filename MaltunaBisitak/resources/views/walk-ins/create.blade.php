<x-layouts.app title="Bisitari baten sarrera">
    @if (! $visitor && ! $isNewVisitor)
        <x-card class="max-w-3xl p-6">
            <h2 class="mb-4 font-semibold">1. Bilatu bisitaria</h2>

            <form method="GET" action="{{ route('walk-ins.create') }}" class="flex gap-2" role="search">
                <input type="search" name="q" value="{{ $search }}" autofocus aria-label="Bilatu bisitaria"
                       placeholder="Izena, abizena, NAN, emaila edo jatorria"
                       class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
                <x-button variant="secondary">Bilatu</x-button>
            </form>

            @if ($search !== '')
                <ul class="mt-4 divide-y divide-slate-100 rounded-md ring-1 ring-slate-200">
                    @forelse ($results as $result)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $result->full_name }}</p>
                                <p class="text-slate-500">{{ collect([$result->document_number, $result->origin])->filter()->join(' · ') ?: '—' }}</p>
                            </div>

                            @if ($insideVisitorIds->contains($result->id))
                                <x-badge color="green">Ikastetxean dago</x-badge>
                            @else
                                <x-button :href="route('walk-ins.create', ['bisitaria' => $result->id])" variant="secondary">Hautatu</x-button>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-slate-500">Ez da bisitaririk aurkitu "{{ $search }}" bilaketarekin.</li>
                    @endforelse
                </ul>
            @endif

            <p class="mt-6 text-sm">
                Lehen aldiz dator?
                <a href="{{ route('walk-ins.create', ['berria' => 1]) }}" class="font-medium text-brand-700 hover:underline">Erregistratu bisitari berria</a>
            </p>
        </x-card>
    @else
        <form method="POST" action="{{ route('walk-ins.store') }}" class="max-w-3xl space-y-6">
            @csrf

            <x-card class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">1. Bisitaria</h2>
                    <a href="{{ route('walk-ins.create') }}" class="text-sm text-brand-700 hover:underline">Aldatu</a>
                </div>

                @if ($visitor)
                    <input type="hidden" name="visitor_id" value="{{ $visitor->id }}">
                    <p class="font-medium">{{ $visitor->full_name }}</p>
                    <p class="text-sm text-slate-500">{{ collect([$visitor->document_number, $visitor->origin, $visitor->phone])->filter()->join(' · ') ?: '—' }}</p>
                    @error('visitor_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                @else
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-form.input name="first_name" label="Izena" required autofocus />
                        <x-form.input name="last_name" label="Abizena(k)" required />
                        <x-form.input name="document_number" label="NAN / AIZ / Pasaportea" />
                        <x-form.input name="origin" label="Jatorria (enpresa, ikastetxea…)" />
                        <x-form.input name="phone" label="Telefonoa" type="tel" />
                        <x-form.input name="email" label="Posta elektronikoa" type="email" />
                    </div>
                @endif
            </x-card>

            <x-card class="p-6">
                <h2 class="mb-4 font-semibold">2. Bisita</h2>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.select name="host_id" label="Nor bisitatzera dator" :options="$hosts" placeholder="—" />
                    <x-form.input name="badge_number" label="Txartel zenbakia" />
                    <div class="sm:col-span-2">
                        <x-form.textarea name="notes" label="Arrazoia / oharrak" />
                    </div>
                </div>
            </x-card>

            <div class="flex gap-3">
                <x-button>Sarrera erregistratu</x-button>
                <x-button :href="route('dashboard')" variant="secondary">Utzi</x-button>
            </div>
        </form>
    @endif
</x-layouts.app>
