<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\VisitAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VisitAttendanceController extends Controller
{
    /**
     * Check in a visitor of an approved booking.
     */
    public function checkIn(Request $request, VisitAttendance $attendance): RedirectResponse
    {
        if ($attendance->checked_in_at || $attendance->visit->status !== VisitStatus::Approved) {
            return back()->with('error', "{$attendance->visitor->full_name}: ezin da sarrera erregistratu.");
        }

        $validated = $request->validate([
            'badge_number' => [
                'nullable', 'string', 'max:20',
                Rule::unique('visit_visitor')->whereNotNull('checked_in_at')->whereNull('checked_out_at'),
            ],
        ], [
            'badge_number.unique' => 'Txartel hori beste bisitari batek du une honetan.',
        ]);

        $attendance->checkIn($validated['badge_number'] ?? null);

        return back()->with('status', "{$attendance->visitor->full_name}: sarrera erregistratuta.");
    }

    public function checkOut(VisitAttendance $attendance): RedirectResponse
    {
        if (! $attendance->checked_in_at || $attendance->checked_out_at) {
            return back()->with('error', "{$attendance->visitor->full_name} ez dago ikastetxean.");
        }

        $attendance->checkOut();

        return back()->with('status', "{$attendance->visitor->full_name}: irteera erregistratuta.");
    }
}
