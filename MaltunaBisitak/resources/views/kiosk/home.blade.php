{{-- Reloading every 10 minutes keeps the CSRF token of the always-open start screen fresh. --}}
<x-layouts.kiosk :idle-seconds="600">
    <section class="shrink-0 rounded-2xl bg-white px-8 py-6 text-center shadow-sm ring-1 ring-slate-200">
        <h1 class="text-4xl font-bold text-brand-800">{{ __('kiosk.title') }}</h1>
        <p class="mt-2 text-xl text-slate-600">{{ __('kiosk.scan.help') }}</p>

        <form method="POST" action="{{ route('kiosk.check-in') }}" class="mx-auto mt-5 flex max-w-xl gap-3">
            @csrf
            <input type="text" name="code" data-kiosk-scanner autocomplete="off" autocapitalize="characters" spellcheck="false"
                   aria-label="{{ __('kiosk.scan.placeholder') }}" placeholder="{{ __('kiosk.scan.placeholder') }}"
                   class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center font-mono text-2xl tracking-widest uppercase placeholder:font-sans placeholder:text-lg placeholder:tracking-normal placeholder:normal-case focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
            <x-button size="xl">{{ __('kiosk.scan.submit') }}</x-button>
        </form>
    </section>

    <nav class="mt-6 grid min-h-0 flex-1 grid-cols-2 gap-6 portrait:grid-cols-1">
        @foreach ([
            ['route' => 'kiosk.meetings.index', 'icon' => 'meeting', 'label' => __('kiosk.home.meetings'), 'help' => __('kiosk.home.meetings_help'), 'count' => $meetingsCount],
            ['route' => 'kiosk.groups.index', 'icon' => 'visit', 'label' => __('kiosk.home.visits'), 'help' => __('kiosk.home.visits_help'), 'count' => $groupsCount],
        ] as $tile)
            <a href="{{ route($tile['route']) }}"
               class="bg-brand-gradient group flex min-h-0 flex-col items-center justify-center gap-3 overflow-hidden rounded-2xl p-6 text-center text-white shadow-md transition hover:shadow-xl hover:brightness-110 active:scale-[0.99]">
                {{-- Line icons in the brand style; the icon shrinks first when the screen is short. --}}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true" class="size-32 min-h-0 shrink text-white/95 transition group-hover:scale-105 portrait:size-48">
                    @if ($tile['icon'] === 'meeting')
                        <rect x="3" y="4.5" width="18" height="16" rx="2.5" /><path d="M3 9.5h18M8 2.5v4M16 2.5v4" />
                        <circle cx="9" cy="14" r="1.6" /><circle cx="15" cy="14" r="1.6" />
                        <path d="M6.5 18.5c.4-1.4 1.4-2.2 2.5-2.2s2.1.8 2.5 2.2M12.5 18.5c.4-1.4 1.4-2.2 2.5-2.2s2.1.8 2.5 2.2" />
                    @else
                        <rect x="4" y="3" width="16" height="18" rx="2.5" /><path d="M9.5 3v2.5h5V3" />
                        <circle cx="12" cy="11" r="2.6" /><path d="M7.5 17.5c.7-2 2.4-3.1 4.5-3.1s3.8 1.1 4.5 3.1" />
                    @endif
                </svg>
                <span class="shrink-0 text-5xl font-bold portrait:text-6xl">{{ $tile['label'] }}</span>
                <span class="shrink-0 text-xl text-white/85 portrait:text-2xl">{{ $tile['help'] }}</span>
                <span class="shrink-0 rounded-full bg-white/15 px-4 py-1 text-base font-medium">{{ trans_choice('kiosk.home.today', $tile['count'], ['count' => $tile['count']]) }}</span>
            </a>
        @endforeach
    </nav>
</x-layouts.kiosk>
