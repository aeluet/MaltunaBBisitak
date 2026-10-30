<?php

use App\Models\VisitType;
use Database\Seeders\VisitTypeSeeder;

it('does not duplicate visit types when run twice', function () {
    $this->seed(VisitTypeSeeder::class);
    $this->seed(VisitTypeSeeder::class);

    expect(VisitType::count())->toBe(10);
});
