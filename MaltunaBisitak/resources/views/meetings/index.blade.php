<x-layouts.app title="Bilerak">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="inline-flex self-start rounded-md bg-white p-1 text-sm ring-1 ring-slate-200">
            <a href="{{ route('meetings.index') }}"
               @class(['rounded px-3 py-1.5', 'bg-brand-600 text-white' => ! $showPast, 'text-slate-600 hover:bg-slate-50' => $showPast])>
                Hurrengoak
            </a>
            <a href="{{ route('meetings.index', ['iraganekoak' => 1]) }}"
               @class(['rounded px-3 py-1.5', 'bg-brand-600 text-white' => $showPast, 'text-slate-600 hover:bg-slate-50' => ! $showPast])>
                Iraganekoak
            </a>
        </div>

        <x-button :href="route('meetings.create')">+ Bilera berria</x-button>
    </div>

    <x-card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Izenburua</th>
                    <th class="hidden px-4 py-3 text-left md:table-cell">Lekua</th>
                    <th class="px-4 py-3 text-right">Izen-emateak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($meetings as $meeting)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $meeting->date->format('Y/m/d') }}
                            <span class="text-slate-500">{{ $meeting->starts_at }}</span>
                            @if ($meeting->date->isToday())
                                <x-badge color="green">Gaur</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('meetings.show', $meeting) }}" class="font-medium text-brand-700 hover:underline">{{ $meeting->title }}</a>
                        </td>
                        <td class="hidden px-4 py-3 md:table-cell">{{ $meeting->location }}</td>
                        <td class="px-4 py-3 text-right">{{ $meeting->visitors_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            {{ $showPast ? 'Ez dago iraganeko bilerarik.' : 'Ez dago bilerarik programatuta.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $meetings->links() }}</div>
</x-layouts.app>
