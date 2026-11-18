<?php

use App\Enums\VisitStatus;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 09:00'));
});

it('registers a group member at the kiosk, checks them in and prints their label', function () {
    $visit = Visit::factory()->group(20)->approved()->today()->create(['origin' => 'Arizmendi']);

    $this->post(route('kiosk.groups.store', ['locale' => 'eu', 'visit' => $visit]), [
        'first_name' => 'Jon',
        'last_name' => 'Agirre',
        'origin' => 'Arizmendi',
        'email' => 'jon@example.com',
    ])
        ->assertRedirect(route('kiosk.done', ['locale' => 'eu']))
        ->assertSessionHas('kiosk_done.message', 'Ongi etorri, Jon!');

    $attendance = $visit->attendances()->with('visitor')->sole();

    expect($attendance->visitor->email)->toBe('jon@example.com')
        ->and($attendance->checked_in_at->toDateTimeString())->toBe('2026-11-10 09:00:00');
});

it('lets someone who came along register in a visit booked for one person', function () {
    $visit = Visit::factory()->approved()->today()->create(['origin' => 'Elkar']);
    $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane']), ['checked_in_at' => '2026-11-10 08:55']);

    $this->get(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $visit]))
        ->assertOk()
        ->assertSee('Elkar');

    $this->post(route('kiosk.groups.store', ['locale' => 'eu', 'visit' => $visit]), [
        'first_name' => 'Jon',
        'last_name' => 'Agirre',
        'origin' => 'Elkar',
        'email' => 'jon@example.com',
    ])->assertRedirect(route('kiosk.done', ['locale' => 'eu']));

    expect($visit->attendances()->count())->toBe(2)
        ->and($visit->refresh()->is_group)->toBeFalse();
});

it('is only available for visits approved for today', function (array $attributes) {
    $visit = Visit::factory()->group(20)->create($attributes);

    $this->get(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $visit]))->assertNotFound();
    $this->post(route('kiosk.groups.print', ['locale' => 'eu', 'visit' => $visit]))->assertNotFound();
})->with([
    'pending today' => [['visit_date' => '2026-11-10']],
    'approved tomorrow' => [['visit_date' => '2026-11-11', 'status' => VisitStatus::Approved]],
]);

describe('index', function () {
    it('lists every visit approved for today, in a group or not, and offers to create a new one', function () {
        Visit::factory()->group(20)->approved()->today()->create(['origin' => 'Gaurko taldea']);
        Visit::factory()->approved()->today()->create(['origin' => 'Bakarkako bisita']);
        Visit::factory()->group(20)->today()->create(['origin' => 'Zain dagoen taldea']);
        Visit::factory()->approved()->create(['visit_date' => '2026-11-11', 'origin' => 'Biharko bisita']);
        Visit::factory()->approved()->today()->create(['origin' => null]);

        $this->get(route('kiosk.groups.index', ['locale' => 'eu']))
            ->assertOk()
            ->assertSee('Zein bisitatara zatozte?')
            ->assertSee('Gaurko taldea')
            ->assertSee('Bakarkako bisita')
            ->assertSee('Izenik gabeko bisita')
            ->assertDontSee('Zain dagoen taldea')
            ->assertDontSee('Biharko bisita')
            ->assertSee(route('kiosk.groups.open', ['locale' => 'eu']));
    });

    it('says when there are no visits today', function () {
        $this->get(route('kiosk.groups.index', ['locale' => 'es']))
            ->assertOk()
            ->assertSee('Hoy no hay visitas.');
    });
});

describe('open', function () {
    it('creates an approved group visit for today and goes to its page', function () {
        $response = $this->post(route('kiosk.groups.open', ['locale' => 'es']), ['origin' => '  Usandizaga BHI ']);

        $visit = Visit::sole();

        $response->assertRedirect(route('kiosk.groups.show', ['locale' => 'es', 'visit' => $visit]));

        expect($visit->origin)->toBe('Usandizaga BHI')
            ->and($visit->is_group)->toBeTrue()
            ->and($visit->status)->toBe(VisitStatus::Approved)
            ->and($visit->visit_date->toDateString())->toBe('2026-11-10')
            ->and($visit->locale)->toBe('es');

        $this->get(route('kiosk.groups.show', ['locale' => 'es', 'visit' => $visit]))
            ->assertOk()
            ->assertSee('Todavía no se ha registrado nadie');
    });

    it('reuses today\'s group with the same name', function () {
        $existing = Visit::factory()->group(20)->approved()->today()->create(['origin' => 'Usandizaga BHI']);

        $this->post(route('kiosk.groups.open', ['locale' => 'eu']), ['origin' => 'usandizaga bhi'])
            ->assertRedirect(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $existing]));

        expect(Visit::count())->toBe(1);
    });

    it('reuses today\'s visit for one person with the same name', function () {
        $existing = Visit::factory()->approved()->today()->create(['origin' => 'Elkar']);

        $this->post(route('kiosk.groups.open', ['locale' => 'eu']), ['origin' => 'elkar'])
            ->assertRedirect(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $existing]));

        expect(Visit::count())->toBe(1);
    });

    it('requires the name of the group', function () {
        $this->post(route('kiosk.groups.open', ['locale' => 'eu']), ['origin' => ''])->assertSessionHasErrors('origin');

        expect(Visit::count())->toBe(0);
    });
});

describe('print all', function () {
    beforeEach(function () {
        $this->visit = Visit::factory()->group(20)->approved()->today()->create(['origin' => 'Arizmendi']);
        [$this->inside, $this->registered, $this->left] = Visitor::factory()->count(3)->sequence(
            ['first_name' => 'Ane'], ['first_name' => 'Jon'], ['first_name' => 'Miren'],
        )->create();

        $this->visit->visitors()->attach($this->inside, ['checked_in_at' => now()->subHour()]);
        $this->visit->visitors()->attach($this->registered);
        $this->visit->visitors()->attach($this->left, ['checked_in_at' => now()->subHours(2), 'checked_out_at' => now()->subHour()]);
    });

    it('lists who signed in with their status', function () {
        $this->get(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $this->visit]))
            ->assertOk()
            ->assertSeeInOrder(['Ane', 'Barruan', 'Jon', 'Iristeko', 'Miren', 'Irten da'])
            ->assertSee(route('kiosk.groups.print', ['locale' => 'eu', 'visit' => $this->visit]))
            ->assertDontSee($this->inside->email);
    });

    it('prints the labels of everyone still in the group and checks in who had not arrived', function () {
        $this->post(route('kiosk.groups.print', ['locale' => 'eu', 'visit' => $this->visit]))
            ->assertRedirect(route('kiosk.done', ['locale' => 'eu']))
            ->assertSessionHas('kiosk_done.message', '2 etiketa inprimatzen…')
            ->assertSessionHas('kiosk_done.labelUrls', fn (array $urls) => count($urls) === 2
                && str_contains($urls[0], "etiketa/{$this->inside->id}")
                && str_contains($urls[1], "etiketa/{$this->registered->id}"));

        $attendances = $this->visit->attendances()->get()->keyBy('visitor_id');

        expect($attendances[$this->inside->id]->checked_in_at->toDateTimeString())->toBe('2026-11-10 08:00:00')
            ->and($attendances[$this->registered->id]->checked_in_at->toDateTimeString())->toBe('2026-11-10 09:00:00')
            ->and($attendances[$this->registered->id]->label_printed_at)->not->toBeNull()
            ->and($attendances[$this->left->id]->label_printed_at)->toBeNull();
    });

    it('shows every label download on the done screen', function () {
        $this->post(route('kiosk.groups.print', ['locale' => 'eu', 'visit' => $this->visit]));

        $this->get(route('kiosk.done', ['locale' => 'eu']))
            ->assertOk()
            ->assertSee('data-label-urls=', escape: false)
            ->assertSee('data-kiosk-print-labels', escape: false)
            ->assertSee('Hartu etiketak inprimagailutik.');
    });

    it('says so when there is nobody to print', function () {
        $empty = Visit::factory()->group(20)->approved()->today()->create();

        $this->post(route('kiosk.groups.print', ['locale' => 'eu', 'visit' => $empty]))
            ->assertRedirect(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $empty]))
            ->assertSessionHas('kiosk_error', 'Ez dago inprimatzeko etiketarik.');
    });
});
