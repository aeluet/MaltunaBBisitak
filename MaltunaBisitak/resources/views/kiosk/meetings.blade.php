<x-layouts.kiosk :title="__('kiosk.meetings.heading')">
    <div class="shrink-0">
        <a href="{{ route('kiosk.home') }}" class="text-lg text-brand-700 hover:underline">&larr; {{ __('kiosk.register.back') }}</a>
        <h1 class="mt-2 mb-5 text-4xl font-bold text-brand-800">{{ __('kiosk.meetings.heading') }}</h1>
    </div>

    {{-- Many meetings scroll inside this box; the screen itself never scrolls. --}}
    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">
        <div class="grid gap-4 md:grid-cols-2">
            @forelse ($meetings as $meeting)
                <a href="{{ route('kiosk.meetings.show', $meeting) }}"
                   class="block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 hover:ring-2 hover:ring-brand-600">
                    <p class="text-2xl font-semibold text-brand-800">{{ $meeting->title }}</p>
                    <p class="mt-1 text-lg text-slate-600">{{ collect([$meeting->starts_at, $meeting->location])->filter()->join(' · ') }}</p>
                </a>
            @empty
                <p class="rounded-2xl bg-white p-8 text-center text-xl text-slate-500 shadow-sm ring-1 ring-slate-200 md:col-span-2">{{ __('kiosk.meetings.empty') }}</p>
            @endforelse
        </div>
    </div>
</x-layouts.kiosk>
