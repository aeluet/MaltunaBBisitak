<?php

use App\Models\ContractorShift;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

it('redirects guests to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('shows the counts of visitors inside, pending requests and working contractors', function () {
    $visit = Visit::factory()->approved()->today()->create();
    $visit->visitors()->attach(Visitor::factory(2)->create(), ['checked_in_at' => now()]);
    Visit::factory(3)->create();
    ContractorShift::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('activeVisitorsCount', 2)
        ->assertViewHas('pendingVisitsCount', 3)
        ->assertViewHas('activeContractorsCount', 1);
});
