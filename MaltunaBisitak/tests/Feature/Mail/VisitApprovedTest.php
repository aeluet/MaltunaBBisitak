<?php

use App\Mail\VisitApproved;
use App\Models\User;
use App\Models\Visit;

it('confirms the visit in its language with the QR code and the code as text', function (string $locale, string $subject, string $date) {
    $visit = Visit::factory()->approved()->create([
        'visit_date' => '2026-11-12',
        'locale' => $locale,
        'host_id' => User::factory()->create(['name' => 'Rodrigo Roig'])->id,
        'response_message' => 'Itxaron sarrera nagusian.',
    ]);

    $mail = (new VisitApproved($visit, 'Ane', 'K7MZ2Q8PWX'))->locale($locale);

    $mail->assertHasSubject($subject);
    $mail->assertSeeInHtml($date);
    $mail->assertSeeInHtml('Rodrigo Roig');
    $mail->assertSeeInHtml('Itxaron sarrera nagusian.');
    $mail->assertSeeInHtml('K7MZ2Q8PWX');
    $mail->assertSeeInHtml('data:image/png;base64,', escape: false);
})->with([
    'basque' => ['eu', 'Zure bisita baieztatu dugu', 'osteguna, 2026ko azaroaren 12a'],
    'spanish' => ['es', 'Hemos confirmado tu visita', 'jueves, 12 de noviembre de 2026'],
    'english' => ['en', 'Your visit is confirmed', 'Thursday, November 12, 2026'],
]);
