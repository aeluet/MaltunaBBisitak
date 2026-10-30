<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Mail\VisitApproved;
use App\Mail\VisitCancelled;
use App\Mail\VisitRejected;
use App\Models\AvailableDate;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Staff review of visit requests (AP002): approve with a QR per visitor (AP003), reject or cancel.
 */
class VisitController extends Controller
{
    private const array FILTERS = ['zain', 'onartuak', 'guztiak'];

    public function index(Request $request): View
    {
        $filter = in_array($request->query('egoera'), self::FILTERS, true) ? $request->query('egoera') : 'zain';

        $visits = Visit::query()
            ->with(['types', 'host'])
            ->withCount('visitors')
            ->when($filter === 'zain', fn (Builder $query) => $query
                ->where('status', VisitStatus::Pending)
                ->orderBy('visit_date'))
            ->when($filter === 'onartuak', fn (Builder $query) => $query
                ->where('status', VisitStatus::Approved)
                ->where('visit_date', '>=', today())
                ->orderBy('visit_date'))
            ->when($filter === 'guztiak', fn (Builder $query) => $query->orderByDesc('visit_date'))
            ->paginate(25)
            ->withQueryString();

        return view('visits.index', [
            'visits' => $visits,
            'filter' => $filter,
            'pendingCount' => Visit::where('status', VisitStatus::Pending)->count(),
        ]);
    }

    public function show(Visit $visit): View
    {
        $visit->load(['types', 'host', 'reviewer', 'visitors']);

        return view('visits.show', [
            'visit' => $visit,
            'availableDate' => AvailableDate::whereDate('date', $visit->visit_date)->first(),
            'sameDayCount' => Visit::whereDate('visit_date', $visit->visit_date)
                ->whereIn('status', Visit::SCHEDULED_STATUSES)
                ->count(),
            'hosts' => User::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'registrationUrl' => route('registrations.visits.create', ['locale' => $visit->locale, 'visit' => $visit]),
        ]);
    }

    public function approve(Request $request, Visit $visit): RedirectResponse
    {
        if ($visit->status !== VisitStatus::Pending) {
            return back()->with('error', 'Zain dauden eskaerak bakarrik onar daitezke.');
        }

        $validated = $request->validate([
            'host_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
            'response_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $visit->host_id = $validated['host_id'] ?? null;
        $visit->approve($request->user(), $validated['response_message'] ?? null);

        $sent = $this->mailVisitors($visit, fn (Visitor $visitor) => new VisitApproved($visit, $visitor->first_name, $visitor->attendance->code));

        return redirect()->route('visits.show', $visit)
            ->with('status', "Bisita onartuta. QR kodea {$sent} bisitariri bidali zaie emailez.");
    }

    public function reject(Request $request, Visit $visit): RedirectResponse
    {
        if ($visit->status !== VisitStatus::Pending) {
            return back()->with('error', 'Zain dauden eskaerak bakarrik uka daitezke.');
        }

        $visit->reject($request->user(), $this->validatedMessage($request));

        $sent = $this->mailVisitors($visit, fn (Visitor $visitor) => new VisitRejected($visit, $visitor->first_name));

        return redirect()->route('visits.show', $visit)
            ->with('status', "Eskaera ukatuta. {$sent} bisitariri jakinarazi zaie.");
    }

    public function cancel(Request $request, Visit $visit): RedirectResponse
    {
        if ($visit->status !== VisitStatus::Approved) {
            return back()->with('error', 'Onartutako bisitak bakarrik utz daitezke bertan behera.');
        }

        if ($visit->attendances()->active()->exists()) {
            return back()->with('error', 'Bisitariak ikastetxean daude: erregistratu haien irteera edo itxi bisita.');
        }

        $visit->cancel($request->user(), $this->validatedMessage($request));

        $sent = $this->mailVisitors($visit, fn (Visitor $visitor) => new VisitCancelled($visit, $visitor->first_name));

        return redirect()->route('visits.show', $visit)
            ->with('status', "Bisita bertan behera utzita. {$sent} bisitariri jakinarazi zaie.");
    }

    private function validatedMessage(Request $request): ?string
    {
        return $request->validate(['response_message' => ['nullable', 'string', 'max:2000']])['response_message'] ?? null;
    }

    /**
     * Every visitor with an email gets their own message in the language of the request (WP009).
     * The decision is already saved, so a mail failure is only reported.
     *
     * @param  Closure(Visitor): Mailable  $mailFor
     */
    private function mailVisitors(Visit $visit, Closure $mailFor): int
    {
        $recipients = $visit->visitors->filter(fn (Visitor $visitor) => filled($visitor->email));

        foreach ($recipients as $visitor) {
            rescue(fn () => Mail::to($visitor->email)->locale($visit->locale)->send($mailFor($visitor)));
        }

        return $recipients->count();
    }
}
