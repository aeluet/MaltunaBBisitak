<div class="grid gap-5 sm:grid-cols-2">
    <x-form.input name="first_name" label="Izena" :value="$visitor->first_name" required />
    <x-form.input name="last_name" label="Abizena(k)" :value="$visitor->last_name" required />
    <x-form.input name="document_number" label="NAN / AIZ / Pasaportea" :value="$visitor->document_number" />
    <x-form.input name="email" label="Posta elektronikoa" type="email" :value="$visitor->email" />
    <x-form.input name="phone" label="Telefonoa" type="tel" :value="$visitor->phone" />
    <x-form.input name="origin" label="Jatorria (enpresa, ikastetxea…)" :value="$visitor->origin" />
    <x-form.select name="country" label="Herrialdea" :options="$countries" :selected="$visitor->country" placeholder="—" />
    <div class="sm:col-span-2">
        <x-form.textarea name="notes" label="Oharrak" :value="$visitor->notes" />
    </div>
</div>
