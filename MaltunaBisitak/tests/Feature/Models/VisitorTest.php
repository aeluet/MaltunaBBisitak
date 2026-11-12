<?php

use App\Models\Visitor;

it('finds visitors when every searched word matches some field', function () {
    $ane = Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Arizmendi']);
    Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Larrañaga', 'origin' => 'Elkar']);

    expect(Visitor::search('ane zubi')->pluck('id')->all())->toBe([$ane->id])
        ->and(Visitor::search('  arizmendi ')->pluck('id')->all())->toBe([$ane->id]);
});

it('offers Euskal Herria first among the countries in every language', function (string $locale) {
    app()->setLocale($locale);

    expect(array_key_first(Visitor::countryOptions()))->toBe(Visitor::BASQUE_COUNTRY)
        ->and(Visitor::countryOptions()[Visitor::BASQUE_COUNTRY])->toBe('Euskal Herria')
        ->and(Visitor::countryOptions())->toHaveKey('FR');
})->with(['eu', 'es', 'en']);

it('names Euskal Herria and the ISO countries', function () {
    expect((new Visitor(['country' => Visitor::BASQUE_COUNTRY]))->country_name)->toBe('Euskal Herria')
        ->and((new Visitor(['country' => 'FR']))->country_name)->toBe('Frantzia')
        ->and((new Visitor)->country_name)->toBeNull();
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
