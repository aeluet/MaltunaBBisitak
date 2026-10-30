<x-layouts.app title="Erabiltzaile berria">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf
            @include('users._form')

            <div class="flex gap-3">
                <x-button>Gorde</x-button>
                <x-button :href="route('users.index')" variant="secondary">Utzi</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
