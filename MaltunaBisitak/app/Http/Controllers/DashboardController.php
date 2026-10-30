<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ContractorShift;
use App\Models\Visit;
use App\Models\VisitAttendance;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'activeVisitorsCount' => VisitAttendance::active()->count(),
            'pendingVisitsCount' => Visit::where('status', VisitStatus::Pending)->count(),
            'activeContractorsCount' => ContractorShift::active()->count(),
        ]);
    }
}
