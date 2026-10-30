<x-layouts.public :title="__('public.title')">
    <h1 class="text-3xl font-semibold text-slate-900">{{ __('public.title') }}</h1>
    <p class="mt-3 text-slate-600">{{ __('public.intro') }}</p>

    @if ($calendar->isEmpty())
        <x-card class="mt-8 p-6 text-slate-600">{{ __('public.calendar.no_dates') }}</x-card>
    @else
        <p class="mt-2 text-sm text-slate-500">{{ __('public.required_hint') }}</p>

        <form method="POST" action="{{ route('bookings.store') }}" class="mt-8 space-y-6" data-booking-form>
            @csrf

            <x-card class="p-6">
                <fieldset>
                    <legend class="mb-4 text-lg font-semibold">1. {{ __('public.sections.types') }} <span class="text-red-600">*</span></legend>

                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($visitTypes as $visitType)
                            <label class="flex cursor-pointer items-center gap-3 rounded-md px-3 py-2 ring-1 ring-slate-200 hover:bg-slate-50 has-checked:bg-brand-50 has-checked:ring-brand-600">
                                <input type="checkbox" name="types[]" value="{{ $visitType->id }}"
                                       @checked(in_array($visitType->id, old('types', [])))
                                       class="rounded border-slate-300 text-brand-600">
                                {{ $visitType->nameFor(app()->getLocale()) }}
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <x-form.input name="other_type" :label="__('public.fields.other_type')" />
                    </div>

                    @error('types')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </fieldset>
            </x-card>

            <x-card class="p-6">
                <fieldset>
                    <legend class="mb-4 text-lg font-semibold">2. {{ __('public.sections.date') }} <span class="text-red-600">*</span></legend>

                    @error('visit_date')
                        <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="grid gap-8 md:grid-cols-2">
                        @foreach ($calendar as ['month' => $month, 'days' => $days])
                            <div>
                                <h3 class="mb-2 text-center font-medium first-letter:uppercase">{{ $month->isoFormat(__('public.calendar.month_format')) }}</h3>

                                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                    @foreach ($weekdays as $weekday)
                                        <div class="pb-1 text-xs font-medium text-slate-500 uppercase" aria-hidden="true">{{ $weekday }}</div>
                                    @endforeach

                                    @foreach ($days as $day)
                                        @php($isOpen = $openDates->get($day->toDateString()))

                                        @if (! $day->isSameMonth($month))
                                            <div></div>
                                        @elseif ($isOpen === true)
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="visit_date" value="{{ $day->toDateString() }}" required
                                                       @checked(old('visit_date') === $day->toDateString()) class="peer sr-only">
                                                <span class="flex h-11 items-center justify-center rounded-md bg-brand-50 font-semibold text-brand-800 ring-1 ring-brand-100 hover:bg-brand-100 peer-checked:bg-brand-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-600"
                                                      aria-hidden="true">{{ $day->day }}</span>
                                                <span class="sr-only">{{ $day->isoFormat('dddd, LL') }}</span>
                                            </label>
                                        @elseif ($isOpen === false)
                                            <div class="flex h-11 items-center justify-center rounded-md text-slate-400 line-through" title="{{ __('public.calendar.full') }}">
                                                {{ $day->day }}
                                            </div>
                                        @else
                                            <div class="flex h-11 items-center justify-center text-slate-300" aria-hidden="true">{{ $day->day }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-500">
                        <span class="flex items-center gap-2"><span class="size-3 rounded bg-brand-50 ring-1 ring-brand-100"></span> {{ __('public.calendar.available') }}</span>
                        <span class="flex items-center gap-2"><span class="text-slate-400 line-through">12</span> {{ __('public.calendar.full') }}</span>
                    </div>
                </fieldset>
            </x-card>

            <x-card class="p-6">
                <h2 class="mb-4 text-lg font-semibold">3. {{ __('public.sections.group') }}</h2>

                <div class="space-y-5">
                    <x-form.input name="origin" :label="__('public.fields.origin')" required />

                    <fieldset>
                        <legend class="mb-2 text-sm font-medium">{{ __('public.fields.visit_kind') }} <span class="text-red-600">*</span></legend>
                        <div class="flex flex-wrap gap-3">
                            @foreach (['0' => __('public.fields.individual'), '1' => __('public.fields.group')] as $value => $label)
                                <label class="flex cursor-pointer items-center gap-2 rounded-md px-4 py-2 ring-1 ring-slate-200 has-checked:bg-brand-50 has-checked:ring-brand-600">
                                    <input type="radio" name="is_group" value="{{ $value }}" class="text-brand-600"
                                           @checked((string) old('is_group', '0') === (string) $value)>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div data-group-size>
                        <x-form.input name="group_size" :label="__('public.fields.group_size')" type="number" min="2" max="500" />
                    </div>
                </div>
            </x-card>

            <x-card class="p-6">
                <h2 class="mb-1 text-lg font-semibold">4. {{ __('public.sections.visitors') }}</h2>
                <p class="mb-4 text-sm text-slate-500">{{ __('public.visitors.group_hint') }}</p>

                @error('visitors')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="space-y-4" data-visitors data-max="{{ $maxVisitors }}">
                    @foreach (old('visitors', [[]]) as $index => $visitor)
                        @include('bookings._visitor', ['index' => $index, 'visitor' => $visitor, 'isContact' => $loop->first])
                    @endforeach
                </div>

                <template data-visitor-template>
                    @include('bookings._visitor', ['index' => '__INDEX__', 'visitor' => [], 'isContact' => false])
                </template>

                <button type="button" data-add-visitor hidden class="mt-4 text-sm font-medium text-brand-700 hover:underline">
                    {{ __('public.visitors.add') }}
                </button>
            </x-card>

            <x-card class="p-6">
                <h2 class="mb-4 text-lg font-semibold">5. {{ __('public.sections.notes') }}</h2>

                <x-form.textarea name="notes" :label="__('public.fields.notes')" />

                <label class="mt-6 flex items-start gap-3 text-sm">
                    <input type="checkbox" name="privacy" value="1" required @checked(old('privacy'))
                           class="mt-0.5 rounded border-slate-300 text-brand-600">
                    <span>{{ __('public.privacy') }} <span class="text-red-600">*</span></span>
                </label>
                @error('privacy')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Honeypot: people never see this field, simple bots fill it in. --}}
                <div class="hidden" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>
            </x-card>

            <x-button size="lg" class="w-full sm:w-auto">{{ __('public.submit') }}</x-button>
        </form>
    @endif
</x-layouts.public>
