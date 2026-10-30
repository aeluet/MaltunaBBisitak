<x-layouts.app title="Panela">
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Bisitariak ikastetxean</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $activeVisitorsCount }}</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Onartzeko zain dauden eskaerak</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $pendingVisitsCount }}</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Kanpoko langileak lanean</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $activeContractorsCount }}</p>
        </div>
    </div>
</x-layouts.app>
