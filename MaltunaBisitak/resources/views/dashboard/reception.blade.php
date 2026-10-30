<x-layouts.app title="Harrera">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ now()->format('Y/m/d') }}, {{ now()->isoFormat('dddd') }}</p>
        <x-button :href="route('walk-ins.create')">+ Bisitari baten sarrera</x-button>
    </div>

    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-card class="p-5">
            <p class="text-sm text-slate-500">Pertsonak ikastetxean</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $peopleInsideCount }}</p>
        </x-card>

        <x-card class="p-5">
            <p class="text-sm text-slate-500">Sarrerak gaur</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $checkInCounts['today'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Astean {{ $checkInCounts['week'] }} · Hilean {{ $checkInCounts['month'] }}</p>
        </x-card>

        <a href="{{ route('visits.index', ['egoera' => 'zain']) }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 hover:ring-brand-600">
            <p class="text-sm text-slate-500">Onartzeko zain</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $pendingVisitsCount }}</p>
        </a>

        <a href="{{ route('contractor-shifts.index') }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200 hover:ring-brand-600">
            <p class="text-sm text-slate-500">Kanpo langileak lanean</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $activeContractorsCount }}</p>
        </a>
    </div>

    <section class="mb-8">
        <h2 class="mb-3 text-lg font-semibold">
            Ikastetxean orain <span class="font-normal text-slate-400">({{ $activeAttendances->count() }})</span>
        </h2>

        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Bisitaria</th>
                        <th class="hidden px-4 py-3 text-left md:table-cell">Nori</th>
                        <th class="px-4 py-3 text-left">Sarrera</th>
                        <th class="hidden px-4 py-3 text-left sm:table-cell">Txartela</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($activeAttendances as $attendance)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('visitors.show', $attendance->visitor) }}" class="font-medium text-brand-700 hover:underline">
                                    {{ $attendance->visitor->full_name }}
                                </a>
                                <p class="text-slate-500">{{ $attendance->visit->origin ?? $attendance->visitor->origin }}</p>
                                @if ($attendance->visit->is_group)
                                    <x-badge color="blue" class="mt-1">Taldea · {{ $attendance->visit->group_size }} pertsona</x-badge>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell">{{ $attendance->visit->host?->name ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $attendance->checked_in_at->format('H:i') }}</td>
                            <td class="hidden px-4 py-3 sm:table-cell">{{ $attendance->badge_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <form method="POST" action="{{ route('attendances.check-out', $attendance) }}">
                                        @csrf
                                        <x-button variant="secondary">Irteera</x-button>
                                    </form>

                                    @if ($activeAttendances->where('visit_id', $attendance->visit_id)->count() > 1)
                                        <form method="POST" action="{{ route('visits.close', $attendance->visit) }}"
                                              onsubmit="return confirm('Talde osoaren irteera erregistratu eta bisita itxi?')">
                                            @csrf
                                            <x-button variant="secondary">Taldea irten</x-button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Ez dago bisitaririk ikastetxean une honetan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </section>

    <section>
        <h2 class="mb-3 text-lg font-semibold">
            Gaur espero dira <span class="font-normal text-slate-400">({{ $expectedAttendances->count() }})</span>
        </h2>

        @error('badge_number')
            <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <x-card>
            <ul class="divide-y divide-slate-100">
                @forelse ($expectedAttendances as $attendance)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ $attendance->visitor->full_name }}</p>
                            <p class="text-slate-500">
                                {{ collect([$attendance->visit->origin, $attendance->visit->host ? 'Nori: '.$attendance->visit->host->name : null])->filter()->join(' · ') }}
                            </p>
                            @if ($attendance->visit->is_group)
                                <x-badge color="blue" class="mt-1">Taldea · {{ $attendance->visit->group_size }} pertsona</x-badge>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('attendances.check-in', $attendance) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="badge_number" placeholder="Txartela" aria-label="{{ $attendance->visitor->full_name }}: txartel zenbakia"
                                   class="w-24 rounded-md border border-slate-300 px-2 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
                            <x-button>Sarrera</x-button>
                        </form>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-slate-500">Gaur ez da beste bisitaririk espero.</li>
                @endforelse
            </ul>
        </x-card>
    </section>
</x-layouts.app>
