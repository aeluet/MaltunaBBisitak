<?php

use App\Models\Visitor;

it('finds visitors when every searched word matches some field', function () {
    $ane = Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Arizmendi']);
    Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Larrañaga', 'origin' => 'Elkar']);

    expect(Visitor::search('ane zubi')->pluck('id')->all())->toBe([$ane->id])
        ->and(Visitor::search('  arizmendi ')->pluck('id')->all())->toBe([$ane->id]);
});

it('normalises document numbers', function (string $input) {
    expect(Visitor::normalizeDocumentNumber($input))->toBe('12345678Z');
})->with(['12345678Z', '12345678 z', '12345678-z', ' 1234 5678 Z ']);

it('reuses a visitor only when the email and both names match', function () {
    $ane = Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'email' => 'ane@example.com', 'origin' => 'Lehengoa']);

    $same = Visitor::matchOrCreate(['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'email' => 'ane@example.com', 'origin' => 'Berria']);
    $sharedEmail = Visitor::matchOrCreate(['first_name' => 'Jon', 'last_name' => 'Zubiaurre', 'email' => 'ane@example.com']);

    expect($same->is($ane))->toBeTrue()
        ->and($ane->refresh()->origin)->toBe('Lehengoa')
        ->and($sharedEmail->is($ane))->toBeFalse();
});
