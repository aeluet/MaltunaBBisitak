<?php

use App\Enums\VisitStatus;
use App\Models\AvailableDate;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    $this->admin = User::factory()->admin()->create();
});

it('forbids receptionists from managing the calendar', function () {
    $this->actingAs(User::factory()->harrera()->create())
        ->get(route('available-dates.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('shows the requested month with its available dates and scheduled visit counts', function () {
        $availableDate = AvailableDate::factory()->create(['date' => '2026-12-03', 'max_visits' => 4]);
        Visit::factory(2)->create(['visit_date' => '2026-12-03']);
        Visit::factory()->create(['visit_date' => '2026-12-03', 'status' => VisitStatus::Cancelled]);

        $this->actingAs($this->admin)
            ->get(route('available-dates.index', ['hilabetea' => '2026-12']))
            ->assertOk()
            ->assertSee('2026ko abendua')
            ->assertSee(route('available-dates.edit', $availableDate))
            ->assertSee('2/4');
    });

    it('falls back to the current month when the month is invalid', function () {
        $this->actingAs($this->admin)
            ->get(route('available-dates.index', ['hilabetea' => '2026-13']))
            ->assertOk()
            ->assertSee('2026ko azaroa');
    });
});

describe('store', function () {
    it('makes a future date available only once', function () {
        $this->actingAs($this->admin)
            ->post(route('available-dates.store'), ['date' => '2026-11-20'])
            ->assertRedirect(route('available-dates.index', ['hilabetea' => '2026-11']));

        $this->actingAs($this->admin)
            ->post(route('available-dates.store'), ['date' => '2026-11-20']);

        expect(AvailableDate::whereDate('date', '2026-11-20')->count())->toBe(1);
    });

    it('rejects past dates', function () {
        $this->actingAs($this->admin)
            ->post(route('available-dates.store'), ['date' => '2026-11-09'])
            ->assertSessionHasErrors(['date' => 'Ezin da iraganeko egun bat erabilgarri jarri.']);

        expect(AvailableDate::count())->toBe(0);
    });
});

describe('update', function () {
    it('sets a visit limit and notes', function () {
        $availableDate = AvailableDate::factory()->create(['date' => '2026-11-20']);

        $this->actingAs($this->admin)
            ->put(route('available-dates.update', $availableDate), ['max_visits' => 3, 'notes' => 'Goizez bakarrik'])
            ->assertRedirect(route('available-dates.index', ['hilabetea' => '2026-11']));

        expect($availableDate->refresh())
            ->max_visits->toBe(3)
            ->notes->toBe('Goizez bakarrik');
    });
});

describe('destroy', function () {
    it('removes the date from the calendar', function () {
        $availableDate = AvailableDate::factory()->create(['date' => '2026-11-20']);

        $this->actingAs($this->admin)
            ->delete(route('available-dates.destroy', $availableDate))
            ->assertRedirect(route('available-dates.index', ['hilabetea' => '2026-11']));

        $this->assertModelMissing($availableDate);
    });
});
