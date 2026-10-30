<?php

use App\Mail\NewBookingRequest;
use App\Models\Visit;
use App\Models\Visitor;

it('summarises the request for the staff with a link to the panel', function () {
    $visit = Visit::factory()->group(24)->create(['visit_date' => '2026-11-12', 'origin' => 'Arizmendi Ikastola', 'locale' => 'es']);
    $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'email' => 'ane@example.com']));

    $mail = (new NewBookingRequest($visit))->locale('eu');

    $mail->assertHasSubject('Bisita eskaera berria: Arizmendi Ikastola (2026/11/12)');
    $mail->assertSeeInHtml('Taldea (24 pertsona)');
    $mail->assertSeeInHtml('Ane Zubiaurre (ane@example.com)');
    $mail->assertSeeInHtml(route('dashboard'));
});
