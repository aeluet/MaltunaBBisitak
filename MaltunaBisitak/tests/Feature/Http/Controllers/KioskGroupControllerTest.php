<?php

use App\Enums\VisitStatus;
use App\Models\Visit;
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

it('is only available for visits approved for today', function (array $attributes) {
    $visit = Visit::factory()->group(20)->create($attributes);

    $this->get(route('kiosk.groups.show', ['locale' => 'eu', 'visit' => $visit]))->assertNotFound();
})->with([
    'pending today' => [['visit_date' => '2026-11-10']],
    'approved tomorrow' => [['visit_date' => '2026-11-11', 'status' => VisitStatus::Approved]],
]);
