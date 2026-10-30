<x-layouts.kiosk :title="$visit->origin">
    @include('kiosk._register', [
        'heading' => $visit->origin,
        'subheading' => trans_choice('kiosk.groups.people', $visit->group_size, ['count' => $visit->group_size]),
        'action' => route('kiosk.groups.store', $visit),
        'origin' => $visit->origin,
        'phoneUrl' => route('registrations.visits.create', $visit),
    ])
</x-layouts.kiosk>
