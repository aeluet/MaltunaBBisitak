<x-layouts.app title="Txostenak">
    <x-card class="mb-6 p-5">
        <form method="GET" action="{{ route('reports.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
            <div class="lg:col-span-2">
                <x-form.select name="mota" label="Datuak" :options="$types" :selected="$type" />
            </div>
            <x-form.input name="noiztik" label="Noiztik" type="date" :value="$from->format('Y-m-d')" />
            <x-form.input name="noiz_arte" label="Noiz arte" type="date" :value="$until->format('Y-m-d')" />
            <x-form.input name="jatorria" :label="$type === 'kanpo-langileak' ? 'Enpresa' : 'Jatorria'" :value="$filters['jatorria'] ?? null" />

            @if (in_array($type, ['bisitariak', 'bisitak']))
                <x-form.select name="ostalaria" label="Nori" :options="$hosts" :selected="$filters['ostalaria'] ?? null" placeholder="Guztiak" />
            @endif

            <div class="flex flex-wrap gap-3 lg:col-span-6">
                <x-button>Iragazi</x-button>
                {{-- Submits the same filters to the export, also when they were changed without filtering first. --}}
                <x-button variant="secondary" formaction="{{ route('reports.export') }}">Excel-era esportatu</x-button>
            </div>
        </form>
    </x-card>

    <p class="mb-3 text-sm text-slate-500">
        {{ $records->total() }} errenkada · {{ $from->format('Y/m/d') }} – {{ $until->format('Y/m/d') }}
    </p>

    <x-card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-3 py-3 text-left whitespace-nowrap">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $cells)
                    <tr class="hover:bg-slate-50">
                        @foreach ($cells as $cell)
                            <td class="px-3 py-2 whitespace-nowrap">{{ $cell ?? '—' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-4 py-8 text-center text-slate-500">Ez dago daturik iragazki hauekin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $records->links() }}</div>
</x-layouts.app>
