<?php

use App\Enums\VisitStatus;
use App\Models\Visit;
use App\Models\Visitor;

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
