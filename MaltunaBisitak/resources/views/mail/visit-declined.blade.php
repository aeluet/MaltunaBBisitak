<x-mail::message>
# {{ __('public.mail.received.greeting', ['name' => $visitorName]) }}

{{ $intro }}

@if ($visit->response_message)
<x-mail::panel>
**{{ __('public.mail.school_message') }}:** {{ $visit->response_message }}
</x-mail::panel>
@endif

<x-mail::button :url="route('bookings.create', ['locale' => $visit->locale])">
{{ __('public.mail.request_again') }}
</x-mail::button>

{{ __('public.mail.regards') }}<br>
{{ __('public.mail.signature') }}
</x-mail::message>
