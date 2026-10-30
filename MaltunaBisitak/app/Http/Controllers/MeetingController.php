<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingRequest;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(Request $request): View
    {
        $showPast = $request->boolean('iraganekoak');

        $meetings = Meeting::query()
            ->withCount('visitors')
            ->when(
                $showPast,
                fn (Builder $query) => $query->where('date', '<', today())->orderByDesc('date')->orderByDesc('starts_at'),
                fn (Builder $query) => $query->where('date', '>=', today())->orderBy('date')->orderBy('starts_at'),
            )
            ->paginate(25)
            ->withQueryString();

        return view('meetings.index', [
            'meetings' => $meetings,
            'showPast' => $showPast,
        ]);
    }

    public function create(): View
    {
        return view('meetings.create', [
            'meeting' => new Meeting(['date' => today()]),
        ]);
    }

    public function store(MeetingRequest $request): RedirectResponse
    {
        $meeting = Meeting::create($request->validated());

        return redirect()->route('meetings.show', $meeting)->with('status', 'Bilera sortuta.');
    }

    public function show(Meeting $meeting): View
    {
        $meeting->load(['visitors' => fn (BelongsToMany $query) => $query->orderByPivot('created_at')]);

        return view('meetings.show', ['meeting' => $meeting]);
    }

    public function edit(Meeting $meeting): View
    {
        return view('meetings.edit', ['meeting' => $meeting]);
    }

    public function update(MeetingRequest $request, Meeting $meeting): RedirectResponse
    {
        $meeting->update($request->validated());

        return redirect()->route('meetings.show', $meeting)->with('status', 'Bilera eguneratuta.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        if ($meeting->visitors()->withTrashed()->exists()) {
            return back()->with('error', 'Ezin da bilera ezabatu: bisitariek izena eman dute jada.');
        }

        $meeting->delete();

        return redirect()->route('meetings.index')->with('status', 'Bilera ezabatuta.');
    }
}
