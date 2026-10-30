<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('forbids non-admin users', function (Role $role) {
    $this->actingAs(User::factory()->create(['role' => $role]))
        ->get(route('users.index'))
        ->assertForbidden();
})->with([Role::Harrera, Role::Irakaslea]);

describe('index', function () {
    it('lists users with their role', function () {
        User::factory()->harrera()->create(['name' => 'Itziar Harrera']);

        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Itziar Harrera')
            ->assertSee('Harrera');
    });
});

describe('store', function () {
    it('creates a user with a hashed password', function () {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Jon Irakaslea',
                'email' => 'jon@example.com',
                'role' => 'irakaslea',
                'password' => 'pasahitz-luzea',
                'password_confirmation' => 'pasahitz-luzea',
                'is_active' => '1',
            ])
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'jon@example.com')->sole();

        expect($user->role)->toBe(Role::Irakaslea)
            ->and(Hash::check('pasahitz-luzea', $user->password))->toBeTrue();
    });

    it('rejects a duplicate email, unknown role and short password', function () {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Bikoiztua',
                'email' => $this->admin->email,
                'role' => 'zuzendaria',
                'password' => 'laburra',
                'password_confirmation' => 'laburra',
            ])
            ->assertSessionHasErrors(['email', 'role', 'password']);
    });
});

describe('update', function () {
    it('keeps the current password when the field is left empty', function () {
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name' => 'Izen Berria',
                'email' => $user->email,
                'role' => 'harrera',
                'password' => '',
                'password_confirmation' => '',
                'is_active' => '0',
            ])
            ->assertRedirect(route('users.index'));

        $user->refresh();

        expect($user->name)->toBe('Izen Berria')
            ->and($user->role)->toBe(Role::Harrera)
            ->and($user->is_active)->toBeFalse()
            ->and($user->password)->toBe($originalHash);
    });

    it('prevents admins from demoting or deactivating themselves', function () {
        $this->actingAs($this->admin)
            ->put(route('users.update', $this->admin), [
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'role' => 'harrera',
                'is_active' => '0',
            ])
            ->assertSessionHasErrors(['role', 'is_active']);

        expect($this->admin->refresh()->role)->toBe(Role::Admin);
    });
});
