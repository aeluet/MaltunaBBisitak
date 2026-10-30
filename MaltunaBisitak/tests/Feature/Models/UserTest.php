<?php

use App\Enums\Role;
use App\Models\User;

it('reports whether the user has any of the given roles', function () {
    $receptionist = User::factory()->harrera()->create();

    expect($receptionist->hasRole(Role::Admin, Role::Harrera))->toBeTrue()
        ->and($receptionist->hasRole(Role::Admin))->toBeFalse();
});
