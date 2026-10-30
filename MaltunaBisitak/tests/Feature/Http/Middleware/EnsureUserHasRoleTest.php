<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:admin,harrera'])
        ->get('_test/role', fn () => 'ok');
});

it('allows users with one of the listed roles', function () {
    $this->actingAs(User::factory()->harrera()->create())
        ->get('_test/role')
        ->assertOk();
});

it('forbids users without any of the listed roles', function () {
    $this->actingAs(User::factory()->create())
        ->get('_test/role')
        ->assertForbidden();
});
