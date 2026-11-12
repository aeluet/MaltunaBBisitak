<x-mail::message>
# Kaixo, {{ $visit->host->name }}

Bisita bat onartu da eta zu izango zara arduraduna.

<x-mail::panel>
**Data:** {{ $visit->visit_date->locale('eu')->isoFormat('dddd, LL') }}<br>
**Nondik:** {{ $visit->origin ?: '—' }}<br>
**Mota:** {{ $visit->typeNames('eu') ?: '—' }}<br>
**Modua:** {{ $mode }}<br>
**Bisitariak:** {{ $visitorNames ?: '—' }}
@if ($visit->response_message)
<br>**Bisitariei bidalitako mezua:** {{ $visit->response_message }}
@endif
</x-mail::panel>

<x-mail::button :url="$calendarUrl">
Gehitu Google Calendar-era
</x-mail::button>

Ondo izan,<br>
{{ config('app.name') }}
</x-mail::message>
