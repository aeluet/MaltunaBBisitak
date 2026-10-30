<?php

use App\Models\Contractor;
use App\Models\ContractorShift;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

beforeEach(function () {
    $this->travelTo(Carbon::parse('2026-11-10 10:00'));
    $this->admin = User::factory()->admin()->create();
});

function exportedSheet(TestResponse $response): Worksheet
{
    $path = tempnam(sys_get_temp_dir(), 'report');
    file_put_contents($path, $response->streamedContent());

    $sheet = IOFactory::load($path)->getActiveSheet();
    unlink($path);

    return $sheet;
}

function checkIn(string $lastName, string $checkedInAt, array $visitAttributes = [], array $visitorAttributes = []): Visit
{
    $visit = Visit::factory()->approved()->create($visitAttributes);
    $visit->visitors()->attach(
        Visitor::factory()->create(['last_name' => $lastName, ...$visitorAttributes]),
        ['checked_in_at' => $checkedInAt],
    );

    return $visit;
}

it('is only available to admins', function () {
    $this->actingAs(User::factory()->harrera()->create())
        ->get(route('reports.index'))
        ->assertForbidden();
});

describe('index', function () {
    it('lists this month\'s check-ins by default', function () {
        checkIn('Azaroan', '2026-11-03 09:00');
        checkIn('Urrian', '2026-10-20 09:00');

        $this->actingAs($this->admin)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Azaroan')
            ->assertDontSee('Urrian');
    });

    it('filters check-ins by origin and host', function () {
        $teacher = User::factory()->create();
        checkIn('Aukeratua', '2026-11-03 09:00', ['origin' => 'Arizmendi Ikastola', 'host_id' => $teacher->id]);
        checkIn('Beste jatorria', '2026-11-03 09:00', ['origin' => 'Elkar', 'host_id' => $teacher->id]);
        checkIn('Beste ostalaria', '2026-11-03 09:00', ['origin' => 'Arizmendi Ikastola']);

        $this->actingAs($this->admin)
            ->get(route('reports.index', ['jatorria' => 'arizmendi', 'ostalaria' => $teacher->id]))
            ->assertSee('Aukeratua')
            ->assertDontSee('Beste jatorria')
            ->assertDontSee('Beste ostalaria');
    });

    it('shows each kind of data', function (string $type, Closure $createRecord, string $expected) {
        $createRecord();

        $this->actingAs($this->admin)
            ->get(route('reports.index', ['mota' => $type, 'noiztik' => '2026-11-01', 'noiz_arte' => '2026-11-30']))
            ->assertOk()
            ->assertSee($expected);
    })->with([
        'visit requests' => ['bisitak', fn () => Visit::factory()->create(['origin' => 'Eskaera berria', 'visit_date' => '2026-11-12']), 'Eskaera berria'],
        'meeting attendees' => ['bilerak', fn () => Meeting::factory()->create(['date' => '2026-11-05'])
            ->visitors()->attach(Visitor::factory()->create(['last_name' => 'Partaidea'])), 'Partaidea'],
        'contractors' => ['kanpo-langileak', fn () => ContractorShift::factory()
            ->for(Contractor::factory()->state(['company' => 'Elektra SL']))
            ->create(['checked_in_at' => '2026-11-04 08:00']), 'Elektra SL'],
    ]);

    it('rejects an end date before the start date without looping', function () {
        $this->actingAs($this->admin)
            ->get(route('reports.index', ['noiztik' => '2026-11-10', 'noiz_arte' => '2026-11-01']))
            ->assertRedirect(route('reports.index'))
            ->assertSessionHasErrors('noiz_arte');
    });
});

describe('export', function () {
    it('downloads the filtered check-ins as an xlsx file', function () {
        checkIn('Zubiaurre', '2026-11-03 09:15', ['origin' => 'Arizmendi Ikastola'], ['first_name' => 'Ane', 'phone' => '0034611222333']);
        checkIn('Urrian', '2026-10-20 09:00');

        $response = $this->actingAs($this->admin)
            ->get(route('reports.export', ['noiztik' => '2026-11-01', 'noiz_arte' => '2026-11-10']));

        $response->assertOk()->assertDownload('bisitariak-2026-11-01-2026-11-10.xlsx');

        $sheet = exportedSheet($response);

        expect($sheet->getHighestRow())->toBe(2)
            ->and($sheet->rangeToArray('A1:G1')[0])->toBe(['Data', 'Sarrera', 'Irteera', 'Izena', 'Abizena(k)', 'NAN', 'Jatorria'])
            ->and($sheet->rangeToArray('A2:G2')[0])->toBe(['2026/11/03', '09:15', null, 'Ane', 'Zubiaurre', null, 'Arizmendi Ikastola'])
            ->and($sheet->getCell('L2')->getValue())->toBe('0034611222333');
    });

    it('stores values that look like formulas as plain text', function () {
        checkIn('Zubiaurre', '2026-11-03 09:00', [], ['first_name' => '=HYPERLINK("https://example.com","Klik")']);

        $sheet = exportedSheet($this->actingAs($this->admin)->get(route('reports.export')));

        expect($sheet->getCell('D2')->getDataType())->toBe(DataType::TYPE_STRING)
            ->and($sheet->getCell('D2')->getValue())->toBe('=HYPERLINK("https://example.com","Klik")');
    });

    it('embeds the contractors\' signatures as images', function () {
        Storage::fake('local');
        Storage::disk('local')->put('signatures/test.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));
        ContractorShift::factory()->create(['checked_in_at' => '2026-11-04 08:00', 'signature_path' => 'signatures/test.png']);

        $sheet = exportedSheet($this->actingAs($this->admin)->get(route('reports.export', ['mota' => 'kanpo-langileak'])));

        expect($sheet->getDrawingCollection())->toHaveCount(1)
            ->and($sheet->getDrawingCollection()[0]->getCoordinates())->toBe('I2')
            ->and($sheet->getCell('I2')->getValue())->toBeNull();
    });
});
