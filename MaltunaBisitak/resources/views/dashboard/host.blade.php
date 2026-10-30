<x-layouts.app title="Zure bisitak">
    <p class="mb-6 text-sm text-slate-500">Zu bisitatzera datozen hurrengo bisitak.</p>

    <div class="space-y-4">
        @forelse ($visits as $visit)
            <x-card class="p-5">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <p class="font-semibold">
                            {{ $visit->visit_date->format('Y/m/d') }}
                            <span class="font-normal text-slate-500">{{ $visit->visit_date->isoFormat('dddd') }}</span>
                        </p>
                        <p class="text-sm text-slate-500">{{ $visit->origin ?? '—' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if ($visit->visit_date->isToday())
                            <x-badge color="green">Gaur</x-badge>
                        @endif
                        @if ($visit->is_group)
                            <x-badge color="blue">Taldea · {{ $visit->group_size }} pertsona</x-badge>
                        @endif
                        <x-badge :color="$visit->status === \App\Enums\VisitStatus::Pending ? 'amber' : 'slate'">{{ $visit->status->label() }}</x-badge>
                    </div>
                </div>

                <ul class="mt-3 space-y-1 text-sm">
                    @foreach ($visit->visitors as $visitor)
                        <li class="flex items-center gap-2">
                            {{ $visitor->full_name }}
                            @if ($visitor->attendance->checked_in_at && ! $visitor->attendance->checked_out_at)
                                <x-badge color="green">Iritsi da · {{ $visitor->attendance->checked_in_at->format('H:i') }}</x-badge>
                            @elseif ($visitor->attendance->checked_out_at)
                                <x-badge>Joan da</x-badge>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @empty
            <x-card class="p-6 text-sm text-slate-500">Ez duzu bisitarik programatuta.</x-card>
        @endforelse
    </div>
</x-layouts.app>
