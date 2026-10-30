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

/**
 * @param  array<string, mixed>  $pivot
 */
function attendanceFor(Visit $visit, array $pivot = []): VisitAttendance
{
    $visit->visitors()->attach(Visitor::factory()->create(), $pivot);

    return $visit->attendances()->latest('id')->first();
}

it('forbids teachers from checking visitors in', function () {
    $attendance = attendanceFor(Visit::factory()->approved()->today()->create());

    $this->actingAs(User::factory()->create())
        ->post(route('attendances.check-in', $attendance))
        ->assertForbidden();
});

describe('check in', function () {
    it('checks in an expected visitor with a badge', function () {
        $attendance = attendanceFor(Visit::factory()->approved()->today()->create());

        $this->actingAs($this->receptionist)
            ->from(route('dashboard'))
            ->post(route('attendances.check-in', $attendance), ['badge_number' => '12'])
            ->assertRedirect(route('dashboard'));

        $attendance->refresh();

        expect($attendance->checked_in_at->toDateTimeString())->toBe('2026-11-10 10:00:00')
            ->and($attendance->badge_number)->toBe('12');
    });

    it('refuses visitors of a visit that is not approved', function () {
        $attendance = attendanceFor(Visit::factory()->today()->create());

        $this->actingAs($this->receptionist)
            ->post(route('attendances.check-in', $attendance))
            ->assertSessionHas('error');

        expect($attendance->refresh()->checked_in_at)->toBeNull();
    });

    it('refuses a visitor who already checked in', function () {
        $attendance = attendanceFor(Visit::factory()->approved()->today()->create(), ['checked_in_at' => '2026-11-10 09:00']);

        $this->actingAs($this->receptionist)
            ->post(route('attendances.check-in', $attendance))
            ->assertSessionHas('error');

        expect($attendance->refresh()->checked_in_at->format('H:i'))->toBe('09:00');
    });
});

describe('check out', function () {
    it('checks out the visitor and closes a visit everyone has left', function () {
        $visit = Visit::factory()->approved()->today()->create();
        $attendance = attendanceFor($visit, ['checked_in_at' => '2026-11-10 09:00']);

        $this->actingAs($this->receptionist)
            ->from(route('dashboard'))
            ->post(route('attendances.check-out', $attendance))
            ->assertRedirect(route('dashboard'));

        expect($attendance->refresh()->checked_out_at->toDateTimeString())->toBe('2026-11-10 10:00:00')
            ->and($visit->refresh()->status)->toBe(VisitStatus::Closed);
    });

    it('refuses a visitor who is not inside', function () {
        $attendance = attendanceFor(Visit::factory()->approved()->today()->create());

        $this->actingAs($this->receptionist)
            ->post(route('attendances.check-out', $attendance))
            ->assertSessionHas('error');

        expect($attendance->refresh()->checked_out_at)->toBeNull();
    });
});
