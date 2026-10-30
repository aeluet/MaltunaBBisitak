<x-layouts.app :title="$meeting->title">
    <x-card class="max-w-3xl p-6">
        <form method="POST" action="{{ route('meetings.update', $meeting) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('meetings._form')

            <div class="flex gap-3">
                <x-button>Gorde</x-button>
                <x-button :href="route('meetings.show', $meeting)" variant="secondary">Utzi</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
