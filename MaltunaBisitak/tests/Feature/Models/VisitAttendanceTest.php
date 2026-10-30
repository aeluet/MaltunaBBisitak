<?php

use App\Models\Visit;
use App\Models\VisitAttendance;
use App\Models\Visitor;

it('lists only visitors who checked in and have not checked out as active', function () {
    $visit = Visit::factory()->approved()->today()->create();
    [$inside, $notArrived, $left] = Visitor::factory(3)->create();

    $visit->visitors()->attach($inside, ['checked_in_at' => now()->subHour()]);
    $visit->visitors()->attach($notArrived);
    $visit->visitors()->attach($left, ['checked_in_at' => now()->subHours(2), 'checked_out_at' => now()]);

    expect(VisitAttendance::active()->pluck('visitor_id')->all())->toBe([$inside->id]);
});

it('keeps the visitor of an attendance resolvable after the visitor is soft deleted', function () {
    $visit = Visit::factory()->create();
    $visitor = Visitor::factory()->create(['first_name' => 'Miren', 'last_name' => 'Etxeberria']);
    $visit->visitors()->attach($visitor, ['checked_in_at' => now()]);

    $visitor->delete();

    expect(VisitAttendance::first()->visitor->full_name)->toBe('Miren Etxeberria');
});
