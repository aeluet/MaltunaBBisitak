<?php

use App\Models\Meeting;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    $this->receptionist = User::factory()->harrera()->create();
});

it('forbids teachers from the meetings area', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('meetings.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('lists today and upcoming meetings by default and past meetings on request', function () {
        Meeting::factory()->create(['title' => 'Gaurko bilera', 'date' => '2026-11-10']);
        Meeting::factory()->create(['title' => 'Enpresen topaketa', 'date' => '2026-11-12']);
        Meeting::factory()->create(['title' => 'Udazkeneko bilera', 'date' => '2026-10-01']);

        $this->actingAs($this->receptionist)
            ->get(route('meetings.index'))
            ->assertOk()
            ->assertSeeInOrder(['Gaurko bilera', 'Enpresen topaketa'])
            ->assertDontSee('Udazkeneko bilera');

        $this->actingAs($this->receptionist)
            ->get(route('meetings.index', ['iraganekoak' => 1]))
            ->assertSee('Udazkeneko bilera')
            ->assertDontSee('Enpresen topaketa');
    });
});

describe('store', function () {
    it('creates a meeting with a public uuid', function () {
        $this->actingAs($this->receptionist)
            ->post(route('meetings.store'), [
                'title' => 'Enpresen topaketa',
                'location' => 'Areto nagusia',
                'date' => '2026-11-20',
                'starts_at' => '10:30',
            ]);

        $meeting = Meeting::sole();

        expect($meeting->date->toDateString())->toBe('2026-11-20')
            ->and($meeting->starts_at)->toBe('10:30')
            ->and($meeting->uuid)->toBeUuid();
    });

    it('rejects a malformed start time', function () {
        $this->actingAs($this->receptionist)
            ->post(route('meetings.store'), [
                'title' => 'Enpresen topaketa',
                'location' => 'Areto nagusia',
                'date' => '2026-11-20',
                'starts_at' => '25:00',
            ])
            ->assertSessionHasErrors('starts_at');
    });
});

describe('show', function () {
    it('lists the visitors registered for the meeting', function () {
        $meeting = Meeting::factory()->create();
        $meeting->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre']));

        $this->actingAs($this->receptionist)
            ->get(route('meetings.show', $meeting))
            ->assertOk()
            ->assertSee('Ane Zubiaurre');
    });
});

describe('update', function () {
    it('updates the meeting details', function () {
        $meeting = Meeting::factory()->create();

        $this->actingAs($this->receptionist)
            ->put(route('meetings.update', $meeting), [
                'title' => 'Izenburu berria',
                'location' => 'Liburutegia',
                'date' => '2026-11-21',
                'starts_at' => '',
            ])
            ->assertRedirect(route('meetings.show', $meeting));

        expect($meeting->refresh())
            ->title->toBe('Izenburu berria')
            ->starts_at->toBeNull();
    });
});

describe('destroy', function () {
    it('deletes a meeting without registrations', function () {
        $meeting = Meeting::factory()->create();

        $this->actingAs($this->receptionist)
            ->delete(route('meetings.destroy', $meeting))
            ->assertRedirect(route('meetings.index'));

        $this->assertModelMissing($meeting);
    });

    it('refuses to delete a meeting with registrations', function () {
        $meeting = Meeting::factory()->create();
        $meeting->visitors()->attach(Visitor::factory()->create());

        $this->actingAs($this->receptionist)
            ->delete(route('meetings.destroy', $meeting))
            ->assertSessionHas('error');

        $this->assertModelExists($meeting);
    });
});
