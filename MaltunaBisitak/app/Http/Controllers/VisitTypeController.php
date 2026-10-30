<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitTypeRequest;
use App\Models\VisitType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VisitTypeController extends Controller
{
    public function index(): View
    {
        return view('visit-types.index', [
            'visitTypes' => VisitType::withCount('visits')->orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('visit-types.create', [
            'visitType' => new VisitType([
                'sort_order' => (int) VisitType::max('sort_order') + 1,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(VisitTypeRequest $request): RedirectResponse
    {
        VisitType::create($request->validated());

        return redirect()->route('visit-types.index')->with('status', 'Bisita mota sortuta.');
    }

    public function edit(VisitType $visitType): View
    {
        return view('visit-types.edit', ['visitType' => $visitType]);
    }

    public function update(VisitTypeRequest $request, VisitType $visitType): RedirectResponse
    {
        $visitType->update($request->validated());

        return redirect()->route('visit-types.index')->with('status', 'Bisita mota eguneratuta.');
    }
}
