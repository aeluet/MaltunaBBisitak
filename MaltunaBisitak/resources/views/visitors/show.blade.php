<x-layouts.app :title="$visitor->full_name">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="p-6 lg:col-span-1">
            <dl class="space-y-3 text-sm">
                @foreach ([
                    'NAN' => $visitor->document_number,
                    'Posta elektronikoa' => $visitor->email,
                    'Telefonoa' => $visitor->phone,
                    'Jatorria' => $visitor->origin,
                    'Herrialdea' => $visitor->country_name,
                ] as $label => $value)
                    <div>
                        <dt class="text-slate-500">{{ $label }}</dt>
                        <dd class="font-medium">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach

                @if ($visitor->notes)
                    <div>
                        <dt class="text-slate-500">Oharrak</dt>
                        <dd class="whitespace-pre-line">{{ $visitor->notes }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 flex flex-wrap gap-3">
                <x-button :href="route('visitors.edit', $visitor)">Editatu</x-button>

                @if (auth()->user()->hasRole(\App\Enums\Role::Admin))
                    <form method="POST" action="{{ route('visitors.destroy', $visitor) }}"
                          onsubmit="return confirm('Ziur zaude bisitari hau ezabatu nahi duzula? Bisiten historia mantendu egingo da.')">
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger">Ezabatu</x-button>
                    </form>
                @endif
            </div>
        </x-card>

        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="border-b border-slate-200 px-6 py-4 font-semibold">Bisiten historia</h2>

                <ul class="divide-y divide-slate-100">
                    @forelse ($visitor->visits as $visit)
                        <li class="flex flex-wrap items-center justify-between gap-2 px-6 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $visit->visit_date->format('Y/m/d') }}</p>
                                <p class="text-slate-500">
                                    {{ $visit->typeNames('eu') ?: str($visit->notes)->limit(60)->toString() ?: 'Mota zehaztu gabe' }}
                                    @if ($visit->host)
                                        · {{ $visit->host->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right text-slate-500">
                                @if ($visit->attendance->checked_in_at)
                                    {{ $visit->attendance->checked_in_at->format('H:i') }}
                                    – {{ $visit->attendance->checked_out_at?->format('H:i') ?? 'barruan' }}
                                @else
                                    <x-badge :color="$visit->status === \App\Enums\VisitStatus::Pending ? 'amber' : 'slate'">{{ $visit->status->label() }}</x-badge>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-sm text-slate-500">Ez dago bisitarik erregistratuta.</li>
                    @endforelse
                </ul>
            </x-card>

            @if ($visitor->meetings->isNotEmpty())
                <x-card>
                    <h2 class="border-b border-slate-200 px-6 py-4 font-semibold">Bilerak</h2>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($visitor->meetings as $meeting)
                            <li class="px-6 py-3 text-sm">
                                <span class="font-medium">{{ $meeting->date->format('Y/m/d') }}</span> · {{ $meeting->title }}
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif
        </div>
    </div>
</x-layouts.app>
