<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingReceived;
use App\Mail\NewBookingRequest;
use App\Models\AvailableDate;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitType;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Public visit requests (WP002–WP009): anyone picks an open day and sends a request that staff review later.
 */
class BookingController extends Controller
{
    public const int MAX_VISITORS = 10;

    private const int MONTHS_AHEAD = 6;

    public function create(): View
    {
        $from = CarbonImmutable::tomorrow();
        $until = CarbonImmutable::today()->addMonthsNoOverflow(self::MONTHS_AHEAD)->endOfMonth();

        $availableDates = AvailableDate::whereBetween('date', [$from, $until])->orderBy('date')->get();
        $scheduledCounts = Visit::scheduledCountsByDate($from, $until);

        $months = $availableDates
            ->map(fn (AvailableDate $availableDate) => $availableDate->date->format('Y-m'))
            ->unique()
            ->map(fn (string $month) => CarbonImmutable::createFromFormat('!Y-m', $month));

        return view('bookings.create', [
            'calendar' => $months->map(fn (CarbonImmutable $month) => [
                'month' => $month,
                'days' => CarbonPeriod::create(
                    $month->startOfWeek(CarbonInterface::MONDAY),
                    $month->endOfMonth()->endOfWeek(CarbonInterface::SUNDAY),
                ),
            ])->values(),
            'openDates' => $availableDates->mapWithKeys(fn (AvailableDate $availableDate) => [
                $availableDate->date->toDateString() => ! $availableDate->isFull(
                    $scheduledCounts->get($availableDate->date->toDateString(), 0),
                ),
            ]),
            'weekdays' => collect(range(0, 6))->map(
                fn (int $offset) => CarbonImmutable::today()->startOfWeek(CarbonInterface::MONDAY)->addDays($offset)->minDayName,
            ),
            'visitTypes' => VisitType::active()->get(),
            'countries' => Visitor::countryOptions(),
            'maxVisitors' => self::MAX_VISITORS,
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $visit = DB::transaction(function () use ($request): Visit {
            $visit = Visit::create([
                'visit_date' => $request->validated('visit_date'),
                'origin' => $request->validated('origin'),
                'is_group' => $request->boolean('is_group'),
                'group_size' => $request->validated('group_size'),
                'other_type' => $request->validated('other_type'),
                'notes' => $request->validated('notes'),
                'locale' => app()->getLocale(),
            ]);

            $visit->types()->attach($request->validated('types', []));
            $visit->visitors()->attach(
                collect($request->validated('visitors'))
                    ->map(fn (array $visitor) => Visitor::matchOrCreate([...$visitor, 'origin' => $visit->origin])->id)
                    ->unique()
                    ->all(),
            );

            return $visit;
        });

        // The first block of the form is the contact person; validation requires their email.
        $this->sendNotifications($visit, contact: $request->validated('visitors.0'));

        return redirect()
            ->route('bookings.thanks')
            ->with('booking_date', $visit->visit_date->toDateString());
    }

    public function thanks(): View
    {
        return view('bookings.thanks');
    }

    /**
     * The request is already saved, so a mail failure is only reported: staff still see it in the panel.
     *
     * @param  array{first_name: string, email: string}  $contact
     */
    private function sendNotifications(Visit $visit, array $contact): void
    {
        rescue(fn () => Mail::to($contact['email'])
            ->locale($visit->locale)
            ->send(new BookingReceived($visit, $contact['first_name'])));

        $staffRecipients = config('bisitak.notification_email')
            ? [config('bisitak.notification_email')]
            : User::where('role', Role::Admin)->where('is_active', true)->pluck('email')->all();

        if ($staffRecipients !== []) {
            rescue(fn () => Mail::to($staffRecipients)->locale('eu')->send(new NewBookingRequest($visit)));
        }
    }
}
