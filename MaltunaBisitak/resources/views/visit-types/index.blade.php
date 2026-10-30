<x-layouts.app title="Bisita motak">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">Web orri publikoko eskaera-formularioan agertzen diren aukerak.</p>
        <x-button :href="route('visit-types.create')">+ Bisita mota berria</x-button>
    </div>

    <x-card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Euskaraz</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">Gaztelaniaz</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">Ingelesez</th>
                    <th class="px-4 py-3 text-left">Egoera</th>
                    <th class="px-4 py-3 text-right">Bisitak</th>
                    <th class="px-4 py-3 text-left"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($visitTypes as $visitType)
                    <tr @class(['text-slate-400' => ! $visitType->is_active])>
                        <td class="px-4 py-3">{{ $visitType->sort_order }}</td>
                        <td class="px-4 py-3 font-medium">{{ $visitType->name_eu }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $visitType->name_es }}</td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $visitType->name_en }}</td>
                        <td class="px-4 py-3">
                            @if ($visitType->is_active)
                                <x-badge color="green">Aktibo</x-badge>
                            @else
                                <x-badge>Ezkutatuta</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">{{ $visitType->visits_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('visit-types.edit', $visitType) }}" class="text-brand-700 hover:underline">Editatu</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-layouts.app>
