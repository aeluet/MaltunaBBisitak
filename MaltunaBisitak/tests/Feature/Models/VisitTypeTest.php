<?php

use App\Models\VisitType;

it('returns the name in the requested locale and falls back to basque', function () {
    $type = VisitType::factory()->make(['name_eu' => 'Tailerra', 'name_es' => 'Taller']);

    expect($type->nameFor('es'))->toBe('Taller')
        ->and($type->nameFor('fr'))->toBe('Tailerra');
});

it('lists only active types in sort order', function () {
    $second = VisitType::factory()->create(['sort_order' => 2]);
    $first = VisitType::factory()->create(['sort_order' => 1]);
    VisitType::factory()->inactive()->create();

    expect(VisitType::active()->pluck('id')->all())->toBe([$first->id, $second->id]);
});
