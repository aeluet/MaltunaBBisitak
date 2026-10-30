<x-layouts.app :title="$meeting->title">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-6">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Data</dt>
                    <dd class="font-medium">{{ $meeting->date->format('Y/m/d') }} ({{ $meeting->date->isoFormat('dddd') }})</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Hasiera ordua</dt>
                    <dd class="font-medium">{{ $meeting->starts_at ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Lekua</dt>
                    <dd class="font-medium">{{ $meeting->location }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex flex-wrap gap-3">
                <x-button :href="route('meetings.edit', $meeting)">Editatu</x-button>

                @if ($meeting->visitors->isEmpty())
                    <form method="POST" action="{{ route('meetings.destroy', $meeting) }}"
                          onsubmit="return confirm('Ziur zaude bilera hau ezabatu nahi duzula?')">
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger">Ezabatu</x-button>
                    </form>
                @endif
            </div>
        </x-card>

        <x-card class="lg:col-span-2">
            <h2 class="border-b border-slate-200 px-6 py-4 font-semibold">
                Izena emandakoak <span class="font-normal text-slate-400">({{ $meeting->visitors->count() }})</span>
            </h2>

            <ul class="divide-y divide-slate-100">
                @forelse ($meeting->visitors as $visitor)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-6 py-3 text-sm">
                        <div>
                            <a href="{{ route('visitors.show', $visitor) }}" class="font-medium text-brand-700 hover:underline">{{ $visitor->full_name }}</a>
                            <p class="text-slate-500">{{ collect([$visitor->origin, $visitor->email])->filter()->join(' · ') }}</p>
                        </div>
                        <span class="text-slate-500">{{ $visitor->pivot->created_at?->format('H:i') }}</span>
                    </li>
                @empty
                    <li class="px-6 py-6 text-sm text-slate-500">
                        Oraindik ez du inork izenik eman. Bisitariek pedestalean edo QR kodearen bidez eman ahal izango dute izena.
                    </li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
