<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\Meeting;
use App\Models\Visit;
use App\Models\VisitAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The kiosk at the entrance: a USB QR reader types the visitor's code into the start screen (PW001, PW002).
 */
class KioskController extends Controller
{
    public function home(): View
    {
        return view('kiosk.home', [
            'meetings' => Meeting::whereDate('date', today())->orderBy('starts_at')->get(),
            'groups' => Visit::where('status', VisitStatus::Approved)
                ->where('is_group', true)
                ->whereDate('visit_date', today())
                ->orderBy('origin')
                ->get(),
        ]);
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $code = strtoupper(preg_replace('/[\s-]+/', '', (string) $request->input('code')));
        $attendance = $code === '' ? null : VisitAttendance::with(['visit', 'visitor'])->firstWhere('code', $code);

        $error = match (true) {
            $attendance === null => __('kiosk.errors.unknown_code'),
            $attendance->checked_out_at !== null, $attendance->visit->status === VisitStatus::Closed => __('kiosk.errors.finished'),
            $attendance->visit->status !== VisitStatus::Approved => __('kiosk.errors.not_approved'),
            ! $attendance->visit->visit_date->isToday() => __('kiosk.errors.not_today', [
                'date' => $attendance->visit->visit_date->isoFormat('dddd, LL'),
            ]),
            default => null,
        };

        if ($error) {
            return redirect()->route('kiosk.home')->with('kiosk_error', $error);
        }

        // Scanning again only reprints the label, e.g. when the printer jammed.
        if (! $attendance->checked_in_at) {
            $attendance->checkIn();
        }

        $attendance->update(['label_printed_at' => now()]);

        return redirect()->route('kiosk.done')->with('kiosk_done', [
            'message' => __('kiosk.done.welcome', ['name' => $attendance->visitor->first_name]),
            'labelUrl' => KioskLabelController::urlFor($attendance->visitor, $attendance->visit->origin),
        ]);
    }

    public function done(): View|RedirectResponse
    {
        if (! session()->has('kiosk_done')) {
            return redirect()->route('kiosk.home');
        }

        return view('kiosk.done', session('kiosk_done'));
    }
}
