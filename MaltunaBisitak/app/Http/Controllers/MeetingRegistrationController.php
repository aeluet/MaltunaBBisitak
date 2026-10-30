<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Meeting;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Signing up for a meeting from the phone, through the QR shown at the kiosk or by the staff (PW006, BP001–BP004).
 */
class MeetingRegistrationController extends Controller
{
    public function create(Meeting $meeting): View
    {
        return view('registrations.meeting', [
            'meeting' => $meeting,
            'isOpen' => $this->isOpen($meeting),
        ]);
    }

    public function store(RegisterAttendeeRequest $request, Meeting $meeting): RedirectResponse
    {
        abort_unless($this->isOpen($meeting), 404);

        $visitor = Visitor::matchOrCreate($request->validated());
        $meeting->visitors()->syncWithoutDetaching([$visitor->id]);

        return redirect()->route('registrations.meetings.done', $meeting)->with('registration', [
            'name' => $visitor->first_name,
        ]);
    }

    public function done(Meeting $meeting): View|RedirectResponse
    {
        if (! session()->has('registration')) {
            return redirect()->route('registrations.meetings.create', $meeting);
        }

        return view('registrations.meeting-done', ['meeting' => $meeting, ...session('registration')]);
    }

    private function isOpen(Meeting $meeting): bool
    {
        return ! $meeting->date->isBefore(today());
    }
}
