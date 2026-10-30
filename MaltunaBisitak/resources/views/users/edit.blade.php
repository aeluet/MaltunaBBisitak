<x-layouts.app :title="$user->name">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('users._form')

            <div class="flex gap-3">
                <x-button>Gorde</x-button>
                <x-button :href="route('users.index')" variant="secondary">Utzi</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
