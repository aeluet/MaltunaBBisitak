<?php

use App\Mail\VisitCancelled;
use App\Models\Visit;

it('announces the cancellation in the language of the request', function () {
    $visit = Visit::factory()->create(['visit_date' => '2026-11-12', 'locale' => 'es']);

    $mail = (new VisitCancelled($visit, 'Ane'))->locale('es');

    $mail->assertHasSubject('Tu visita ha sido cancelada');
    $mail->assertSeeInHtml('Fecha: jueves, 12 de noviembre de 2026.');
    $mail->assertSeeInHtml(route('bookings.create', ['locale' => 'es']));
});
