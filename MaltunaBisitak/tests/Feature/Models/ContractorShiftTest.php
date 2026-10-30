<?php

use App\Models\ContractorShift;

it('lists only shifts without a check-out as active', function () {
    $working = ContractorShift::factory()->create();
    ContractorShift::factory()->checkedOut()->create();

    expect(ContractorShift::active()->pluck('id')->all())->toBe([$working->id]);
});
