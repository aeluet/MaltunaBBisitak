@if (session('status'))
    <div class="mb-6 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200" role="status">
        {{ session('status') }}
    </div>
@endif
