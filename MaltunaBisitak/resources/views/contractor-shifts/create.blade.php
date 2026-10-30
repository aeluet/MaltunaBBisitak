<x-layouts.app title="Kanpo langilearen sarrera">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('contractor-shifts.store') }}" class="space-y-6">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="first_name" label="Izena" :value="$contractor->first_name" required />
                <x-form.input name="last_name" label="Abizena(k)" :value="$contractor->last_name" required />
                <x-form.input name="phone" label="Telefonoa" type="tel" :value="$contractor->phone" required />
                <x-form.input name="company" label="Enpresa (nondik)" :value="$contractor->company" required />
                <x-form.input name="checked_in_at" label="Sarrera eguna eta ordua" type="datetime-local" :value="$now" :max="$now" required />
                <div class="flex items-end pb-2">
                    <x-form.checkbox name="needs_key" label="Giltza eman zaio" />
                </div>
            </div>

            <div data-signature-pad>
                <div class="mb-1 flex items-center justify-between">
                    <span class="text-sm font-medium">Sinadura <span class="text-red-600">*</span></span>
                    <button type="button" data-signature-clear class="text-sm text-brand-700 hover:underline">Garbitu</button>
                </div>
                <canvas class="h-44 w-full touch-none rounded-md border-2 border-dashed border-slate-300 bg-white"
                        aria-label="Sinadura laukia"></canvas>
                <input type="hidden" name="signature">
                <p data-signature-error hidden class="mt-1 text-sm text-red-600">Langileak sinatu egin behar du.</p>
                @error('signature')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Langileak laukian sinatu behar du, hatzarekin edo saguarekin.</p>
            </div>

            <div class="flex gap-3">
                <x-button>Sarrera erregistratu</x-button>
                <x-button :href="route('contractor-shifts.index')" variant="secondary">Utzi</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
