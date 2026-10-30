<?php

use App\Enums\VisitStatus;
use App\Mail\BookingReceived;
use App\Mail\NewBookingRequest;
use App\Models\AvailableDate;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    Mail::fake();

    $this->visitType = VisitType::factory()->create(['name_eu' => 'Tailerra 4.0', 'name_es' => 'Taller 4.0', 'name_en' => 'Workshop 4.0']);
    AvailableDate::factory()->create(['date' => '2026-11-12']);

    $this->payload = [
        'types' => [$this->visitType->id],
        'visit_date' => '2026-11-12',
        'origin' => 'Arizmendi Ikastola',
        'is_group' => '1',
        'group_size' => '24',
        'visitors' => [
            ['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'email' => 'ane@example.com', 'country' => 'ES'],
            ['first_name' => 'Jon', 'last_name' => 'Agirre', 'email' => '', 'country' => 'FR'],
        ],
        'privacy' => '1',
    ];
});

it('sends visitors to the booking page in their preferred language', function () {
    $this->get('/', ['Accept-Language' => 'es-ES,es;q=0.9'])
        ->assertRedirect(route('bookings.create', ['locale' => 'es']));

    $this->get('/', ['Accept-Language' => 'fr-FR'])
        ->assertRedirect(route('bookings.create', ['locale' => 'eu']));
});

it('returns 404 for unsupported languages', function () {
    $this->get('/fr')->assertNotFound();
});

it('is reachable from outside the school network', function () {
    config(['bisitak.internal_networks' => ['10.0.0.0/8']]);

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
        ->get(route('bookings.create', ['locale' => 'eu']))
        ->assertOk();
});

describe('create', function () {
    it('offers the active visit types and the open days in the chosen language', function () {
        VisitType::factory()->inactive()->create(['name_es' => 'Tipo oculto']);
        AvailableDate::factory()->create(['date' => '2026-11-10']);
        AvailableDate::factory()->create(['date' => '2026-11-13', 'max_visits' => 1]);
        Visit::factory()->create(['visit_date' => '2026-11-13']);

        $this->get(route('bookings.create', ['locale' => 'es']))
            ->assertOk()
            ->assertSee('Solicitar una visita')
            ->assertSee('Taller 4.0')
            ->assertDontSee('Tipo oculto')
            ->assertSee('noviembre de 2026')
            ->assertSee('value="2026-11-12"', escape: false)
            ->assertDontSee('value="2026-11-13"', escape: false)
            ->assertDontSee('value="2026-11-10"', escape: false);
    });

    it('explains when there are no open days', function () {
        AvailableDate::query()->delete();

        $this->get(route('bookings.create', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('There are no days available for visits at the moment.')
            ->assertDontSee('Send request');
    });
});

describe('store', function () {
    it('saves a pending group visit with its visitors in the chosen language', function () {
        $this->post(route('bookings.store', ['locale' => 'es']), $this->payload)
            ->assertRedirect(route('bookings.thanks', ['locale' => 'es']));

        $visit = Visit::with(['types', 'visitors'])->sole();

        expect($visit->status)->toBe(VisitStatus::Pending)
            ->and($visit->locale)->toBe('es')
            ->and($visit->visit_date->toDateString())->toBe('2026-11-12')
            ->and($visit->origin)->toBe('Arizmendi Ikastola')
            ->and($visit->is_group)->toBeTrue()
            ->and($visit->group_size)->toBe(24)
            ->and($visit->types->modelKeys())->toBe([$this->visitType->id])
            ->and($visit->visitors->map->full_name->sort()->values()->all())->toBe(['Ane Zubiaurre', 'Jon Agirre'])
            ->and(Visitor::where('email', 'ane@example.com')->sole()->origin)->toBe('Arizmendi Ikastola');
    });

    it('accepts an individual visit with only a free-text type', function () {
        $this->post(route('bookings.store', ['locale' => 'eu']), [
            ...$this->payload,
            'types' => [],
            'other_type' => 'Robotika',
            'is_group' => '0',
            'group_size' => '',
        ])->assertSessionHasNoErrors();

        $visit = Visit::sole();

        expect($visit->is_group)->toBeFalse()
            ->and($visit->group_size)->toBeNull()
            ->and($visit->other_type)->toBe('Robotika');
    });

    it('emails the requester in their language and the active admins', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->inactive()->create();
        User::factory()->harrera()->create();

        $this->post(route('bookings.store', ['locale' => 'en']), $this->payload);

        Mail::assertSent(BookingReceived::class, fn (BookingReceived $mail) => $mail->hasTo('ane@example.com')
            && $mail->locale === 'en'
            && $mail->contactName === 'Ane');
        Mail::assertSent(NewBookingRequest::class, fn (NewBookingRequest $mail) => $mail->hasTo($admin->email)
            && count($mail->to) === 1);
    });

    it('sends the staff notification to the configured address instead of the admins', function () {
        config(['bisitak.notification_email' => 'harrera@example.com']);
        User::factory()->admin()->create();

        $this->post(route('bookings.store', ['locale' => 'eu']), $this->payload);

        Mail::assertSent(NewBookingRequest::class, fn (NewBookingRequest $mail) => $mail->hasTo('harrera@example.com')
            && count($mail->to) === 1);
    });

    it('reuses a visitor whose email and names match without changing their record', function () {
        $existing = Visitor::factory()->create([
            'first_name' => 'Ane',
            'last_name' => 'Zubiaurre',
            'email' => 'ane@example.com',
            'origin' => 'Lehengo enpresa',
        ]);

        $this->post(route('bookings.store', ['locale' => 'eu']), $this->payload);

        expect(Visitor::where('email', 'ane@example.com')->count())->toBe(1)
            ->and($existing->refresh()->origin)->toBe('Lehengo enpresa')
            ->and(Visit::sole()->visitors->modelKeys())->toContain($existing->id);
    });

    it('creates a new visitor when only the email matches', function () {
        Visitor::factory()->create(['first_name' => 'Beste', 'last_name' => 'Bat', 'email' => 'ane@example.com']);

        $this->post(route('bookings.store', ['locale' => 'eu']), $this->payload);

        expect(Visitor::where('email', 'ane@example.com')->count())->toBe(2);
    });

    it('rejects invalid requests without saving or sending anything', function (array $override, string $field) {
        $this->post(route('bookings.store', ['locale' => 'eu']), [...$this->payload, ...$override])
            ->assertSessionHasErrors($field);

        expect(Visit::count())->toBe(0);
        Mail::assertNothingSent();
    })->with([
        'a day that is not open' => [['visit_date' => '2026-11-20'], 'visit_date'],
        'a past day' => [['visit_date' => '2026-11-09'], 'visit_date'],
        'no type at all' => [['types' => []], 'types'],
        'a group without its size' => [['group_size' => ''], 'group_size'],
        'no visitors' => [['visitors' => []], 'visitors'],
        'a contact without email' => [['visitors' => [['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'email' => '', 'country' => 'ES']]], 'visitors.0.email'],
        'an unknown country' => [['visitors' => [['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'email' => 'ane@example.com', 'country' => 'XX']]], 'visitors.0.country'],
        'privacy not accepted' => [['privacy' => ''], 'privacy'],
        'a filled honeypot' => [['website' => 'https://spam.example'], 'website'],
    ]);

    it('rejects a day that is already full', function () {
        AvailableDate::whereDate('date', '2026-11-12')->update(['max_visits' => 1]);
        Visit::factory()->approved()->create(['visit_date' => '2026-11-12']);

        $this->post(route('bookings.store', ['locale' => 'eu']), $this->payload)
            ->assertSessionHasErrors(['visit_date' => 'Egun hori bete egin da. Aukeratu beste bat.']);
    });

    it('shows validation messages in the chosen language', function () {
        $this->post(route('bookings.store', ['locale' => 'es']), [...$this->payload, 'origin' => ''])
            ->assertSessionHasErrors(['origin' => 'El campo centro, empresa u organización es obligatorio.']);
    });

    it('limits the number of requests per hour', function () {
        foreach (range(1, 10) as $attempt) {
            $this->post(route('bookings.store', ['locale' => 'eu']));
        }

        $this->post(route('bookings.store', ['locale' => 'eu']))->assertTooManyRequests();
    });
});

describe('thanks', function () {
    it('confirms the requested day in the chosen language', function () {
        $this->withSession(['booking_date' => '2026-11-12'])
            ->get(route('bookings.thanks', ['locale' => 'es']))
            ->assertOk()
            ->assertSee('¡Muchas gracias!')
            ->assertSee('jueves, 12 de noviembre de 2026');
    });
});
