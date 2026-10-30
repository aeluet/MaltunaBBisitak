<x-layouts.app title="Egutegia">
    <p class="mb-4 text-sm text-slate-500">
        Web orri publikoan bisitak eskatzeko erabilgarri dauden egunak. Egin klik egun libre batean gehitzeko,
        edo erabilgarri dagoen batean bisita kopuru maximoa aldatzeko edo kentzeko.
    </p>

    <x-card class="p-3 sm:p-5">
        <div class="mb-4 flex items-center justify-between gap-2">
            <a href="{{ route('available-dates.index', ['hilabetea' => $month->subMonth()->format('Y-m')]) }}"
               class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100" aria-label="Aurreko hilabetea">&larr;</a>

            <h2 class="text-lg font-semibold">{{ $month->isoFormat('YYYY[ko] MMMM') }}</h2>

            <a href="{{ route('available-dates.index', ['hilabetea' => $month->addMonth()->format('Y-m')]) }}"
               class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100" aria-label="Hurrengo hilabetea">&rarr;</a>
        </div>

        <div class="grid grid-cols-7 gap-1 text-center text-sm">
            @foreach (['Al', 'Ar', 'Az', 'Og', 'Or', 'Lr', 'Ig'] as $weekday)
                <div class="pb-2 text-xs font-medium text-slate-500 uppercase">{{ $weekday }}</div>
            @endforeach

            @foreach ($days as $day)
                @php
                    $availableDate = $availableDates->get($day->toDateString());
                    $visitCount = $visitCounts->get($day->toDateString(), 0);
                @endphp

                @if (! $day->isSameMonth($month))
                    <div></div>
                @elseif ($availableDate)
                    <a href="{{ route('available-dates.edit', $availableDate) }}"
                       title="{{ $availableDate->notes }}"
                       @class([
                           'flex h-14 flex-col items-center justify-center rounded-md bg-brand-600 text-white hover:bg-brand-700 sm:h-20',
                           'opacity-60' => $day->isBefore($today),
                           'ring-2 ring-amber-400 ring-offset-1' => $day->isSameDay($today),
                       ])>
                        <span class="font-semibold">{{ $day->day }}</span>
                        <span class="text-[10px] sm:text-xs">{{ $visitCount }}{{ $availableDate->max_visits ? '/'.$availableDate->max_visits : '' }}</span>
                    </a>
                @elseif ($day->isBefore($today))
                    <div class="flex h-14 items-center justify-center rounded-md text-slate-300 sm:h-20">{{ $day->day }}</div>
                @else
                    <form method="POST" action="{{ route('available-dates.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $day->toDateString() }}">
                        <button type="submit" aria-label="Erabilgarri jarri: {{ $day->format('Y/m/d') }}"
                                @class([
                                    'flex h-14 w-full items-center justify-center rounded-md hover:bg-brand-50 sm:h-20',
                                    'text-slate-400' => $day->isWeekend(),
                                    'ring-1 ring-slate-200 hover:ring-brand-600' => ! $day->isSameDay($today),
                                    'ring-2 ring-amber-400' => $day->isSameDay($today),
                                ])>
                            {{ $day->day }}
                        </button>
                    </form>
                @endif
            @endforeach
        </div>
    </x-card>

    <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-500">
        <span class="flex items-center gap-2"><span class="size-3 rounded bg-brand-600"></span> Erabilgarri (eskaerak/muga)</span>
        <span class="flex items-center gap-2"><span class="size-3 rounded ring-1 ring-slate-300"></span> Libre: klik egin gehitzeko</span>
        <span class="flex items-center gap-2"><span class="size-3 rounded ring-2 ring-amber-400"></span> Gaur</span>
    </div>
</x-layouts.app>
