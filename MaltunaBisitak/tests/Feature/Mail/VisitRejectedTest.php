<?php

use App\Mail\VisitRejected;
use App\Models\Visit;

it('explains the rejection in the language of the request and links to a new request', function () {
    $visit = Visit::factory()->create(['visit_date' => '2026-11-12', 'locale' => 'en', 'response_message' => 'That day is full.']);

    $mail = (new VisitRejected($visit, 'Ane'))->locale('en');

    $mail->assertHasSubject('We cannot confirm your visit');
    $mail->assertSeeInHtml('Date: Thursday, November 12, 2026.');
    $mail->assertSeeInHtml('That day is full.');
    $mail->assertSeeInHtml(route('bookings.create', ['locale' => 'en']));
});
