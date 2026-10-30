<?php

use App\Models\User;

it('logs out a user deactivated during an open session', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->update(['is_active' => false]);

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});
