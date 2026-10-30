<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\VisitStatus;
use App\Models\ContractorShift;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitAttendance;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return $request->user()->hasRole(Role::Admin, Role::Harrera)
            ? $this->receptionPanel()
            : $this->hostPanel($request->user());
    }

    private function receptionPanel(): View
    {
        $activeAttendances = VisitAttendance::active()
            ->with(['visitor', 'visit.host'])
            ->orderBy('checked_in_at')
            ->get();

        return view('dashboard.reception', [
            'activeAttendances' => $activeAttendances,
            'peopleInsideCount' => $this->countPeople($activeAttendances),
            'expectedAttendances' => VisitAttendance::whereNull('checked_in_at')
                ->whereHas('visit', fn (Builder $query) => $query
                    ->where('status', VisitStatus::Approved)
                    ->whereDate('visit_date', today()))
                ->with(['visitor', 'visit.host'])
                ->orderBy('visit_id')
                ->get(),
            'checkInCounts' => [
                'today' => VisitAttendance::where('checked_in_at', '>=', today())->count(),
                'week' => VisitAttendance::where('checked_in_at', '>=', today()->startOfWeek(CarbonInterface::MONDAY))->count(),
                'month' => VisitAttendance::where('checked_in_at', '>=', today()->startOfMonth())->count(),
            ],
            'pendingVisitsCount' => Visit::where('status', VisitStatus::Pending)->count(),
            'activeContractorsCount' => ContractorShift::active()->count(),
        ]);
    }

    private function hostPanel(User $host): View
    {
        return view('dashboard.host', [
            'visits' => Visit::whereBelongsTo($host, 'host')
                ->where('visit_date', '>=', today())
                ->whereIn('status', [VisitStatus::Pending, VisitStatus::Approved])
                ->with('visitors')
                ->orderBy('visit_date')
                ->limit(20)
                ->get(),
        ]);
    }

    /**
     * Named visitors inside, plus the unnamed members of groups (a group of 20 with one named organiser counts 20).
     *
     * @param  Collection<int, VisitAttendance>  $activeAttendances
     */
    private function countPeople(Collection $activeAttendances): int
    {
        return $activeAttendances->groupBy('visit_id')->sum(function (Collection $attendances): int {
            $visit = $attendances->first()->visit;

            return $visit->is_group ? max($attendances->count(), (int) $visit->group_size) : $attendances->count();
        });
    }
}
