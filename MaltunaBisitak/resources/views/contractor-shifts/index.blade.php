<x-layouts.app title="Kanpo langileak">
    <div class="mb-4 flex justify-end">
        <x-button :href="route('contractor-shifts.create')">+ Sarrera erregistratu</x-button>
    </div>

    <section class="mb-8">
        <h2 class="mb-3 text-lg font-semibold">
            Une honetan lanean <span class="font-normal text-slate-400">({{ $activeShifts->count() }})</span>
        </h2>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($activeShifts as $shift)
                <x-card class="flex flex-col p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold">{{ $shift->contractor->full_name }}</p>
                            <p class="text-sm text-slate-500">{{ $shift->contractor->company }}</p>
                            <a href="tel:{{ $shift->contractor->phone }}" class="text-sm text-brand-700 hover:underline">{{ $shift->contractor->phone }}</a>
                        </div>
                        @if ($shift->needs_key)
                            <x-badge color="amber">Giltza du</x-badge>
                        @endif
                    </div>

                    <p class="mt-3 text-sm text-slate-600">
                        Sarrera: <strong>{{ $shift->checked_in_at->isToday() ? $shift->checked_in_at->format('H:i') : $shift->checked_in_at->format('Y/m/d H:i') }}</strong>
                    </p>

                    <form method="POST" action="{{ route('contractor-shifts.check-out', $shift) }}" class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                        @csrf
                        @if ($shift->needs_key)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="key_returned" value="1" class="rounded border-slate-300 text-brand-600">
                                Giltza itzuli du
                            </label>
                        @endif
                        <x-button class="ml-auto">Irteera markatu</x-button>
                    </form>
                </x-card>
            @empty
                <x-card class="p-5 text-sm text-slate-500 sm:col-span-2 lg:col-span-3">
                    Ez dago kanpo langilerik lanean une honetan.
                </x-card>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-3 text-lg font-semibold">Historia</h2>

        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Data</th>
                        <th class="px-4 py-3 text-left">Langilea</th>
                        <th class="px-4 py-3 text-left">Ordutegia</th>
                        <th class="px-4 py-3 text-left">Giltza</th>
                        <th class="hidden px-4 py-3 text-left md:table-cell">Sinadura</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($finishedShifts as $shift)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $shift->checked_in_at->format('Y/m/d') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $shift->contractor->full_name }}</p>
                                <p class="text-slate-500">{{ $shift->contractor->company }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $shift->checked_in_at->format('H:i') }} – {{ $shift->checked_out_at->format('H:i') }}</td>
                            <td class="px-4 py-3">
                                @if (! $shift->needs_key)
                                    <span class="text-slate-400">—</span>
                                @elseif ($shift->key_returned)
                                    <x-badge color="green">Itzulita</x-badge>
                                @else
                                    <x-badge color="red">Itzuli gabe</x-badge>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                @if ($shift->signature_path)
                                    <img src="{{ route('contractor-shifts.signature', $shift) }}" alt="{{ $shift->contractor->full_name }}: sinadura"
                                         class="h-8 w-auto" loading="lazy">
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('contractor-shifts.create', ['langilea' => $shift->contractor_id]) }}" class="text-brand-700 hover:underline">Sarrera berria</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Oraindik ez dago amaitutako lanaldirik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        <div class="mt-4">{{ $finishedShifts->links() }}</div>
    </section>
</x-layouts.app>
