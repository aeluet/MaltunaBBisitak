<?php

use App\Enums\VisitStatus;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

it('creates visits as pending with a public uuid while keeping the numeric id', function () {
    $visit = Visit::create(['visit_date' => today()]);

    expect($visit->status)->toBe(VisitStatus::Pending)
        ->and($visit->uuid)->toBeUuid()
        ->and($visit->getKey())->toBeInt();
});

it('removes attendances but keeps visitors when a visit is deleted', function () {
    $visit = Visit::factory()->create();
    $visitor = Visitor::factory()->create();
    $visit->visitors()->attach($visitor);

    $visit->delete();

    $this->assertDatabaseMissing('visit_visitor', ['visitor_id' => $visitor->id]);
    $this->assertModelExists($visitor);
});

it('checks out everyone still inside when the visit is closed', function () {
    $this->travelTo(Carbon::parse('2026-11-10 12:00'));
    $visit = Visit::factory()->approved()->create();
    [$inside, $left] = Visitor::factory(2)->create();
    $visit->visitors()->attach($inside, ['checked_in_at' => '2026-11-10 09:00']);
    $visit->visitors()->attach($left, ['checked_in_at' => '2026-11-10 09:00', 'checked_out_at' => '2026-11-10 10:00']);

    $visit->close();

    expect($visit->refresh()->status)->toBe(VisitStatus::Closed)
        ->and($visit->closed_at->toDateTimeString())->toBe('2026-11-10 12:00:00')
        ->and($visit->attendances()->whereBelongsTo($inside)->value('checked_out_at')->toDateTimeString())->toBe('2026-11-10 12:00:00')
        ->and($visit->attendances()->whereBelongsTo($left)->value('checked_out_at')->toDateTimeString())->toBe('2026-11-10 10:00:00');
});
