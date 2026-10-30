<x-layouts.app title="Bisitariak">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('visitors.index') }}" class="flex w-full gap-2 sm:max-w-md" role="search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Bilatu izena, NAN, emaila, jatorria…"
                   aria-label="Bilatu bisitariak"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
            <x-button variant="secondary">Bilatu</x-button>
        </form>

        <x-button :href="route('visitors.create')">+ Bisitari berria</x-button>
    </div>

    <x-card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Izena</th>
                    <th class="px-4 py-3 text-left">NAN</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">Jatorria</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">Posta elektronikoa</th>
                    <th class="px-4 py-3 text-right">Bisitak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($visitors as $visitor)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('visitors.show', $visitor) }}" class="font-medium text-brand-700 hover:underline">
                                {{ $visitor->last_name }}, {{ $visitor->first_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $visitor->document_number ?? '—' }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $visitor->origin ?? '—' }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $visitor->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">{{ $visitor->visits_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            @if ($search !== '')
                                Ez da bisitaririk aurkitu "{{ $search }}" bilaketarekin.
                            @else
                                Oraindik ez dago bisitaririk.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $visitors->links() }}</div>
</x-layouts.app>
