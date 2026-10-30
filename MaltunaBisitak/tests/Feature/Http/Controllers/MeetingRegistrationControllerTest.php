<?php

use App\Models\Meeting;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 09:00'));
    $this->attendee = ['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Elkar', 'email' => 'ane@example.com'];
});

it('registers the attendee from the phone and thanks them in their language', function () {
    $meeting = Meeting::factory()->create(['title' => 'Enpresen topaketa', 'date' => '2026-11-12']);

    $this->post(route('registrations.meetings.store', ['locale' => 'en', 'meeting' => $meeting]), $this->attendee)
        ->assertRedirect(route('registrations.meetings.done', ['locale' => 'en', 'meeting' => $meeting]));

    $this->get(route('registrations.meetings.done', ['locale' => 'en', 'meeting' => $meeting]))
        ->assertOk()
        ->assertSee('Thank you, Ane!')
        ->assertSee('You are registered for: Enpresen topaketa.');

    expect($meeting->visitors()->sole()->email)->toBe('ane@example.com');
});

it('closes registration once the meeting is over', function () {
    $meeting = Meeting::factory()->create(['date' => '2026-11-09']);

    $this->get(route('registrations.meetings.create', ['locale' => 'eu', 'meeting' => $meeting]))
        ->assertSee('Sentitzen dugu, ezin da jada izena eman.');

    $this->post(route('registrations.meetings.store', ['locale' => 'eu', 'meeting' => $meeting]), $this->attendee)
        ->assertNotFound();
});
