<x-layouts.kiosk :title="$meeting->title">
    @include('kiosk._register', [
        'heading' => $meeting->title,
        'subheading' => collect([$meeting->starts_at, $meeting->location])->filter()->join(' · '),
        'action' => route('kiosk.meetings.store', $meeting),
        'origin' => null,
        'phoneUrl' => route('registrations.meetings.create', $meeting),
    ])
</x-layouts.kiosk>
