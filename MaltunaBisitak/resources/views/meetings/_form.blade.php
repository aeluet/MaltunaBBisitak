<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-form.input name="title" label="Izenburua" :value="$meeting->title" required />
    </div>
    <div class="sm:col-span-2">
        <x-form.input name="location" label="Lekua" :value="$meeting->location" required />
    </div>
    <x-form.input name="date" label="Data" type="date" :value="$meeting->date?->format('Y-m-d')" required />
    <x-form.input name="starts_at" label="Hasiera ordua" type="time" :value="$meeting->starts_at" />
</div>
