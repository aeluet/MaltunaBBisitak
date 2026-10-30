<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;

class CloseVisitController extends Controller
{
    /**
     * Close a visit, checking out everyone in it who is still inside (e.g. a whole group).
     */
    public function __invoke(Visit $visit): RedirectResponse
    {
        if ($visit->status !== VisitStatus::Approved) {
            return back()->with('error', 'Onartutako bisitak bakarrik itxi daitezke.');
        }

        $visit->close();

        return back()->with('status', 'Bisita itxita: barruan zeuden guztien irteera erregistratu da.');
    }
}
