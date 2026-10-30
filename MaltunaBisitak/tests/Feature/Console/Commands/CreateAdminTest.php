<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an admin account with a hashed password', function () {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Izena', 'Ainhoa')
        ->expectsQuestion('Posta elektronikoa', 'ainhoa@example.com')
        ->expectsQuestion('Pasahitza', 'pasahitz-luzea')
        ->assertSuccessful();

    $admin = User::where('email', 'ainhoa@example.com')->sole();

    expect($admin->role)->toBe(Role::Admin)
        ->and(Hash::check('pasahitz-luzea', $admin->password))->toBeTrue();
});
