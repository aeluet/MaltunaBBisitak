<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Group members open this page from the visit's QR and get their own check-in QR (PW008, BP001–BP004).
 */
class VisitRegistrationController extends Controller
{
    public function create(Visit $visit): View
    {
        return view('registrations.visit', [
            'visit' => $visit,
            'isOpen' => $this->isOpen($visit),
        ]);
    }

    public function store(RegisterAttendeeRequest $request, Visit $visit): RedirectResponse
    {
        abort_unless($this->isOpen($visit), 404);

        $visitor = Visitor::matchOrCreate($request->validated());
        $visit->visitors()->syncWithoutDetaching([$visitor->id]);

        return redirect()->route('registrations.visits.done', $visit)->with('registration', [
            'name' => $visitor->first_name,
            'code' => $visit->attendances()->whereBelongsTo($visitor)->value('code'),
        ]);
    }

    public function done(Visit $visit): View|RedirectResponse
    {
        if (! session()->has('registration')) {
            return redirect()->route('registrations.visits.create', $visit);
        }

        return view('registrations.visit-done', ['visit' => $visit, ...session('registration')]);
    }

    private function isOpen(Visit $visit): bool
    {
        return $visit->status === VisitStatus::Approved && ! $visit->visit_date->isBefore(today());
    }
}
