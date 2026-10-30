<x-layouts.guest title="Saioa hasi">
    <h2 class="mb-6 text-lg font-semibold text-slate-900">Saioa hasi</h2>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Posta elektronikoa</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Pasahitza</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-100 focus:outline-none">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600">
            Gogoratu nazazu
        </label>

        <button type="submit"
                class="w-full rounded-md bg-brand-600 px-4 py-2.5 font-medium text-white hover:bg-brand-700 focus:ring-2 focus:ring-brand-100 focus:outline-none">
            Sartu
        </button>
    </form>
</x-layouts.guest>
