<?php

use App\Models\Contractor;
use App\Models\ContractorShift;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    $this->receptionist = User::factory()->harrera()->create();

    $onePixelPng = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    $this->signaturePng = base64_decode($onePixelPng);
    $this->checkInPayload = [
        'first_name' => 'Iñaki',
        'last_name' => 'Arrieta',
        'phone' => '611222333',
        'company' => 'Elektra SL',
        'checked_in_at' => '2026-11-10T08:15',
        'needs_key' => '1',
        'signature' => 'data:image/png;base64,'.$onePixelPng,
    ];
});

it('forbids teachers from the contractors area', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('contractor-shifts.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('separates contractors at work from finished shifts', function () {
        $working = ContractorShift::factory()->create();
        $finished = ContractorShift::factory()->checkedOut()->create();

        $this->actingAs($this->receptionist)
            ->get(route('contractor-shifts.index'))
            ->assertOk()
            ->assertViewHas('activeShifts', fn ($shifts) => $shifts->modelKeys() === [$working->id])
            ->assertViewHas('finishedShifts', fn ($shifts) => $shifts->getCollection()->modelKeys() === [$finished->id]);
    });
});

describe('create', function () {
    it('prefills the form with a returning contractor', function () {
        $contractor = Contractor::factory()->create(['first_name' => 'Iñaki', 'phone' => '611222333']);

        $this->actingAs($this->receptionist)
            ->get(route('contractor-shifts.create', ['langilea' => $contractor->id]))
            ->assertOk()
            ->assertSee('value="Iñaki"', escape: false)
            ->assertSee('value="611222333"', escape: false);
    });
});

describe('store', function () {
    it('registers the check-in of a new contractor with the signature', function () {
        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.store'), $this->checkInPayload)
            ->assertRedirect(route('contractor-shifts.index'));

        $shift = ContractorShift::with('contractor')->sole();

        expect($shift->contractor->full_name)->toBe('Iñaki Arrieta')
            ->and($shift->checked_in_at->format('Y-m-d H:i'))->toBe('2026-11-10 08:15')
            ->and($shift->needs_key)->toBeTrue()
            ->and($shift->registered_by)->toBe($this->receptionist->id)
            ->and(Storage::disk('local')->get($shift->signature_path))->toBe($this->signaturePng);
    });

    it('reuses a returning contractor and updates their company', function () {
        $contractor = Contractor::factory()->create([
            'first_name' => 'Iñaki',
            'last_name' => 'Arrieta',
            'phone' => '611222333',
            'company' => 'Zaharra SL',
        ]);

        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.store'), $this->checkInPayload);

        expect(Contractor::count())->toBe(1)
            ->and($contractor->refresh()->company)->toBe('Elektra SL')
            ->and($contractor->shifts()->count())->toBe(1);
    });

    it('rejects invalid input without storing anything', function (array $override, string $field) {
        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.store'), [...$this->checkInPayload, ...$override])
            ->assertSessionHasErrors($field);

        expect(ContractorShift::count())->toBe(0)
            ->and(Storage::disk('local')->allFiles())->toBeEmpty();
    })->with([
        'a signature that is not a png' => [['signature' => 'data:image/png;base64,'.base64_encode('<svg/>')], 'signature'],
        'a missing signature' => [['signature' => ''], 'signature'],
        'a check-in time in the future' => [['checked_in_at' => '2026-11-10T11:00'], 'checked_in_at'],
    ]);
});

describe('check out', function () {
    it('records the exit time and whether the key was returned', function () {
        $shift = ContractorShift::factory()->withKey()->create(['checked_in_at' => '2026-11-10 08:00']);

        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.check-out', $shift), ['key_returned' => '1'])
            ->assertRedirect(route('contractor-shifts.index'));

        $shift->refresh();

        expect($shift->checked_out_at->toDateTimeString())->toBe('2026-11-10 10:00:00')
            ->and($shift->key_returned)->toBeTrue();
    });

    it('records an unreturned key', function () {
        $shift = ContractorShift::factory()->withKey()->create();

        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.check-out', $shift));

        expect($shift->refresh()->key_returned)->toBeFalse();
    });

    it('ignores the key answer when no key was handed out', function () {
        $shift = ContractorShift::factory()->create();

        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.check-out', $shift), ['key_returned' => '1']);

        expect($shift->refresh()->key_returned)->toBeNull();
    });

    it('does not change a shift that already ended', function () {
        $shift = ContractorShift::factory()->checkedOut()->create(['checked_out_at' => '2026-11-10 09:00']);

        $this->actingAs($this->receptionist)
            ->post(route('contractor-shifts.check-out', $shift))
            ->assertSessionHas('error');

        expect($shift->refresh()->checked_out_at->format('H:i'))->toBe('09:00');
    });
});

describe('signature', function () {
    it('streams the stored signature image', function () {
        Storage::disk('local')->put('signatures/test.png', $this->signaturePng);
        $shift = ContractorShift::factory()->create(['signature_path' => 'signatures/test.png']);

        $this->actingAs($this->receptionist)
            ->get(route('contractor-shifts.signature', $shift))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    });

    it('returns 404 when the shift has no signature', function () {
        $shift = ContractorShift::factory()->create();

        $this->actingAs($this->receptionist)
            ->get(route('contractor-shifts.signature', $shift))
            ->assertNotFound();
    });
});
