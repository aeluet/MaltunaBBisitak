<x-layouts.kiosk :title="__('kiosk.home.visits')">
    <a href="{{ route('kiosk.home') }}" class="shrink-0 text-lg text-brand-700 hover:underline">&larr; {{ __('kiosk.register.back') }}</a>

    <div class="mt-2 grid min-h-0 flex-1 grid-cols-5 gap-8 portrait:grid-cols-1 portrait:grid-rows-[minmax(0,1fr)_auto]">
        <section class="col-span-3 flex min-h-0 flex-col portrait:col-span-1">
            <h1 class="shrink-0 text-4xl font-bold text-brand-800">{{ __('kiosk.groups.heading') }}</h1>
            <p class="mt-1 mb-5 shrink-0 text-xl text-slate-600">{{ __('kiosk.groups.help') }}</p>

            {{-- Many groups scroll inside this box; the screen itself never scrolls. --}}
            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-contain p-1">
                @forelse ($groups as $group)
                    <a href="{{ route('kiosk.groups.show', $group) }}"
                       class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:ring-2 hover:ring-brand-600">
                        <p class="text-2xl font-semibold text-brand-800">{{ $group->origin }}</p>
                        <p class="mt-1 text-lg text-slate-600">
                            {{ $group->group_size
                                ? trans_choice('kiosk.groups.people', $group->group_size, ['count' => $group->group_size])
                                : trans_choice('kiosk.groups.registered', $group->attendances_count, ['count' => $group->attendances_count]) }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-2xl bg-white p-8 text-center text-xl text-slate-500 shadow-sm ring-1 ring-slate-200">{{ __('kiosk.groups.empty') }}</p>
                @endforelse
            </div>
        </section>

        <section class="col-span-2 self-start rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 portrait:col-span-1">
            <h2 class="text-2xl font-bold text-brand-800">{{ __('kiosk.groups.new_heading') }}</h2>
            <p class="mt-1 mb-5 text-lg text-slate-600">{{ __('kiosk.groups.new_help') }}</p>

            <form method="POST" action="{{ route('kiosk.groups.open') }}" class="space-y-4">
                @csrf
                <x-form.input name="origin" :label="__('kiosk.groups.new_origin')" required autocomplete="off" maxlength="255" class="text-lg" />
                <x-button size="xl" class="w-full">{{ __('kiosk.groups.new_submit') }}</x-button>
            </form>
        </section>
    </div>
</x-layouts.kiosk>
