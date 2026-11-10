<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Visit;
use App\Models\VisitAttendance;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Group visits at the kiosk: members who were not named in the request sign up and get their label (PW008).
 * A group that arrives without a booking can open its own visit here, as in the original kiosk.
 */
class KioskGroupController extends Controller
{
    /**
     * Approved group visits for today, the ones the kiosk offers.
     *
     * @return Builder<Visit>
     */
    public static function todaysGroups(): Builder
    {
        return Visit::where('status', VisitStatus::Approved)
            ->where('is_group', true)
            ->whereDate('visit_date', today());
    }

    /**
     * The "Bisita" button of the start screen: today's groups and a form to open a new one.
     */
    public function index(): View
    {
        return view('kiosk.groups', [
            'groups' => self::todaysGroups()->withCount('attendances')->orderBy('origin')->get(),
        ]);
    }

    /**
     * Open a group visit for today from the kiosk. A group with the same name is reused, so tapping twice
     * or a second member typing the same school does not split the group in two.
     */
    public function open(Request $request): RedirectResponse
    {
        $origin = trim($request->validate(['origin' => ['required', 'string', 'max:255']])['origin']);

        $visit = self::todaysGroups()->whereRaw('lower(origin) = ?', [mb_strtolower($origin)])->first()
            ?? Visit::create([
                'visit_date' => today(),
                'origin' => $origin,
                'is_group' => true,
                'status' => VisitStatus::Approved,
                'locale' => app()->getLocale(),
                'notes' => 'Pedestalean sortua.',
            ]);

        return redirect()->route('kiosk.groups.show', $visit);
    }

    public function show(Visit $visit): View
    {
        abort_unless($this->isHereToday($visit), 404);

        return view('kiosk.group', [
            'visit' => $visit,
            'attendances' => $visit->attendances()->with('visitor')->oldest()->orderBy('id')->get(),
        ]);
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

    /**
     * Print the labels of everyone registered in the group, for example members who signed up from their
     * phone. Printing a label means the person is here, so whoever was not inside yet is checked in.
     */
    public function printAll(Visit $visit): RedirectResponse
    {
        abort_unless($this->isHereToday($visit), 404);

        $attendances = $visit->attendances()->with('visitor')->whereNull('checked_out_at')->oldest()->orderBy('id')->get();

        if ($attendances->isEmpty()) {
            return redirect()->route('kiosk.groups.show', $visit)->with('kiosk_error', __('kiosk.groups.nothing_to_print'));
        }

        $attendances->each(function (VisitAttendance $attendance): void {
            if (! $attendance->checked_in_at) {
                $attendance->checkIn();
            }

            $attendance->update(['label_printed_at' => now()]);
        });

        return redirect()->route('kiosk.done')->with('kiosk_done', [
            'message' => trans_choice('kiosk.groups.printed', $attendances->count(), ['count' => $attendances->count()]),
            'labelUrls' => $attendances
                ->map(fn (VisitAttendance $attendance): string => KioskLabelController::urlFor($attendance->visitor, $visit->origin))
                ->all(),
        ]);
    }

    private function isHereToday(Visit $visit): bool
    {
        return $visit->status === VisitStatus::Approved && $visit->visit_date->isToday();
    }
}
