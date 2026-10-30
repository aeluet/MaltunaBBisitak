<x-layouts.app :title="$visitor->full_name">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('visitors.update', $visitor) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('visitors._form')

            <div class="flex gap-3">
                <x-button>Gorde</x-button>
                <x-button :href="route('visitors.show', $visitor)" variant="secondary">Utzi</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
