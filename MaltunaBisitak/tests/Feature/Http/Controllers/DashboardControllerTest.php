<?php

use App\Models\ContractorShift;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
});

it('redirects guests to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

describe('reception panel', function () {
    beforeEach(function () {
        $this->receptionist = User::factory()->harrera()->create();
    });

    it('counts the people inside including unnamed group members', function () {
        Visit::factory()->approved()->today()->create()
            ->visitors()->attach(Visitor::factory()->create(), ['checked_in_at' => now()]);
        Visit::factory()->group(20)->approved()->today()->create()
            ->visitors()->attach(Visitor::factory()->create(), ['checked_in_at' => now()]);

        $this->actingAs($this->receptionist)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('peopleInsideCount', 21);
    });

    it('lists the visitors inside and the approved visitors expected today', function () {
        $visit = Visit::factory()->approved()->today()->create();
        $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane', 'last_name' => 'Barruan']), ['checked_in_at' => now()]);
        $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Jon', 'last_name' => 'Zain']));
        Visit::factory()->approved()->create(['visit_date' => '2026-11-11'])
            ->visitors()->attach(Visitor::factory()->create(['first_name' => 'Bihar', 'last_name' => 'Dator']));
        Visit::factory()->today()->create()
            ->visitors()->attach(Visitor::factory()->create(['first_name' => 'Onartu', 'last_name' => 'Gabe']));

        $this->actingAs($this->receptionist)
            ->get(route('dashboard'))
            ->assertSee('Ane Barruan')
            ->assertSee('Jon Zain')
            ->assertDontSee('Bihar Dator')
            ->assertDontSee('Onartu Gabe');
    });

    it('summarises the check-ins of today, this week and this month', function () {
        $visit = Visit::factory()->approved()->create();

        // 2026-11-10 is a Tuesday, so the week started on Monday the 9th.
        foreach (['2026-11-10 09:00', '2026-11-09 09:00', '2026-11-02 09:00', '2026-10-20 09:00'] as $checkedInAt) {
            $visit->visitors()->attach(Visitor::factory()->create(), ['checked_in_at' => $checkedInAt, 'checked_out_at' => $checkedInAt]);
        }

        $this->actingAs($this->receptionist)
            ->get(route('dashboard'))
            ->assertViewHas('checkInCounts', ['today' => 1, 'week' => 2, 'month' => 3]);
    });

    it('shows the pending requests and the contractors at work', function () {
        Visit::factory(3)->create();
        ContractorShift::factory()->create();

        $this->actingAs($this->receptionist)
            ->get(route('dashboard'))
            ->assertViewHas('pendingVisitsCount', 3)
            ->assertViewHas('activeContractorsCount', 1);
    });
});

describe('host panel', function () {
    it('shows teachers only their own upcoming visits', function () {
        $teacher = User::factory()->create();
        Visit::factory()->approved()->create(['host_id' => $teacher->id, 'visit_date' => '2026-11-12', 'origin' => 'Nire bisita']);
        Visit::factory()->approved()->create(['visit_date' => '2026-11-12', 'origin' => 'Besteren bisita']);
        Visit::factory()->approved()->create(['host_id' => $teacher->id, 'visit_date' => '2026-11-02', 'origin' => 'Iraganeko bisita']);

        $this->actingAs($teacher)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Nire bisita')
            ->assertDontSee('Besteren bisita')
            ->assertDontSee('Iraganeko bisita');
    });
});
