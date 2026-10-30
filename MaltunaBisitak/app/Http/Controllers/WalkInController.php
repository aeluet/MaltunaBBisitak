<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Http\Requests\StoreWalkInRequest;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitAttendance;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Reception registers someone who arrives without a booking: a visit for today, checked in at once.
 */
class WalkInController extends Controller
{
    public function create(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('walk-ins.create', [
            'search' => $search,
            'results' => $search === '' ? collect() : Visitor::search($search)->orderBy('last_name')->limit(10)->get(),
            'insideVisitorIds' => VisitAttendance::active()->pluck('visitor_id'),
            'visitor' => Visitor::find($request->query('bisitaria')),
            'isNewVisitor' => $request->boolean('berria'),
            'hosts' => User::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StoreWalkInRequest $request): RedirectResponse
    {
        $visitor = DB::transaction(function () use ($request): Visitor {
            $visitor = $request->filled('visitor_id')
                ? Visitor::findOrFail($request->validated('visitor_id'))
                : Visitor::create($request->safe()->only(StoreWalkInRequest::NEW_VISITOR_FIELDS));

            $visit = new Visit([
                'visit_date' => today(),
                'origin' => $visitor->origin,
                'status' => VisitStatus::Approved,
                'host_id' => $request->validated('host_id'),
                'notes' => $request->validated('notes'),
            ]);
            $visit->reviewer()->associate($request->user());
            $visit->reviewed_at = now();
            $visit->save();

            $visit->visitors()->attach($visitor, [
                'checked_in_at' => now(),
                'badge_number' => $request->validated('badge_number'),
            ]);

            return $visitor;
        });

        return redirect()->route('dashboard')->with('status', "{$visitor->full_name}: sarrera erregistratuta.");
    }
}
