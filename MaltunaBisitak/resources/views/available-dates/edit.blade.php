<x-layouts.app :title="$availableDate->date->format('Y/m/d').', '.$availableDate->date->isoFormat('dddd')">
    <x-card class="max-w-xl p-6">
        <p class="mb-6 text-sm text-slate-600">
            Egun honetarako bisita eskaerak (zain edo onartuta): <strong>{{ $visitCount }}</strong>
        </p>

        <form method="POST" action="{{ route('available-dates.update', $availableDate) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-form.input name="max_visits" label="Bisita kopuru maximoa" type="number" min="1" max="100"
                          :value="$availableDate->max_visits" hint="Hutsik utzi mugarik ez jartzeko." />
            <x-form.input name="notes" label="Oharrak" :value="$availableDate->notes" hint="Adib.: goizez bakarrik." />

            <div class="flex gap-3">
                <x-button>Gorde</x-button>
                <x-button :href="route('available-dates.index', ['hilabetea' => $availableDate->date->format('Y-m')])" variant="secondary">Utzi</x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('available-dates.destroy', $availableDate) }}" class="mt-8 border-t border-slate-200 pt-6"
              onsubmit="return confirm('Ziur zaude egun hau egutegitik kendu nahi duzula?{{ $visitCount ? ' Dauden '.$visitCount.' eskaerak ez dira ezabatuko.' : '' }}')">
            @csrf
            @method('DELETE')
            <x-button variant="danger">Egun hau egutegitik kendu</x-button>
        </form>
    </x-card>
</x-layouts.app>
