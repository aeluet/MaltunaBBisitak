<?php

use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;

beforeEach(function () {
    $this->receptionist = User::factory()->harrera()->create();
});

it('forbids teachers from the visitors area', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('visitors.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('filters visitors by name, document number or origin', function () {
        Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Zubiaurre', 'origin' => 'Arizmendi']);
        Visitor::factory()->create(['first_name' => 'Peio', 'last_name' => 'Larrañaga', 'document_number' => '12345678Z']);

        $this->actingAs($this->receptionist)
            ->get(route('visitors.index', ['q' => 'arizmendi']))
            ->assertOk()
            ->assertSee('Zubiaurre')
            ->assertDontSee('Larrañaga');

        $this->actingAs($this->receptionist)
            ->get(route('visitors.index', ['q' => '12345678']))
            ->assertSee('Larrañaga')
            ->assertDontSee('Zubiaurre');
    });
});

describe('store', function () {
    it('creates a visitor with a normalised document number', function () {
        $response = $this->actingAs($this->receptionist)
            ->post(route('visitors.store'), [
                'first_name' => 'Miren',
                'last_name' => 'Etxeberria',
                'document_number' => '12345678 z',
                'country' => 'ES',
            ]);

        $visitor = Visitor::sole();

        $response->assertRedirect(route('visitors.show', $visitor));
        expect($visitor->document_number)->toBe('12345678Z');
    });

    it('rejects a document number that already belongs to another visitor', function () {
        Visitor::factory()->create(['document_number' => '12345678Z']);

        $this->actingAs($this->receptionist)
            ->post(route('visitors.store'), [
                'first_name' => 'Bikoiztua',
                'last_name' => 'Bat',
                'document_number' => '12345678-z',
            ])
            ->assertSessionHasErrors(['document_number' => 'NAN zenbaki hori duen bisitaria badago jada.']);
    });

    it('rejects an unknown country code', function () {
        $this->actingAs($this->receptionist)
            ->post(route('visitors.store'), [
                'first_name' => 'Ane',
                'last_name' => 'Zubiaurre',
                'country' => 'XX',
            ])
            ->assertSessionHasErrors('country');
    });
});

describe('show', function () {
    it('shows the visitor with their visit history', function () {
        $visitor = Visitor::factory()->create(['country' => 'FR']);
        $visit = Visit::factory()->approved()->create(['visit_date' => '2026-05-14']);
        $visit->visitors()->attach($visitor, ['checked_in_at' => '2026-05-14 09:30:00']);

        $this->actingAs($this->receptionist)
            ->get(route('visitors.show', $visitor))
            ->assertOk()
            ->assertSee('Frantzia')
            ->assertSee('2026/05/14')
            ->assertSee('09:30');
    });
});

describe('update', function () {
    it('keeps the visitor own document number valid on update', function () {
        $visitor = Visitor::factory()->create(['document_number' => '12345678Z']);

        $this->actingAs($this->receptionist)
            ->put(route('visitors.update', $visitor), [
                'first_name' => 'Izen',
                'last_name' => 'Berria',
                'document_number' => '12345678Z',
            ])
            ->assertRedirect(route('visitors.show', $visitor));

        expect($visitor->refresh()->first_name)->toBe('Izen');
    });
});

describe('destroy', function () {
    it('soft deletes the visitor when requested by an admin', function () {
        $visitor = Visitor::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('visitors.destroy', $visitor))
            ->assertRedirect(route('visitors.index'));

        $this->assertSoftDeleted($visitor);
    });

    it('forbids receptionists from deleting visitors', function () {
        $visitor = Visitor::factory()->create();

        $this->actingAs($this->receptionist)
            ->delete(route('visitors.destroy', $visitor))
            ->assertForbidden();

        $this->assertNotSoftDeleted($visitor);
    });
});
