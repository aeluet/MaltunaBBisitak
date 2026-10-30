<x-layouts.app title="Erabiltzaileak">
    <div class="mb-4 flex justify-end">
        <x-button :href="route('users.create')">+ Erabiltzaile berria</x-button>
    </div>

    <x-card class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Izena</th>
                    <th class="px-4 py-3 text-left">Posta elektronikoa</th>
                    <th class="px-4 py-3 text-left">Rola</th>
                    <th class="px-4 py-3 text-left">Egoera</th>
                    <th class="px-4 py-3 text-left"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr @class(['text-slate-400' => ! $user->is_active])>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3"><x-badge color="blue">{{ $user->role->label() }}</x-badge></td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <x-badge color="green">Aktibo</x-badge>
                            @else
                                <x-badge>Desaktibatuta</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.edit', $user) }}" class="text-brand-700 hover:underline">Editatu</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">{{ $users->links() }}</div>
</x-layouts.app>
