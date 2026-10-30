<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvailableDateRequest;
use App\Http\Requests\UpdateAvailableDateRequest;
use App\Models\AvailableDate;
use App\Models\Visit;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailableDateController extends Controller
{
    public function index(Request $request): View
    {
        $month = $this->requestedMonth($request);
        $monthRange = [$month->startOfMonth(), $month->endOfMonth()];

        return view('available-dates.index', [
            'month' => $month,
            'today' => CarbonImmutable::today(),
            'days' => CarbonPeriod::create(
                $month->startOfMonth()->startOfWeek(CarbonInterface::MONDAY),
                $month->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY),
            ),
            'availableDates' => AvailableDate::whereBetween('date', $monthRange)
                ->get()
                ->keyBy(fn (AvailableDate $availableDate) => $availableDate->date->toDateString()),
            'visitCounts' => Visit::scheduledCountsByDate(...$monthRange),
        ]);
    }

    public function store(StoreAvailableDateRequest $request): RedirectResponse
    {
        $date = $request->date('date')->startOfDay();

        AvailableDate::firstOrCreate(['date' => $date]);

        return redirect()
            ->route('available-dates.index', ['hilabetea' => $date->format('Y-m')])
            ->with('status', "{$date->format('Y/m/d')} eguna erabilgarri dago orain.");
    }

    public function edit(AvailableDate $availableDate): View
    {
        return view('available-dates.edit', [
            'availableDate' => $availableDate,
            'visitCount' => Visit::whereDate('visit_date', $availableDate->date)
                ->whereIn('status', Visit::SCHEDULED_STATUSES)
                ->count(),
        ]);
    }

    public function update(UpdateAvailableDateRequest $request, AvailableDate $availableDate): RedirectResponse
    {
        $availableDate->update($request->validated());

        return redirect()
            ->route('available-dates.index', ['hilabetea' => $availableDate->date->format('Y-m')])
            ->with('status', "{$availableDate->date->format('Y/m/d')} eguna eguneratuta.");
    }

    public function destroy(AvailableDate $availableDate): RedirectResponse
    {
        $availableDate->delete();

        return redirect()
            ->route('available-dates.index', ['hilabetea' => $availableDate->date->format('Y-m')])
            ->with('status', "{$availableDate->date->format('Y/m/d')} eguna egutegitik kenduta.");
    }

    /**
     * The first day of the month given as ?hilabetea=YYYY-MM, or of the current month.
     */
    private function requestedMonth(Request $request): CarbonImmutable
    {
        $month = (string) $request->query('hilabetea');

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            return CarbonImmutable::createFromFormat('!Y-m', $month);
        }

        return CarbonImmutable::today()->startOfMonth();
    }
}
