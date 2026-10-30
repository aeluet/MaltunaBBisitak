<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Members of a group visit who were not named in the request sign up at the kiosk and get their label (PW008).
 */
class KioskGroupController extends Controller
{
    public function show(Visit $visit): View
    {
        abort_unless($this->isHereToday($visit), 404);

        return view('kiosk.group', ['visit' => $visit]);
    }

    public function store(RegisterAttendeeRequest $request, Visit $visit): RedirectResponse
    {
        abort_unless($this->isHereToday($visit), 404);

        $visitor = Visitor::matchOrCreate($request->validated());
        $visit->visitors()->syncWithoutDetaching([$visitor->id]);

        $attendance = $visit->attendances()->whereBelongsTo($visitor)->sole();

        if (! $attendance->checked_in_at) {
            $attendance->checkIn();
        }

        $attendance->update(['label_printed_at' => now()]);

        return redirect()->route('kiosk.done')->with('kiosk_done', [
            'message' => __('kiosk.done.welcome', ['name' => $visitor->first_name]),
            'labelUrl' => KioskLabelController::urlFor($visitor, $visit->origin),
        ]);
    }

    private function isHereToday(Visit $visit): bool
    {
        return $visit->status === VisitStatus::Approved && $visit->visit_date->isToday();
    }
}
