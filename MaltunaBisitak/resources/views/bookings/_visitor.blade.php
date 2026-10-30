<div class="rounded-lg p-4 ring-1 ring-slate-200" data-visitor data-index="{{ $index }}">
    <div class="mb-3 flex items-center justify-between gap-2">
        <p class="font-medium">{{ $isContact ? __('public.visitors.contact') : __('public.visitors.other') }}</p>

        @unless ($isContact)
            <button type="button" data-remove-visitor class="text-sm text-red-600 hover:underline">{{ __('public.visitors.remove') }}</button>
        @endunless
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-form.input :name="'visitors['.$index.'][first_name]'" :label="__('public.fields.first_name')"
                      :value="$visitor['first_name'] ?? null" required autocomplete="given-name" />
        <x-form.input :name="'visitors['.$index.'][last_name]'" :label="__('public.fields.last_name')"
                      :value="$visitor['last_name'] ?? null" required autocomplete="family-name" />
        <x-form.input :name="'visitors['.$index.'][email]'" :label="__('public.fields.email')" type="email"
                      :value="$visitor['email'] ?? null" :required="$isContact" autocomplete="email"
                      :hint="$isContact ? __('public.visitors.contact_hint') : null" />
        <x-form.select :name="'visitors['.$index.'][country]'" :label="__('public.fields.country')"
                       :options="$countries" :selected="$visitor['country'] ?? 'ES'" required />
    </div>
</div>
