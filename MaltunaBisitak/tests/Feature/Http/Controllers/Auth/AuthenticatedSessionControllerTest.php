<?php

use App\Models\User;

describe('create', function () {
    it('renders the login form', function () {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Saioa hasi');
    });

    it('redirects authenticated users to the dashboard', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    });
});

describe('store', function () {
    it('logs in an active user and redirects to the dashboard', function () {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    });

    it('rejects a wrong password with a basque message', function () {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'okerra'])
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        $this->assertGuest();
    });

    it('rejects a deactivated user with valid credentials', function () {
        $user = User::factory()->inactive()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('locks out the email after five failed attempts', function () {
        $user = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'okerra']);
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('requires email and password', function () {
        $this->post(route('login'), [])
            ->assertSessionHasErrors(['email', 'password']);
    });
});

describe('destroy', function () {
    it('logs the user out and redirects to the login page', function () {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    });
});
