<?php

use App\Models\Meeting;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 09:00'));
    $this->meeting = Meeting::factory()->create(['title' => 'Enpresen topaketa', 'date' => '2026-11-10']);
    $this->attendee = ['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Elkar', 'email' => 'ane@example.com'];
});

it('offers the form and a QR code to register from the phone', function () {
    $this->get(route('kiosk.meetings.show', ['locale' => 'eu', 'meeting' => $this->meeting]))
        ->assertOk()
        ->assertSee('Enpresen topaketa')
        ->assertSee('data:image/png;base64,', escape: false);
});

it('is only available for today\'s meetings', function () {
    $tomorrow = Meeting::factory()->create(['date' => '2026-11-11']);

    $this->get(route('kiosk.meetings.show', ['locale' => 'eu', 'meeting' => $tomorrow]))->assertNotFound();
});

it('registers the attendee and prints a label with the origin they typed', function () {
    $this->post(route('kiosk.meetings.store', ['locale' => 'es', 'meeting' => $this->meeting]), $this->attendee)
        ->assertRedirect(route('kiosk.done', ['locale' => 'es']))
        ->assertSessionHas('kiosk_done.message', '¡Hola, Ane! Te has registrado en: Enpresen topaketa.')
        ->assertSessionHas('kiosk_done.labelUrl', fn (string $url) => str_contains($url, 'nondik=Elkar'));

    expect($this->meeting->visitors()->sole()->email)->toBe('ane@example.com');
});

it('does not register the same person twice', function () {
    $this->post(route('kiosk.meetings.store', ['locale' => 'eu', 'meeting' => $this->meeting]), $this->attendee);
    $this->post(route('kiosk.meetings.store', ['locale' => 'eu', 'meeting' => $this->meeting]), $this->attendee);

    expect($this->meeting->visitors()->count())->toBe(1);
});
