<?php

use App\Models\Meeting;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 09:00'));
    $this->meeting = Meeting::factory()->create(['title' => 'Enpresen topaketa', 'date' => '2026-11-10']);
    $this->attendee = ['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Elkar', 'email' => 'ane@example.com'];
});

it('lists today\'s meetings', function () {
    Meeting::factory()->create(['title' => 'Biharko bilera', 'date' => '2026-11-11']);

    $this->get(route('kiosk.meetings.index', ['locale' => 'eu']))
        ->assertOk()
        ->assertSee('Enpresen topaketa')
        ->assertDontSee('Biharko bilera');
});

it('says when there are no meetings today', function () {
    $this->meeting->delete();

    $this->get(route('kiosk.meetings.index', ['locale' => 'eu']))->assertOk()->assertSee('Gaur ez dago bilerarik.');
});

it('offers the form and a QR code to register from the phone', function () {
    $this->get(route('kiosk.meetings.show', ['locale' => 'eu', 'meeting' => $this->meeting]))
        ->assertOk()
        ->assertSee('Enpresen topaketa')
        ->assertSee('data:image/png;base64,', escape: false)
        ->assertSee('name="country"', escape: false)
        ->assertSee('<option value="XE" selected>Euskal Herria</option>', escape: false)
        ->assertSee('Izena ematean, datu-babesari buruzko arauak onartzen dituzu.')
        ->assertSee(config('bisitak.privacy_url'));
});

it('keeps the optional country of the attendee', function () {
    $this->post(route('kiosk.meetings.store', ['locale' => 'eu', 'meeting' => $this->meeting]), [...$this->attendee, 'country' => 'FR']);

    expect($this->meeting->visitors()->sole()->country)->toBe('FR');
});

it('rejects an unknown country code', function () {
    $this->post(route('kiosk.meetings.store', ['locale' => 'eu', 'meeting' => $this->meeting]), [...$this->attendee, 'country' => 'XX'])
        ->assertSessionHasErrors('country');

    expect($this->meeting->visitors()->count())->toBe(0);
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
