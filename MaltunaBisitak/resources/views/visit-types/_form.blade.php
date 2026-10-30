<div class="grid gap-5 sm:grid-cols-3">
    <x-form.input name="name_eu" label="Euskaraz" :value="$visitType->name_eu" required />
    <x-form.input name="name_es" label="Gaztelaniaz" :value="$visitType->name_es" required />
    <x-form.input name="name_en" label="Ingelesez" :value="$visitType->name_en" required />
    <x-form.input name="sort_order" label="Ordena" type="number" min="0" :value="$visitType->sort_order" required />
    <div class="flex items-end pb-2 sm:col-span-2">
        <x-form.checkbox name="is_active" label="Aktibo (formulario publikoan erakutsi)" :checked="$visitType->is_active" />
    </div>
</div>
