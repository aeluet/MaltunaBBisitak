<x-mail::message>
# {{ __('public.mail.received.greeting', ['name' => $visitorName]) }}

{{ __('public.mail.approved.intro') }}

<x-mail::panel>
**{{ __('public.fields.date') }}:** {{ $visit->visit_date->isoFormat('dddd, LL') }}<br>
**{{ __('public.fields.types') }}:** {{ $visit->typeNames(app()->getLocale()) }}<br>
@if ($visit->is_group)
**{{ __('public.fields.group_size') }}:** {{ $visit->group_size }}<br>
@endif
@if ($visit->host)
**{{ __('public.mail.approved.host') }}:** {{ $visit->host->name }}
@endif
</x-mail::panel>

{{ __('public.mail.approved.qr_intro') }}

<p style="text-align: center;"><img src="{{ $message->embedData($qrCodePng, 'qr.png', 'image/png') }}" alt="QR" width="200" height="200"></p>
<p style="text-align: center; font-family: monospace; font-size: 18px; letter-spacing: 3px;">{{ $code }}</p>

@if ($visit->response_message)
**{{ __('public.mail.school_message') }}:** {{ $visit->response_message }}
@endif

{{ __('public.mail.regards') }}<br>
{{ __('public.mail.signature') }}
</x-mail::message>
