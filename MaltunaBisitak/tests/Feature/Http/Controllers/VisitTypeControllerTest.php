<?php

use App\Models\User;
use App\Models\VisitType;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('forbids receptionists from managing visit types', function () {
    $this->actingAs(User::factory()->harrera()->create())
        ->get(route('visit-types.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('lists visit types in sort order', function () {
        VisitType::factory()->create(['name_eu' => 'Bigarrena', 'sort_order' => 2]);
        VisitType::factory()->create(['name_eu' => 'Lehena', 'sort_order' => 1]);

        $this->actingAs($this->admin)
            ->get(route('visit-types.index'))
            ->assertOk()
            ->assertSeeInOrder(['Lehena', 'Bigarrena']);
    });
});

describe('store', function () {
    it('creates a visit type in the three languages', function () {
        $this->actingAs($this->admin)
            ->post(route('visit-types.store'), [
                'name_eu' => 'Robotika',
                'name_es' => 'Robótica',
                'name_en' => 'Robotics',
                'sort_order' => 11,
                'is_active' => '1',
            ])
            ->assertRedirect(route('visit-types.index'));

        $this->assertDatabaseHas('visit_types', ['name_eu' => 'Robotika', 'name_en' => 'Robotics', 'is_active' => true]);
    });

    it('requires every translation', function () {
        $this->actingAs($this->admin)
            ->post(route('visit-types.store'), ['name_eu' => 'Robotika', 'sort_order' => 1])
            ->assertSessionHasErrors(['name_es', 'name_en']);
    });
});

describe('update', function () {
    it('hides a visit type from the public form', function () {
        $visitType = VisitType::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('visit-types.update', $visitType), [
                ...$visitType->only('name_eu', 'name_es', 'name_en', 'sort_order'),
                'is_active' => '0',
            ])
            ->assertRedirect(route('visit-types.index'));

        expect($visitType->refresh()->is_active)->toBeFalse();
    });
});
