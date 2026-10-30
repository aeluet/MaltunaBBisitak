<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorRequest;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $visitors = Visitor::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    foreach (['first_name', 'last_name', 'email', 'document_number', 'origin'] as $column) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                });
            })
            ->withCount('visits')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        return view('visitors.index', [
            'visitors' => $visitors,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('visitors.create', [
            'visitor' => new Visitor(['country' => 'ES']),
            'countries' => Visitor::countryOptions(),
        ]);
    }

    public function store(VisitorRequest $request): RedirectResponse
    {
        $visitor = Visitor::create($request->validated());

        return redirect()->route('visitors.show', $visitor)->with('status', 'Bisitaria sortuta.');
    }

    public function show(Visitor $visitor): View
    {
        $visitor->load([
            'visits' => fn ($query) => $query->with(['host', 'types'])->orderByDesc('visit_date'),
            'meetings' => fn ($query) => $query->orderByDesc('date'),
        ]);

        return view('visitors.show', ['visitor' => $visitor]);
    }

    public function edit(Visitor $visitor): View
    {
        return view('visitors.edit', [
            'visitor' => $visitor,
            'countries' => Visitor::countryOptions(),
        ]);
    }

    public function update(VisitorRequest $request, Visitor $visitor): RedirectResponse
    {
        $visitor->update($request->validated());

        return redirect()->route('visitors.show', $visitor)->with('status', 'Bisitaria eguneratuta.');
    }

    public function destroy(Visitor $visitor): RedirectResponse
    {
        $visitor->delete();

        return redirect()->route('visitors.index')->with('status', 'Bisitaria ezabatuta.');
    }
}
