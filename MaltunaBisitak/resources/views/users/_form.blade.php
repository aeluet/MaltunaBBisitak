<div class="grid gap-5 sm:grid-cols-2">
    <x-form.input name="name" label="Izena" :value="$user->name" required />
    <x-form.input name="email" label="Posta elektronikoa" type="email" :value="$user->email" required />
    <x-form.select name="role" label="Rola" :options="$roles" :selected="$user->role?->value" required />
    <div class="flex items-end pb-2">
        <x-form.checkbox name="is_active" label="Kontua aktibo" :checked="$user->is_active" />
    </div>
    <x-form.input name="password" label="Pasahitza" type="password" autocomplete="new-password"
                  :required="! $user->exists"
                  :hint="$user->exists ? 'Hutsik utzi ez aldatzeko. Gutxienez 10 karaktere.' : 'Gutxienez 10 karaktere.'" />
    <x-form.input name="password_confirmation" label="Pasahitza berretsi" type="password" autocomplete="new-password" />
</div>
