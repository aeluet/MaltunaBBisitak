<?php

use App\Http\Controllers\KioskLabelController;
use App\Models\Visitor;

it('downloads the label as plain ascii json for the print script', function () {
    $visitor = Visitor::factory()->create(['first_name' => 'Iñaki', 'last_name' => 'Arrieta<b>']);

    $response = $this->get(KioskLabelController::urlFor($visitor, 'Elkar & Co'));

    $response->assertOk()->assertDownload();
    $label = $response->streamedContent();

    expect(mb_check_encoding($label, 'ASCII'))->toBeTrue()
        ->and(json_decode($label, true))->toBe(['Izena' => 'Iñaki', 'Abizena' => 'Arrieta', 'Nondik' => 'Elkar & Co']);
});

it('refuses links without a valid signature', function () {
    $visitor = Visitor::factory()->create();

    $this->get(route('kiosk.label', ['visitor' => $visitor, 'nondik' => 'Elkar']))->assertForbidden();
});

it('expires label links after ten minutes', function () {
    $url = KioskLabelController::urlFor(Visitor::factory()->create(), 'Elkar');

    $this->travel(11)->minutes();

    $this->get($url)->assertForbidden();
});
