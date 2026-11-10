<x-layouts.kiosk :title="$visit->origin">
    @include('kiosk._register', [
        'heading' => $visit->origin,
        'subheading' => $visit->group_size
            ? trans_choice('kiosk.groups.people', $visit->group_size, ['count' => $visit->group_size])
            : trans_choice('kiosk.groups.registered', $attendances->count(), ['count' => $attendances->count()]),
        'action' => route('kiosk.groups.store', $visit),
        'origin' => $visit->origin,
        'phoneUrl' => route('registrations.visits.create', $visit),
        'backUrl' => route('kiosk.groups.index'),
        'aside' => view('kiosk._attendees', ['visit' => $visit, 'attendances' => $attendances])->render(),
    ])
</x-layouts.kiosk>
