<x-layouts.app :title="$visitType->name_eu">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('visit-types.update', $visitType) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('visit-types._form')

            <div class="flex gap-3">
                <x-button>Gorde</x-button>
                <x-button :href="route('visit-types.index')" variant="secondary">Utzi</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
