@use('App\Enums\VisitStatus')

<x-layouts.app :title="($visit->origin ?? 'Bisita').' · '.$visit->visit_date->format('Y/m/d')">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card class="p-6">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <x-badge :color="$visit->status->color()">
                        {{ $visit->status->label() }}
                    </x-badge>
                    @if ($visit->is_group)
                        <x-badge color="blue">Taldea · {{ $visit->group_size }} pertsona</x-badge>
                    @endif
                    <x-badge>{{ strtoupper($visit->locale) }}</x-badge>
                </div>

                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Data</dt>
                        <dd class="font-medium">{{ $visit->visit_date->format('Y/m/d') }} ({{ $visit->visit_date->isoFormat('dddd') }})</dd>
                        <dd @class(['mt-1 text-xs', 'text-red-600' => ! $availableDate || $availableDate->isFull($sameDayCount), 'text-slate-500' => $availableDate && ! $availableDate->isFull($sameDayCount)])>
                            @if (! $availableDate)
                                Egun hau ez dago egutegian erabilgarri.
                            @else
                                Egun horretan {{ $sameDayCount }} bisita{{ $availableDate->max_visits ? " (muga: {$availableDate->max_visits})" : '' }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Jatorria</dt>
                        <dd class="font-medium">{{ $visit->origin ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Mota</dt>
                        <dd class="font-medium">{{ $visit->typeNames('eu') ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Nori</dt>
                        <dd class="font-medium">{{ $visit->host?->name ?? '—' }}</dd>
                    </div>
                    @if ($visit->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500">Oharrak</dt>
                            <dd class="whitespace-pre-line">{{ $visit->notes }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-slate-500">Eskatua</dt>
                        <dd>{{ $visit->created_at->format('Y/m/d H:i') }}</dd>
                    </div>
                    @if ($visit->reviewed_at)
                        <div>
                            <dt class="text-slate-500">Erabakia</dt>
                            <dd>{{ $visit->reviewed_at->format('Y/m/d H:i') }} · {{ $visit->reviewer?->name ?? '—' }}</dd>
                        </div>
                    @endif
                    @if ($visit->response_message)
                        <div class="sm:col-span-2">
                            <dt class="text-slate-500">Bisitariei bidalitako mezua</dt>
                            <dd class="whitespace-pre-line">{{ $visit->response_message }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            <x-card>
                <h2 class="border-b border-slate-200 px-6 py-4 font-semibold">
                    Bisitariak <span class="font-normal text-slate-400">({{ $visit->visitors->count() }})</span>
                </h2>

                <ul class="divide-y divide-slate-100">
                    @foreach ($visit->visitors as $visitor)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-6 py-3 text-sm">
                            <div>
                                <a href="{{ route('visitors.show', $visitor) }}" class="font-medium text-brand-700 hover:underline">{{ $visitor->full_name }}</a>
                                <p class="text-slate-500">{{ collect([$visitor->email, $visitor->country_name])->filter()->join(' · ') ?: '—' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($visitor->attendance->checked_out_at)
                                    <x-badge>Irten da · {{ $visitor->attendance->checked_out_at->format('H:i') }}</x-badge>
                                @elseif ($visitor->attendance->checked_in_at)
                                    <x-badge color="green">Barruan · {{ $visitor->attendance->checked_in_at->format('H:i') }}</x-badge>
                                @endif
                                <span class="font-mono text-xs tracking-wider text-slate-500" title="Sarrera kodea (QR)">{{ $visitor->attendance->code }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        </div>

        <div class="space-y-6">
            @if ($visit->status === VisitStatus::Pending)
                <x-card class="p-6">
                    <h2 class="mb-4 font-semibold">Onartu</h2>
                    <form method="POST" action="{{ route('visits.approve', $visit) }}" class="space-y-4">
                        @csrf
                        <x-form.select name="host_id" label="Nork hartuko ditu" :options="$hosts" :selected="$visit->host_id" placeholder="—" />
                        <x-form.textarea name="response_message" id="approve-message" label="Mezua bisitariei (aukerakoa)" />
                        <p class="text-xs text-slate-500">Emaila duen bisitari bakoitzak bere QR kodea jasoko du, eskaeraren hizkuntzan.</p>
                        <x-button class="w-full">Onartu eta emaila bidali</x-button>
                    </form>
                </x-card>

                <x-card class="p-6">
                    <h2 class="mb-4 font-semibold">Ukatu</h2>
                    <form method="POST" action="{{ route('visits.reject', $visit) }}" class="space-y-4"
                          onsubmit="return confirm('Ziur zaude eskaera hau ukatu nahi duzula? Bisitariei emaila bidaliko zaie.')">
                        @csrf
                        <x-form.textarea name="response_message" id="reject-message" label="Arrazoia (aukerakoa)" />
                        <x-button variant="danger" class="w-full">Ukatu eta emaila bidali</x-button>
                    </form>
                </x-card>
            @elseif ($visit->status === VisitStatus::Approved)
                <x-card class="p-6">
                    <h2 class="mb-4 font-semibold">Bertan behera utzi</h2>
                    <form method="POST" action="{{ route('visits.cancel', $visit) }}" class="space-y-4"
                          onsubmit="return confirm('Ziur zaude bisita hau bertan behera utzi nahi duzula? Bisitariei emaila bidaliko zaie.')">
                        @csrf
                        <x-form.textarea name="response_message" id="cancel-message" label="Arrazoia (aukerakoa)" />
                        <x-button variant="danger" class="w-full">Bertan behera utzi</x-button>
                    </form>
                </x-card>
            @endif
        </div>
    </div>
</x-layouts.app>
