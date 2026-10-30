<x-form.input name="first_name" :label="__('kiosk.fields.first_name')" required autocomplete="given-name" />
<x-form.input name="last_name" :label="__('kiosk.fields.last_name')" required autocomplete="family-name" />
<x-form.input name="origin" :label="__('kiosk.fields.origin')" :value="$origin" required autocomplete="organization" />
<x-form.input name="email" :label="__('kiosk.fields.email')" type="email" required autocomplete="email" />

{{-- Honeypot: people never see this field, simple bots fill it in. --}}
<div class="hidden" aria-hidden="true">
    <label for="website">Website</label>
    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
</div>
