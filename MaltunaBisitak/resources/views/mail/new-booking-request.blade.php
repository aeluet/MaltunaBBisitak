<x-mail::message>
# Bisita eskaera berria

**{{ $visit->origin }}** erakundeak bisita bat eskatu du web orritik.

<x-mail::panel>
**Data:** {{ $visit->visit_date->isoFormat('dddd, LL') }}<br>
**Mota:** {{ $visit->typeNames('eu') ?: '—' }}<br>
**Modua:** {{ $visit->is_group ? "Taldea ({$visit->group_size} pertsona)" : 'Bakarka' }}<br>
**Bisitariak:** {{ $visit->visitors->map(fn ($visitor) => $visitor->email ? "{$visitor->full_name} ({$visitor->email})" : $visitor->full_name)->join(', ') }}<br>
**Hizkuntza:** {{ strtoupper($visit->locale) }}
@if ($visit->notes)
<br>**Oharrak:** {{ $visit->notes }}
@endif
</x-mail::panel>

<x-mail::button :url="route('dashboard')">
Ireki kudeaketa gunea
</x-mail::button>
</x-mail::message>
