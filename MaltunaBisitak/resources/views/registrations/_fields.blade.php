<x-form.input name="first_name" :label="__('kiosk.fields.first_name')" required autocomplete="given-name" />
<x-form.input name="last_name" :label="__('kiosk.fields.last_name')" required autocomplete="family-name" />
<x-form.input name="origin" :label="__('kiosk.fields.origin')" :value="$origin" required autocomplete="organization" />
<x-form.input name="email" :label="__('kiosk.fields.email')" type="email" required autocomplete="email" />
<x-form.select name="country" :label="__('kiosk.fields.country')" :options="\App\Models\Visitor::countryOptions()"
               :placeholder="__('kiosk.fields.country_placeholder')" autocomplete="country" />

<p class="col-span-full rounded-lg bg-slate-50 p-4 text-sm text-slate-600 ring-1 ring-slate-200">
    {{ __('kiosk.privacy.notice') }}
    @if (config('bisitak.privacy_url'))
        <a href="{{ config('bisitak.privacy_url') }}" target="_blank" rel="noopener" class="font-medium text-brand-600 underline">{{ __('kiosk.privacy.link') }}</a>
    @endif
</p>

{{-- Honeypot: people never see this field, simple bots fill it in. --}}
<div class="hidden" aria-hidden="true">
    <label for="website">Website</label>
    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
</div>
