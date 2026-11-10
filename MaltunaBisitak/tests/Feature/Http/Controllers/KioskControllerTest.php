<?php

use App\Enums\VisitStatus;
use App\Models\Meeting;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 09:00'));
});

it('is only available from the school network', function () {
    config(['bisitak.internal_networks' => ['10.0.0.0/8']]);

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
        ->get(route('kiosk.home', ['locale' => 'eu']))
        ->assertForbidden();
});

it('sends the short addresses to the Basque start screen', function (string $address) {
    $this->get($address)->assertRedirect(route('kiosk.home', ['locale' => 'eu']));
})->with(['/pedestala', '/pedestal']);

describe('home', function () {
    it('offers the meeting and visit buttons with how many there are today', function () {
        Meeting::factory()->count(2)->create(['date' => '2026-11-10']);
        Meeting::factory()->create(['date' => '2026-11-11']);
        Visit::factory()->group(20)->approved()->today()->create(['origin' => 'Gaurko taldea']);
        Visit::factory()->group(20)->today()->create(['origin' => 'Zain dagoen taldea']);

        $this->get(route('kiosk.home', ['locale' => 'eu']))
            ->assertOk()
            ->assertSee(route('kiosk.meetings.index', ['locale' => 'eu']))
            ->assertSee(route('kiosk.groups.index', ['locale' => 'eu']))
            ->assertSeeInOrder(['Bilera', 'Gaur: 2', 'Bisita', 'Gaur: 1'])
            ->assertSee('Aplikazio hau WAGeko ikasle eta irakasleen arteko elkarlanean garatu da.');
    });
});

describe('check in', function () {
    it('checks in the visitor of the scanned code and prints their label', function () {
        $visit = Visit::factory()->approved()->today()->create(['origin' => 'Arizmendi']);
        $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane']));
        $attendance = $visit->attendances()->sole();

        $this->post(route('kiosk.check-in', ['locale' => 'eu']), ['code' => ' '.strtolower($attendance->code)])
            ->assertRedirect(route('kiosk.done', ['locale' => 'eu']))
            ->assertSessionHas('kiosk_done.message', 'Ongi etorri, Ane!')
            ->assertSessionHas('kiosk_done.labelUrl', fn (string $url) => str_contains($url, 'nondik=Arizmendi'));

        $attendance->refresh();

        expect($attendance->checked_in_at->toDateTimeString())->toBe('2026-11-10 09:00:00')
            ->and($attendance->label_printed_at)->not->toBeNull();
    });

    it('only reprints the label when the visitor is already inside', function () {
        $visit = Visit::factory()->approved()->today()->create();
        $visit->visitors()->attach(Visitor::factory()->create(), ['checked_in_at' => '2026-11-10 08:30']);
        $attendance = $visit->attendances()->sole();

        $this->post(route('kiosk.check-in', ['locale' => 'eu']), ['code' => $attendance->code])
            ->assertRedirect(route('kiosk.done', ['locale' => 'eu']));

        expect($attendance->refresh()->checked_in_at->format('H:i'))->toBe('08:30');
    });

    it('explains in the chosen language why a code cannot be used', function (array $visitAttributes, array $pivot, string $message) {
        $visit = Visit::factory()->create($visitAttributes);
        $visit->visitors()->attach(Visitor::factory()->create(), $pivot);

        $this->post(route('kiosk.check-in', ['locale' => 'eu']), ['code' => $visit->attendances()->sole()->code])
            ->assertRedirect(route('kiosk.home', ['locale' => 'eu']))
            ->assertSessionHas('kiosk_error', $message);

        expect($visit->attendances()->sole()->label_printed_at)->toBeNull();
    })->with([
        'a pending visit' => [['visit_date' => '2026-11-10'], [], 'Bisita hau ez dago baieztatuta. Galdetu harreran, mesedez.'],
        'a visit on another day' => [['visit_date' => '2026-11-12', 'status' => VisitStatus::Approved], [], 'Zure bisita beste egun batean da: osteguna, 2026ko azaroaren 12a.'],
        'a visitor who already left' => [
            ['visit_date' => '2026-11-10', 'status' => VisitStatus::Approved],
            ['checked_in_at' => '2026-11-10 08:00', 'checked_out_at' => '2026-11-10 08:30'],
            'Bisita hau amaitu da. Galdetu harreran, mesedez.',
        ],
    ]);

    it('rejects an unknown code', function () {
        $this->post(route('kiosk.check-in', ['locale' => 'en']), ['code' => 'ZZZZZZZZZZ'])
            ->assertRedirect(route('kiosk.home', ['locale' => 'en']))
            ->assertSessionHas('kiosk_error', 'We could not find that code. Please ask at reception.');
    });
});

describe('done', function () {
    it('downloads the label and greets the visitor', function () {
        $this->withSession(['kiosk_done' => ['message' => 'Ongi etorri, Ane!', 'labelUrl' => 'https://example.test/etiketa']])
            ->get(route('kiosk.done', ['locale' => 'eu']))
            ->assertOk()
            ->assertSee('Ongi etorri, Ane!')
            ->assertSee('data-label-url="https://example.test/etiketa"', escape: false);
    });

    it('goes back to the start screen when there is nothing to show', function () {
        $this->get(route('kiosk.done', ['locale' => 'eu']))
            ->assertRedirect(route('kiosk.home', ['locale' => 'eu']));
    });
});
