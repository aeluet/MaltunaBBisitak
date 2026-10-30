<?php

use App\Enums\VisitStatus;
use App\Models\Visit;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 09:00'));
    $this->visit = Visit::factory()->group(20)->approved()->create(['visit_date' => '2026-11-12', 'origin' => 'Arizmendi Ikastola']);
    $this->attendee = ['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Arizmendi Ikastola', 'email' => 'ane@example.com'];
});

it('shows the visit from outside the school network', function () {
    config(['bisitak.internal_networks' => ['10.0.0.0/8']]);

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5'])
        ->get(route('registrations.visits.create', ['locale' => 'eu', 'visit' => $this->visit]))
        ->assertOk()
        ->assertSee('Arizmendi Ikastola')
        ->assertSee('osteguna, 2026ko azaroaren 12a');
});

it('registers the visitor without checking them in and shows their own QR code', function () {
    $this->post(route('registrations.visits.store', ['locale' => 'eu', 'visit' => $this->visit]), $this->attendee)
        ->assertRedirect(route('registrations.visits.done', ['locale' => 'eu', 'visit' => $this->visit]));

    $attendance = $this->visit->attendances()->sole();

    expect($attendance->checked_in_at)->toBeNull();

    $this->get(route('registrations.visits.done', ['locale' => 'eu', 'visit' => $this->visit]))
        ->assertOk()
        ->assertSee($attendance->code)
        ->assertSee('data:image/png;base64,', escape: false);
});

it('gives the same code to someone who registers twice', function () {
    $this->post(route('registrations.visits.store', ['locale' => 'eu', 'visit' => $this->visit]), $this->attendee);
    $this->post(route('registrations.visits.store', ['locale' => 'eu', 'visit' => $this->visit]), $this->attendee);

    expect($this->visit->attendances()->count())->toBe(1);
});

it('closes registration for visits that are not approved or already past', function (array $attributes) {
    $visit = Visit::factory()->create($attributes);

    $this->get(route('registrations.visits.create', ['locale' => 'eu', 'visit' => $visit]))
        ->assertOk()
        ->assertSee('Sentitzen dugu, ezin da jada izena eman.')
        ->assertDontSee('name="first_name"', escape: false);

    $this->post(route('registrations.visits.store', ['locale' => 'eu', 'visit' => $visit]), $this->attendee)
        ->assertNotFound();
})->with([
    'pending' => [['visit_date' => '2026-11-12']],
    'cancelled' => [['visit_date' => '2026-11-12', 'status' => VisitStatus::Cancelled]],
    'past' => [['visit_date' => '2026-11-09', 'status' => VisitStatus::Approved]],
]);

it('returns 404 for a link that is not a visit', function () {
    $this->get('/eu/bisita/ez-da-uuid-bat')->assertNotFound();
});
