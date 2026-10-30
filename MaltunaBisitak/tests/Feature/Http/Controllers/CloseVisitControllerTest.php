<?php

use App\Enums\VisitStatus;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitAttendance;
use App\Models\Visitor;

beforeEach(function () {
    $this->receptionist = User::factory()->harrera()->create();
});

it('checks out the whole group and closes the visit', function () {
    $visit = Visit::factory()->group(20)->approved()->today()->create();
    $visit->visitors()->attach(Visitor::factory(2)->create(), ['checked_in_at' => now()->subHour()]);

    $this->actingAs($this->receptionist)
        ->from(route('dashboard'))
        ->post(route('visits.close', $visit))
        ->assertRedirect(route('dashboard'));

    expect($visit->refresh()->status)->toBe(VisitStatus::Closed)
        ->and(VisitAttendance::active()->count())->toBe(0);
});

it('refuses to close a visit that is not approved', function () {
    $visit = Visit::factory()->create();

    $this->actingAs($this->receptionist)
        ->post(route('visits.close', $visit))
        ->assertSessionHas('error');

    expect($visit->refresh()->status)->toBe(VisitStatus::Pending);
});

it('forbids teachers from closing visits', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('visits.close', Visit::factory()->approved()->create()))
        ->assertForbidden();
});
