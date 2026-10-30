<?php

use App\Enums\VisitStatus;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitAttendance;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    $this->receptionist = User::factory()->harrera()->create();
});

it('forbids teachers from registering walk-ins', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('walk-ins.create'))
        ->assertForbidden();
});

describe('create', function () {
    it('finds visitors and flags those who are already inside', function () {
        $inside = Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Etxeberria']);
        $outside = Visitor::factory()->create(['first_name' => 'Jon', 'last_name' => 'Etxeberria']);
        Visit::factory()->approved()->today()->create()->visitors()->attach($inside, ['checked_in_at' => now()]);

        $this->actingAs($this->receptionist)
            ->get(route('walk-ins.create', ['q' => 'etxeberria']))
            ->assertOk()
            ->assertSee('Ikastetxean dago')
            ->assertSee(route('walk-ins.create', ['bisitaria' => $outside->id]))
            ->assertDontSee(route('walk-ins.create', ['bisitaria' => $inside->id]));
    });
});

describe('store', function () {
    it('checks in an existing visitor with a host, reason and badge', function () {
        $visitor = Visitor::factory()->create(['origin' => 'Arizmendi']);
        $teacher = User::factory()->create();

        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), [
                'visitor_id' => $visitor->id,
                'host_id' => $teacher->id,
                'notes' => 'Aurrekontua aurkeztera',
                'badge_number' => '7',
            ])
            ->assertRedirect(route('dashboard'));

        $visit = Visit::sole();
        $attendance = $visit->attendances()->sole();

        expect($visit->status)->toBe(VisitStatus::Approved)
            ->and($visit->visit_date->toDateString())->toBe('2026-11-10')
            ->and($visit->host_id)->toBe($teacher->id)
            ->and($visit->origin)->toBe('Arizmendi')
            ->and($visit->notes)->toBe('Aurrekontua aurkeztera')
            ->and($visit->reviewed_by)->toBe($this->receptionist->id)
            ->and($attendance->visitor_id)->toBe($visitor->id)
            ->and($attendance->badge_number)->toBe('7')
            ->and($attendance->checked_in_at->toDateTimeString())->toBe('2026-11-10 10:00:00');
    });

    it('registers a new visitor and checks them in', function () {
        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), [
                'first_name' => 'Miren',
                'last_name' => 'Etxeberria',
                'document_number' => '12345678 z',
                'origin' => 'Elkar SL',
            ])
            ->assertRedirect(route('dashboard'));

        $visitor = Visitor::sole();

        expect($visitor->document_number)->toBe('12345678Z')
            ->and(VisitAttendance::active()->sole()->visitor_id)->toBe($visitor->id);
    });

    it('requires the names of a new visitor', function () {
        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), ['origin' => 'Elkar SL'])
            ->assertSessionHasErrors(['first_name', 'last_name']);

        expect(Visit::count())->toBe(0);
    });

    it('sends reception to the existing visitor when the document number is known', function () {
        Visitor::factory()->create(['document_number' => '12345678Z']);

        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), ['first_name' => 'Miren', 'last_name' => 'Etxeberria', 'document_number' => '12345678-Z'])
            ->assertSessionHasErrors(['document_number' => 'NAN zenbaki hori duen bisitaria badago jada: bilatu eta hautatu.']);

        expect(Visit::count())->toBe(0);
    });

    it('rejects a visitor who is already inside', function () {
        $visitor = Visitor::factory()->create();
        Visit::factory()->approved()->today()->create()->visitors()->attach($visitor, ['checked_in_at' => now()]);

        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), ['visitor_id' => $visitor->id])
            ->assertSessionHasErrors(['visitor_id' => 'Bisitari hau ikastetxean dago jada.']);
    });

    it('rejects a badge that another visitor inside is using', function () {
        Visit::factory()->approved()->today()->create()
            ->visitors()->attach(Visitor::factory()->create(), ['checked_in_at' => now(), 'badge_number' => '7']);

        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), ['visitor_id' => Visitor::factory()->create()->id, 'badge_number' => '7'])
            ->assertSessionHasErrors(['badge_number' => 'Txartel hori beste bisitari batek du une honetan.']);
    });

    it('allows reusing the badge of a visitor who has left', function () {
        Visit::factory()->approved()->today()->create()
            ->visitors()->attach(Visitor::factory()->create(), [
                'checked_in_at' => now()->subHour(),
                'checked_out_at' => now(),
                'badge_number' => '7',
            ]);

        $this->actingAs($this->receptionist)
            ->post(route('walk-ins.store'), ['visitor_id' => Visitor::factory()->create()->id, 'badge_number' => '7'])
            ->assertSessionHasNoErrors();
    });
});
