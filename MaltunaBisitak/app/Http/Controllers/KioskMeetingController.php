<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Meeting;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Signing up for one of today's meetings at the kiosk (PW005–PW007).
 */
class KioskMeetingController extends Controller
{
    public function show(Meeting $meeting): View
    {
        abort_unless($meeting->date->isToday(), 404);

        return view('kiosk.meeting', ['meeting' => $meeting]);
    }

    public function store(RegisterAttendeeRequest $request, Meeting $meeting): RedirectResponse
    {
        abort_unless($meeting->date->isToday(), 404);

        $visitor = Visitor::matchOrCreate($request->validated());
        $meeting->visitors()->syncWithoutDetaching([$visitor->id]);

        return redirect()->route('kiosk.done')->with('kiosk_done', [
            'message' => __('kiosk.done.meeting', ['name' => $visitor->first_name, 'meeting' => $meeting->title]),
            'labelUrl' => KioskLabelController::urlFor($visitor, $request->validated('origin')),
        ]);
    }
}
