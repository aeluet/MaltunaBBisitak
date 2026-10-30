<?php

use App\Mail\BookingReceived;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\VisitType;

it('is written in the language of the request', function (string $locale, string $subject, string $date, string $type) {
    $visit = Visit::factory()->group(24)->create(['visit_date' => '2026-11-12', 'locale' => $locale]);
    $visit->types()->attach(VisitType::factory()->create(['name_eu' => 'Tailerra', 'name_es' => 'Taller', 'name_en' => 'Workshop']));
    $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre']));

    $mail = (new BookingReceived($visit, 'Ane'))->locale($locale);

    $mail->assertHasSubject($subject);
    $mail->assertSeeInHtml($date);
    $mail->assertSeeInHtml($type);
    $mail->assertSeeInHtml('Ane Zubiaurre');
})->with([
    'basque' => ['eu', 'Bisita eskaera jaso dugu', 'osteguna, 2026ko azaroaren 12a', 'Tailerra'],
    'spanish' => ['es', 'Hemos recibido tu solicitud de visita', 'jueves, 12 de noviembre de 2026', 'Taller'],
    'english' => ['en', 'We have received your visit request', 'Thursday, November 12, 2026', 'Workshop'],
]);
