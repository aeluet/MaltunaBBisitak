{{-- Reloading every 10 minutes keeps the CSRF token of the always-open start screen fresh. --}}
<x-layouts.kiosk :idle-seconds="600">
    @if (session('kiosk_error'))
        <div class="mb-8 rounded-2xl bg-red-50 px-6 py-5 text-xl text-red-800 ring-1 ring-red-200" role="alert">
            {{ session('kiosk_error') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
        <h1 class="text-4xl font-semibold text-slate-900">{{ __('kiosk.title') }}</h1>
        <p class="mt-4 text-xl text-slate-600">{{ __('kiosk.scan.help') }}</p>

        <form method="POST" action="{{ route('kiosk.check-in') }}" class="mx-auto mt-8 flex max-w-lg gap-3">
            @csrf
            <input type="text" name="code" data-kiosk-scanner autocomplete="off" autocapitalize="characters" spellcheck="false"
                   aria-label="{{ __('kiosk.scan.placeholder') }}" placeholder="{{ __('kiosk.scan.placeholder') }}"
                   class="w-full rounded-xl border border-slate-300 px-4 py-4 text-center font-mono text-2xl tracking-widest uppercase placeholder:font-sans placeholder:text-lg placeholder:tracking-normal placeholder:normal-case focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
            <x-button size="xl">{{ __('kiosk.scan.submit') }}</x-button>
        </form>
    </section>

    @if ($meetings->isNotEmpty())
        <section class="mt-10">
            <h2 class="mb-4 text-2xl font-semibold">{{ __('kiosk.meetings.heading') }}</h2>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($meetings as $meeting)
                    <a href="{{ route('kiosk.meetings.show', $meeting) }}"
                       class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:ring-2 hover:ring-brand-600">
                        <p class="text-2xl font-semibold">{{ $meeting->title }}</p>
                        <p class="mt-1 text-lg text-slate-600">{{ collect([$meeting->starts_at, $meeting->location])->filter()->join(' · ') }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($groups->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-2xl font-semibold">{{ __('kiosk.groups.heading') }}</h2>
            <p class="mb-4 text-lg text-slate-600">{{ __('kiosk.groups.help') }}</p>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($groups as $group)
                    <a href="{{ route('kiosk.groups.show', $group) }}"
                       class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:ring-2 hover:ring-brand-600">
                        <p class="text-2xl font-semibold">{{ $group->origin }}</p>
                        <p class="mt-1 text-lg text-slate-600">{{ trans_choice('kiosk.groups.people', $group->group_size, ['count' => $group->group_size]) }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.kiosk>
