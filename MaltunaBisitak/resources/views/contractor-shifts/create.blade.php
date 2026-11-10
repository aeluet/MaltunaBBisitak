<x-layouts.app title="Kanpo langilearen sarrera">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('contractor-shifts.store') }}" class="space-y-6">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="first_name" label="Izena" :value="$contractor->first_name" required />
                <x-form.input name="last_name" label="Abizena(k)" :value="$contractor->last_name" required />
                <x-form.input name="phone" label="Telefonoa" type="tel" :value="$contractor->phone" required
                              pattern="[0-9 .\(\)\-\+]{9,20}" title="Telefono zenbakia: 9-15 digitu, adibidez 611 222 333 edo +34 611 222 333" />
                <x-form.input name="company" label="Enpresa (nondik)" :value="$contractor->company" required />
                <x-form.input name="checked_in_at" label="Sarrera eguna eta ordua" type="datetime-local" :value="$now" :max="$now" required />
                <div class="flex items-end pb-2">
                    <x-form.checkbox name="needs_key" label="Giltza eman zaio" />
                </div>
            </div>

            <section aria-labelledby="documents-heading" class="rounded-lg bg-brand-50 p-4 ring-1 ring-brand-100 sm:p-5">
                <h2 id="documents-heading" class="font-semibold text-brand-800">Segurtasun dokumentuak</h2>
                <p class="mt-1 text-sm text-slate-600">Sinatu aurretik, langileak dokumentu hauek irakurri behar ditu.</p>

                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($documents as $document)
                        <li>
                            <a href="{{ asset($document['path']) }}" target="_blank" rel="noopener"
                               class="flex h-full items-start gap-3 rounded-lg bg-white p-3 ring-1 ring-slate-200 transition hover:ring-brand-500">
                                <span class="mt-0.5 shrink-0 rounded bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white">PDF</span>
                                <span>
                                    <span class="block text-sm font-semibold text-brand-800">{{ $document['title'] }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">{{ $document['description'] }}</span>
                                    <span class="mt-1 block text-xs font-medium text-brand-600">Ireki dokumentua ↗</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 border-t border-brand-100 pt-4">
                    <x-form.checkbox name="documents_accepted" id="documents-accepted"
                                     label="Langileak dokumentuak irakurri ditu eta haietan jasotako arauak betetzea onartzen du." />
                </div>
            </section>

            <div data-signature-pad data-signature-requires="documents-accepted">
                <div class="mb-1 flex items-center justify-between">
                    <span class="text-sm font-medium">Sinadura <span class="text-red-600">*</span></span>
                    <button type="button" data-signature-clear class="text-sm text-brand-700 hover:underline">Garbitu</button>
                </div>
                <canvas class="h-44 w-full touch-none rounded-md border-2 border-dashed border-slate-300 bg-white"
                        aria-label="Sinadura laukia"></canvas>
                <input type="hidden" name="signature">
                <p data-signature-locked hidden class="mt-1 text-sm font-medium text-amber-700">Sinatzeko, lehenik onartu dokumentuak irakurri direla.</p>
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
