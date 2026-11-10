{{-- Who signed in to the group, with their status; a long list scrolls inside the card. --}}
<section class="flex min-h-0 flex-1 flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
    <div class="flex shrink-0 flex-wrap items-center justify-between gap-3">
        <h2 class="text-2xl font-bold text-brand-800">
            {{ __('kiosk.groups.attendees_heading') }}
            <span class="font-normal text-slate-400">({{ $attendances->count() }})</span>
        </h2>
        <x-button :href="route('kiosk.groups.show', $visit)" variant="secondary">{{ __('kiosk.groups.refresh') }}</x-button>
    </div>

    @if ($attendances->isEmpty())
        <p class="mt-4 text-lg text-slate-500">{{ __('kiosk.groups.attendees_empty') }}</p>
    @else
        <ul class="mt-3 min-h-0 flex-1 divide-y divide-slate-100 overflow-y-auto overscroll-contain">
            @foreach ($attendances as $attendance)
                <li class="flex items-center justify-between gap-3 py-2.5 text-lg">
                    <span class="min-w-0 truncate"><span class="font-medium">{{ $attendance->visitor->first_name }}</span> {{ $attendance->visitor->last_name }}</span>
                    @if ($attendance->checked_out_at)
                        <x-badge class="shrink-0">{{ __('kiosk.groups.status_left') }}</x-badge>
                    @elseif ($attendance->checked_in_at)
                        <x-badge color="green" class="shrink-0">{{ __('kiosk.groups.status_inside') }}</x-badge>
                    @else
                        <x-badge color="amber" class="shrink-0">{{ __('kiosk.groups.status_waiting') }}</x-badge>
                    @endif
                </li>
            @endforeach
        </ul>

        @if ($attendances->whereNull('checked_out_at')->isNotEmpty())
            <form method="POST" action="{{ route('kiosk.groups.print', $visit) }}" class="mt-3 shrink-0 border-t border-slate-100 pt-3">
                @csrf
                <x-button size="lg" class="w-full">{{ __('kiosk.groups.print_all') }}</x-button>
                @if ($attendances->whereNull('checked_in_at')->isNotEmpty())
                    <p class="mt-2 text-sm text-slate-500">{{ __('kiosk.groups.print_all_help') }}</p>
                @endif
            </form>
        @endif
    @endif
</section>
