<x-layouts.app title="Bisitak">
    <div class="mb-4 inline-flex rounded-md bg-white p-1 text-sm ring-1 ring-slate-200">
        @foreach (['zain' => 'Onartzeko zain', 'onartuak' => 'Onartuak (hurrengoak)', 'guztiak' => 'Guztiak'] as $key => $label)
            <a href="{{ route('visits.index', ['egoera' => $key]) }}"
               @class(['flex items-center gap-2 rounded px-3 py-1.5', 'bg-brand-600 text-white' => $filter === $key, 'text-slate-600 hover:bg-slate-50' => $filter !== $key])>
                {{ $label }}
                @if ($key === 'zain' && $pendingCount > 0)
                    <span @class(['rounded-full px-1.5 text-xs font-semibold', 'bg-white text-brand-700' => $filter === $key, 'bg-amber-100 text-amber-800' => $filter !== $key])>{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <x-card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Jatorria</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">Mota</th>
                    <th class="px-4 py-3 text-right">Pertsonak</th>
                    <th class="px-4 py-3 text-left">Egoera</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($visits as $visit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $visit->visit_date->format('Y/m/d') }}
                            <span class="text-slate-500">{{ $visit->visit_date->isoFormat('ddd') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('visits.show', $visit) }}" class="font-medium text-brand-700 hover:underline">{{ $visit->origin ?? '—' }}</a>
                            @if ($visit->host)
                                <p class="text-slate-500">Nori: {{ $visit->host->name }}</p>
                            @endif
                        </td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $visit->typeNames('eu') ?: '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            {{ $visit->is_group ? $visit->group_size : $visit->visitors_count }}
                            @if ($visit->is_group)
                                <span class="text-slate-500">(taldea)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :color="$visit->status->color()">
                                {{ $visit->status->label() }}
                            </x-badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            {{ $filter === 'zain' ? 'Ez dago onartzeko zain dagoen eskaerarik.' : 'Ez dago bisitarik.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $visits->links() }}</div>
</x-layouts.app>
