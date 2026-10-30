<?php

use App\Enums\VisitStatus;
use App\Mail\VisitApproved;
use App\Mail\VisitCancelled;
use App\Mail\VisitRejected;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    Mail::fake();
    $this->receptionist = User::factory()->harrera()->create();
});

/**
 * A pending Spanish request with one visitor who has an email and one who does not.
 */
function pendingVisitWithVisitors(): Visit
{
    $visit = Visit::factory()->create(['visit_date' => '2026-11-12', 'locale' => 'es', 'origin' => 'Arizmendi Ikastola']);
    $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Ane', 'email' => 'ane@example.com']));
    $visit->visitors()->attach(Visitor::factory()->create(['first_name' => 'Jon', 'email' => null]));

    return $visit;
}

it('forbids teachers from reviewing visits', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('visits.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('lists pending requests by default and upcoming approved visits on request', function () {
        Visit::factory()->create(['origin' => 'Zain dagoena', 'visit_date' => '2026-11-12']);
        Visit::factory()->approved()->create(['origin' => 'Onartutakoa', 'visit_date' => '2026-11-13']);
        Visit::factory()->approved()->create(['origin' => 'Iraganekoa', 'visit_date' => '2026-11-02']);

        $this->actingAs($this->receptionist)
            ->get(route('visits.index'))
            ->assertOk()
            ->assertSee('Zain dagoena')
            ->assertDontSee('Onartutakoa');

        $this->actingAs($this->receptionist)
            ->get(route('visits.index', ['egoera' => 'onartuak']))
            ->assertSee('Onartutakoa')
            ->assertDontSee('Zain dagoena')
            ->assertDontSee('Iraganekoa');
    });
});

describe('show', function () {
    it('shows the request with its visitors and their check-in codes', function () {
        $visit = pendingVisitWithVisitors();

        $this->actingAs($this->receptionist)
            ->get(route('visits.show', $visit))
            ->assertOk()
            ->assertSee('Arizmendi Ikastola')
            ->assertSee('ane@example.com')
            ->assertSee($visit->attendances()->first()->code)
            ->assertSee('Onartu eta emaila bidali');
    });
});

describe('show for approved visits', function () {
    it('offers the QR link where group members can register from their phone', function () {
        $visit = pendingVisitWithVisitors();
        $visit->approve($this->receptionist);

        $this->actingAs($this->receptionist)
            ->get(route('visits.show', $visit))
            ->assertSee(route('registrations.visits.create', ['locale' => 'es', 'visit' => $visit]));
    });
});

describe('approve', function () {
    it('approves the request and emails each visitor with an email their own QR code', function () {
        $visit = pendingVisitWithVisitors();
        $teacher = User::factory()->create();

        $this->actingAs($this->receptionist)
            ->post(route('visits.approve', $visit), ['host_id' => $teacher->id, 'response_message' => 'Itxaron sarrera nagusian.'])
            ->assertRedirect(route('visits.show', $visit));

        $visit->refresh();
        $aneCode = $visit->visitors->firstWhere('first_name', 'Ane')->attendance->code;

        expect($visit->status)->toBe(VisitStatus::Approved)
            ->and($visit->host_id)->toBe($teacher->id)
            ->and($visit->reviewed_by)->toBe($this->receptionist->id)
            ->and($visit->response_message)->toBe('Itxaron sarrera nagusian.');

        Mail::assertSentCount(1);
        Mail::assertSent(VisitApproved::class, fn (VisitApproved $mail) => $mail->hasTo('ane@example.com')
            && $mail->locale === 'es'
            && $mail->code === $aneCode);
    });

    it('refuses a visit that is no longer pending', function () {
        $visit = Visit::factory()->approved()->create();

        $this->actingAs($this->receptionist)
            ->post(route('visits.approve', $visit))
            ->assertSessionHas('error');

        Mail::assertNothingSent();
    });
});

describe('reject', function () {
    it('rejects the request and tells the visitors why', function () {
        $visit = pendingVisitWithVisitors();

        $this->actingAs($this->receptionist)
            ->post(route('visits.reject', $visit), ['response_message' => 'Egun hori beteta dago.'])
            ->assertRedirect(route('visits.show', $visit));

        expect($visit->refresh()->status)->toBe(VisitStatus::Rejected)
            ->and($visit->response_message)->toBe('Egun hori beteta dago.');

        Mail::assertSent(VisitRejected::class, fn (VisitRejected $mail) => $mail->hasTo('ane@example.com') && $mail->locale === 'es');
    });
});

describe('cancel', function () {
    it('cancels an approved visit and tells the visitors', function () {
        $visit = pendingVisitWithVisitors();
        $visit->approve($this->receptionist);

        $this->actingAs($this->receptionist)
            ->post(route('visits.cancel', $visit))
            ->assertRedirect(route('visits.show', $visit));

        expect($visit->refresh()->status)->toBe(VisitStatus::Cancelled);
        Mail::assertSent(VisitCancelled::class, fn (VisitCancelled $mail) => $mail->hasTo('ane@example.com'));
    });

    it('refuses to cancel a visit while its visitors are inside', function () {
        $visit = Visit::factory()->approved()->today()->create();
        $visit->visitors()->attach(Visitor::factory()->create(), ['checked_in_at' => now()]);

        $this->actingAs($this->receptionist)
            ->post(route('visits.cancel', $visit))
            ->assertSessionHas('error');

        expect($visit->refresh()->status)->toBe(VisitStatus::Approved);
        Mail::assertNothingSent();
    });
});
