<x-mail::message>
# {{ __('public.mail.received.greeting', ['name' => $contactName]) }}

{{ __('public.mail.received.intro') }}

<x-mail::panel>
**{{ __('public.fields.date') }}:** {{ $visit->visit_date->isoFormat('dddd, LL') }}<br>
**{{ __('public.fields.types') }}:** {{ $visit->typeNames(app()->getLocale()) }}<br>
**{{ __('public.fields.origin') }}:** {{ $visit->origin }}<br>
@if ($visit->is_group)
**{{ __('public.fields.group_size') }}:** {{ $visit->group_size }}<br>
@endif
**{{ __('public.fields.visitors') }}:** {{ $visit->visitors->map->full_name->join(', ') }}
</x-mail::panel>

{{ __('public.mail.received.next_steps') }}

{{ __('public.mail.regards') }}<br>
{{ __('public.mail.signature') }}
</x-mail::message>
