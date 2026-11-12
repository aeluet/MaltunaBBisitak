<?php

use App\Mail\HostVisitApproved;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

beforeEach(function () {
    $this->visit = Visit::factory()->group(20)->approved()->create([
        'visit_date' => '2026-11-12',
        'origin' => 'Arizmendi Ikastola',
        'host_id' => User::factory()->create(['name' => 'Rodrigo Roig'])->id,
        'response_message' => 'Itxaron sarrera nagusian.',
    ]);
    $this->visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre']));
});

it('tells the teacher in Basque who is coming and when', function () {
    $mail = new HostVisitApproved($this->visit);

    $mail->assertHasSubject('Bisita bat duzu: Arizmendi Ikastola (2026/11/12)');
    $mail->assertSeeInHtml('Rodrigo Roig');
    $mail->assertSeeInHtml('osteguna, 2026ko azaroaren 12a');
    $mail->assertSeeInHtml('Taldea (20 pertsona)');
    $mail->assertSeeInHtml('Ane Zubiaurre');
    $mail->assertSeeInHtml('Itxaron sarrera nagusian.');
    $mail->assertSeeInHtml('Gehitu Google Calendar-era');
});

it('links to a whole-day Google Calendar event for the visit', function () {
    $url = (new HostVisitApproved($this->visit))->googleCalendarUrl();

    parse_str(parse_url($url, PHP_URL_QUERY), $query);

    expect($url)->toStartWith('https://calendar.google.com/calendar/render?')
        ->and($query['action'])->toBe('TEMPLATE')
        ->and($query['text'])->toBe('Bisita: Arizmendi Ikastola')
        ->and($query['dates'])->toBe('20261112/20261113')
        ->and($query['ctz'])->toBe('Europe/Madrid')
        ->and($query['details'])->toContain('Ane Zubiaurre');
});
